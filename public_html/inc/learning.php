<?php
/**
 * Learning path engine: build an adaptive, ordered study plan from weak
 * topics (and optionally a target exam date), and track item completion.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Create a fresh learning path for a subject from weak topics.
 *
 * @param array $weakTopics List of ['id'=>topic_id, 'name'=>, 'mastery'=>].
 * @return int learning_paths id.
 */
function generate_learning_path(int $userId, int $subjectId, array $weakTopics): int
{
    $subject = db_one('SELECT name FROM subjects WHERE id = ?', [$subjectId]);
    $target  = db_one('SELECT exam_target_date FROM student_profiles WHERE user_id = ?', [$userId])['exam_target_date'] ?? null;

    // Retire any previous active path for this subject so the newest is canonical.
    db_exec('UPDATE learning_paths SET status = "archived" WHERE user_id = ? AND subject_id = ? AND status = "active"', [$userId, $subjectId]);

    $pathId = db_exec(
        'INSERT INTO learning_paths (user_id, subject_id, title, target_date, status) VALUES (?,?,?,?,?)',
        [$userId, $subjectId, 'Improve ' . ($subject['name'] ?? 'subject'), $target, 'active']
    );

    // Weakest first.
    usort($weakTopics, fn($a, $b) => ($a['mastery'] ?? 0) <=> ($b['mastery'] ?? 0));
    $order = 1;
    foreach ($weakTopics as $t) {
        db_exec(
            'INSERT INTO learning_path_items (path_id, topic_id, title, sort_order, status) VALUES (?,?,?,?,?)',
            [$pathId, (int) $t['id'], 'Master: ' . $t['name'], $order++, 'pending']
        );
        // Add the topic's skills as sub-steps where available.
        foreach (db_all('SELECT id, name FROM skills WHERE topic_id = ? AND status = "active" ORDER BY difficulty', [(int) $t['id']]) as $sk) {
            db_exec(
                'INSERT INTO learning_path_items (path_id, topic_id, skill_id, title, sort_order, status) VALUES (?,?,?,?,?,?)',
                [$pathId, (int) $t['id'], (int) $sk['id'], 'Practise: ' . $sk['name'], $order++, 'pending']
            );
        }
    }

    return $pathId;
}

function active_learning_paths(int $userId): array
{
    return db_all(
        'SELECT lp.*, s.name AS subject FROM learning_paths lp
         LEFT JOIN subjects s ON s.id = lp.subject_id
         WHERE lp.user_id = ? AND lp.status = "active" ORDER BY lp.id DESC',
        [$userId]
    );
}

function learning_path_items(int $pathId): array
{
    return db_all('SELECT * FROM learning_path_items WHERE path_id = ? ORDER BY sort_order', [$pathId]);
}

/** Mark an item's status, verifying it belongs to the user. */
function set_path_item_status(int $userId, int $itemId, string $status): bool
{
    if (!in_array($status, ['pending', 'in_progress', 'done'], true)) {
        return false;
    }
    $owns = db_one(
        'SELECT i.id FROM learning_path_items i JOIN learning_paths p ON p.id = i.path_id
         WHERE i.id = ? AND p.user_id = ?',
        [$itemId, $userId]
    );
    if (!$owns) {
        return false;
    }
    db_exec('UPDATE learning_path_items SET status = ? WHERE id = ?', [$status, $itemId]);
    return true;
}

function path_progress(int $pathId): int
{
    $row = db_one(
        'SELECT COUNT(*) total, SUM(status = "done") done FROM learning_path_items WHERE path_id = ?',
        [$pathId]
    );
    $total = (int) ($row['total'] ?? 0);
    return $total ? (int) round(((int) $row['done']) / $total * 100) : 0;
}
