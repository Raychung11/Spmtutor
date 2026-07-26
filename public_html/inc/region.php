<?php
/**
 * Region / education-level helpers.
 *
 * Wraps the pattern of "give me only the subjects that match this
 * student's chosen exam system" so the six student pages (diagnostic,
 * practice, library, snap_check, tutor, writing) stay tidy.
 *
 * All helpers are safe when the student has no education_level_id
 * set — they return null / an empty filter and callers fall back to
 * showing everything (matches pre-multi-region behaviour).
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** The education_level_id the student picked at registration (or null). */
function current_student_level_id(int $userId): ?int
{
    static $cache = [];
    if (array_key_exists($userId, $cache)) {
        return $cache[$userId];
    }
    $row = db_one('SELECT education_level_id FROM student_profiles WHERE user_id = ?', [$userId]);
    $lid = $row && $row['education_level_id'] ? (int) $row['education_level_id'] : null;
    return $cache[$userId] = $lid;
}

/** Full row for the student's chosen level, or null. */
function current_student_level(int $userId): ?array
{
    $lid = current_student_level_id($userId);
    if (!$lid) return null;
    return db_one('SELECT id, name, slug FROM education_levels WHERE id = ?', [$lid]);
}

/**
 * Returns [$sql, $params] to filter a `subjects` query by the student's
 * chosen region. Use like:
 *
 *   [$where, $args] = student_subjects_filter($uid);
 *   db_all("SELECT id, name FROM subjects WHERE status = 'active' $where ORDER BY sort_order", $args);
 *
 * If the student has no level set, returns an empty filter — the caller
 * shows everything (safe default matching pre-multi-region behaviour).
 */
function student_subjects_filter(int $userId, string $tableAlias = ''): array
{
    $lid = current_student_level_id($userId);
    if (!$lid) return ['', []];
    $col = $tableAlias === '' ? 'education_level_id' : ($tableAlias . '.education_level_id');
    return [' AND ' . $col . ' = ?', [$lid]];
}
