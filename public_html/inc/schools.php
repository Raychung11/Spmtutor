<?php
/**
 * School / learning-centre helpers: ownership, membership, analytics.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/notifications.php';
require_once __DIR__ . '/mailer.php';

/** The school owned by a school_admin user, or null. */
function current_school(int $ownerUserId): ?array
{
    return db_one('SELECT * FROM schools WHERE owner_user_id = ? ORDER BY id LIMIT 1', [$ownerUserId]);
}

function school_approved(?array $school): bool
{
    return $school !== null && $school['status'] === 'active';
}

/** Notify every admin that a new school is awaiting approval. */
function notify_admins_new_school(string $schoolName): void
{
    foreach (db_all("SELECT id FROM users WHERE role = 'admin' AND status = 'active'") as $a) {
        notify((int) $a['id'], 'New school awaiting approval', $schoolName . ' has registered and needs approval.', 'school');
    }
}

/**
 * Add a teacher or student to a school by email.
 * @return array [bool ok, string message]
 */
function add_school_member(int $schoolId, string $email, string $role): array
{
    if (!in_array($role, ['teacher', 'student'], true)) {
        return [false, 'Invalid member role.'];
    }
    $user = db_one('SELECT id, name FROM users WHERE email = ? AND role = ?', [$email, $role]);
    if (!$user) {
        return [false, "No active {$role} account found with that email. They must register first."];
    }
    try {
        db_exec(
            'INSERT INTO school_members (school_id, user_id, member_role, status) VALUES (?,?,?,?)',
            [$schoolId, (int) $user['id'], $role, 'active']
        );
        notify((int) $user['id'], 'Added to a school', 'You have been added to a school on ' . APP_NAME . '.', 'school');
        return [true, $user['name'] . ' added.'];
    } catch (Throwable $e) {
        return [false, 'That person is already a member.'];
    }
}

function remove_school_member(int $schoolId, int $memberId): void
{
    db_exec('DELETE FROM school_members WHERE id = ? AND school_id = ?', [$memberId, $schoolId]);
}

/**
 * Add the person if they already have a matching account, otherwise email an
 * invitation so they can register straight into the school.
 *
 * @return array [bool ok, string message]
 */
