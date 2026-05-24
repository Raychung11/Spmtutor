<?php
/**
 * School / learning-centre helpers: ownership, membership, analytics.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/notifications.php';

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
             WHERE m.school_id = ? AND m.member_role = "student" ORDER BY u.name',
            [$schoolId]
        );
    }
    return db_all(
        'SELECT m.id AS member_id, u.id, u.name, u.email,
                (SELECT COUNT(*) FROM teacher_classes c WHERE c.teacher_user_id = u.id) AS class_count
         FROM school_members m JOIN users u ON u.id = m.user_id
         WHERE m.school_id = ? AND m.member_role = "teacher" ORDER BY u.name',
        [$schoolId]
    );
}

function school_member_count(int $schoolId, string $role): int
{
    return (int) (db_one('SELECT COUNT(*) c FROM school_members WHERE school_id = ? AND member_role = ?', [$schoolId, $role])['c'] ?? 0);
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