function invite_or_add_member(int $schoolId, string $email, string $role, int $invitedBy): array
{
    $email = strtolower(trim($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [false, 'Please enter a valid email address.'];
    }
    if (!in_array($role, ['teacher', 'student'], true)) {
        return [false, 'Invalid member role.'];
    }

    $user = db_one('SELECT id, role FROM users WHERE email = ?', [$email]);
    if ($user) {
        if ($user['role'] !== $role) {
            return [false, "That email belongs to a {$user['role']} account, so they can't join as a {$role}."];
        }
        return add_school_member($schoolId, $email, $role); // existing account → add now
    }

    // No account yet → create / refresh an invitation and email it.
    create_invitation($schoolId, $email, $role, $invitedBy);
    return [true, "Invitation emailed to {$email}."];
}

function create_invitation(int $schoolId, string $email, string $role, int $invitedBy): string
{
    db_exec('DELETE FROM school_invitations WHERE school_id = ? AND email = ? AND status = "pending"', [$schoolId, $email]);
    $token = bin2hex(random_bytes(32));
    db_exec(
        'INSERT INTO school_invitations (school_id, email, member_role, token, invited_by, expires_at)
         VALUES (?,?,?,?,?, DATE_ADD(NOW(), INTERVAL 14 DAY))',
        [$schoolId, $email, $role, $token, $invitedBy]
    );
    send_invitation_email($schoolId, $email, $role, $token);
    return $token;
}

function send_invitation_email(int $schoolId, string $email, string $role, string $token): void
{
    $school = db_one('SELECT name FROM schools WHERE id = ?', [$schoolId]);
    $link   = (APP_URL ?: '') . url('accept-invite.php?token=' . $token);
    send_email(
        $email,
        'You are invited to join ' . ($school['name'] ?? 'a school') . ' on ' . APP_NAME,
        email_template('School invitation',
            '<p>You have been invited to join <strong>' . htmlspecialchars($school['name'] ?? '', ENT_QUOTES)
            . '</strong> as a ' . htmlspecialchars($role, ENT_QUOTES) . ' on ' . htmlspecialchars(APP_NAME, ENT_QUOTES) . '.</p>'
            . '<p>Click below to create your account and join (valid for 14 days):</p>'
            . '<p><a href="' . htmlspecialchars($link, ENT_QUOTES) . '">' . htmlspecialchars($link, ENT_QUOTES) . '</a></p>')
    );
}

function pending_invitations(int $schoolId): array
{
    return db_all(
        'SELECT * FROM school_invitations WHERE school_id = ? AND status = "pending" ORDER BY id DESC',
        [$schoolId]
    );
}

function revoke_invitation(int $schoolId, int $inviteId): void
{
    db_exec('UPDATE school_invitations SET status = "revoked" WHERE id = ? AND school_id = ?', [$inviteId, $schoolId]);
}

function resend_invitation(int $schoolId, int $inviteId): bool
{
    $inv = db_one('SELECT * FROM school_invitations WHERE id = ? AND school_id = ? AND status = "pending"', [$inviteId, $schoolId]);
    if (!$inv) {
        return false;
    }
    db_exec('UPDATE school_invitations SET expires_at = DATE_ADD(NOW(), INTERVAL 14 DAY) WHERE id = ?', [$inviteId]);
    send_invitation_email($schoolId, $inv['email'], $inv['member_role'], $inv['token']);
    return true;
}

/** Return a valid, unexpired, pending invitation for a token (with school name). */
function valid_invitation(string $token): ?array
{
    if ($token === '') {
        return null;
    }
    return db_one(
        'SELECT i.*, s.name AS school_name, s.status AS school_status
         FROM school_invitations i JOIN schools s ON s.id = i.school_id
         WHERE i.token = ? AND i.status = "pending" AND (i.expires_at IS NULL OR i.expires_at > NOW())',
        [$token]
    );
}

/** Mark an invitation accepted and add the user to the school. */
function accept_invitation(array $invite, int $userId): void
{
    try {
        db_exec(
            'INSERT INTO school_members (school_id, user_id, member_role, status) VALUES (?,?,?,?)',
            [(int) $invite['school_id'], $userId, $invite['member_role'], 'active']
        );
    } catch (Throwable $e) {
        // Already a member — ignore.
    }
    db_exec('UPDATE school_invitations SET status = "accepted", accepted_at = NOW() WHERE id = ?', [(int) $invite['id']]);
}

function school_members(int $schoolId, string $role): array
{
    if ($role === 'student') {
        return db_all(
            'SELECT m.id AS member_id, u.id, u.name, u.email,
                    COALESCE(ps.total_questions,0) answered, COALESCE(ps.avg_score,0) avg_score,
                    COALESCE(st.current_streak,0) streak
             FROM school_members m JOIN users u ON u.id = m.user_id
             LEFT JOIN student_progress_summary ps ON ps.user_id = u.id
             LEFT JOIN student_streaks st ON st.user_id = u.id
             WHERE m.school_id = ? AND m.member_role = "student" AND m.status = "active" ORDER BY u.name',
            [$schoolId]
        );
    }
    return db_all(
        'SELECT m.id AS member_id, u.id, u.name, u.email,
                (SELECT COUNT(*) FROM teacher_classes c WHERE c.teacher_user_id = u.id) AS class_count
         FROM school_members m JOIN users u ON u.id = m.user_id
         WHERE m.school_id = ? AND m.member_role = "teacher" AND m.status = "active" ORDER BY u.name',
        [$schoolId]
    );
}

function school_member_count(int $schoolId, string $role): int
{
    return (int) (db_one(
        'SELECT COUNT(*) c FROM school_members WHERE school_id = ? AND member_role = ? AND status = "active"',
        [$schoolId, $role]
    )['c'] ?? 0);
}

/** All schools a user belongs to (active first), with status. */
function user_schools(int $userId, string $role): array
{
    return db_all(
        'SELECT m.status, m.created_at, s.id, s.name, s.type
         FROM school_members m JOIN schools s ON s.id = m.school_id
         WHERE m.user_id = ? AND m.member_role = ?
         ORDER BY (m.status = "active") DESC, m.id DESC',
        [$userId, $role]
    );
}

/** Pending self-requested join requests for a school + role. */
function pending_member_requests(int $schoolId, string $role): array
{
    return db_all(
        'SELECT m.id AS member_id, u.name, u.email, m.created_at
         FROM school_members m JOIN users u ON u.id = m.user_id
         WHERE m.school_id = ? AND m.member_role = ? AND m.status = "pending"
         ORDER BY m.id DESC',
        [$schoolId, $role]
    );
}

/** Approve a pending join request and notify the user. */
function approve_member_request(int $schoolId, int $memberId): bool
{
    $row = db_one(
        'SELECT m.id, m.user_id, m.member_role, s.name AS school_name
         FROM school_members m JOIN schools s ON s.id = m.school_id
         WHERE m.id = ? AND m.school_id = ? AND m.status = "pending"',
        [$memberId, $schoolId]
    );
    if (!$row) {
        return false;
    }
    db_exec('UPDATE school_members SET status = "active" WHERE id = ?', [(int) $row['id']]);
    notify((int) $row['user_id'], 'You joined ' . $row['school_name'], 'Your join request was approved.', 'school');
    return true;
}

/** School-wide aggregate analytics across member students. */
function school_analytics(int $schoolId): array
{
    $base = 'FROM school_members m WHERE m.school_id = ? AND m.member_role = "student"';
    $studentIds = array_map('intval', array_column(db_all('SELECT user_id ' . $base, [$schoolId]), 'user_id'));
    if (!$studentIds) {
        return ['students' => 0, 'avg_score' => 0, 'total_attempts' => 0, 'active_today' => 0, 'weak_topics' => [], 'top_students' => []];
    }
    $in = implode(',', array_fill(0, count($studentIds), '?'));

    $agg = db_one(
        "SELECT COALESCE(AVG(avg_score),0) avg_score, COALESCE(SUM(total_questions),0) attempts
         FROM student_progress_summary WHERE user_id IN ($in)",
        $studentIds
    );
    $activeToday = (int) (db_one(
        "SELECT COUNT(DISTINCT user_id) c FROM question_attempts WHERE DATE(created_at) = CURDATE() AND user_id IN ($in)",
        $studentIds
    )['c'] ?? 0);
    $weak = db_all(
        "SELECT t.name, ROUND(AVG(ts.mastery),0) m FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id
         WHERE ts.user_id IN ($in) GROUP BY t.id ORDER BY m ASC LIMIT 5",
        $studentIds
    );
    $top = db_all(
        "SELECT u.name, ps.avg_score FROM student_progress_summary ps JOIN users u ON u.id = ps.user_id
         WHERE ps.user_id IN ($in) ORDER BY ps.avg_score DESC LIMIT 5",
        $studentIds
    );

    return [
        'students'       => count($studentIds),
        'avg_score'      => (float) $agg['avg_score'],
        'total_attempts' => (int) $agg['attempts'],
        'active_today'   => $activeToday,
        'weak_topics'    => $weak,
        'top_students'   => $top,
    ];
}
