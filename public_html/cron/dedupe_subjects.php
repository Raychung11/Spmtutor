<?php
/**
 * Subjects dedupe maintenance script.
 *
 * Detects subject rows that share an (education_level_id, slug) or
 * (education_level_id, name) and merges them: keeps the lowest id of
 * each group, re-points every dependent row (topics, questions,
 * lessons, diagnostic tests, learning paths, teacher classes, chat
 * sessions, essay submissions, sandbox runs, student stats) to the
 * keeper, then deletes the duplicates.
 *
 * Always runs as DRY-RUN unless $apply=true so the admin can preview
 * what will happen before pulling the trigger.
 *
 *   php public_html/cron/dedupe_subjects.php          # dry-run
 *   php public_html/cron/dedupe_subjects.php --apply  # commit
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

/** Tables with a regular subject_id column we can just re-point. */
function dedupe_subjects_simple_tables(): array
{
    return [
        'topics', 'questions', 'lessons', 'diagnostic_tests',
        'learning_paths', 'teacher_classes',
        'ai_chat_sessions', 'essay_submissions', 'sandbox_runs',
    ];
}

/** Build groups of duplicate subjects to merge. */
function dedupe_subjects_groups(): array
{
    // Two passes: by (level, slug) and by (level, name). We collect all
    // distinct ids per "fingerprint" and treat any fingerprint with >1
    // ids as a duplicate group.
    $groups = [];

    foreach (db_all(
        'SELECT education_level_id, LOWER(slug) AS k, GROUP_CONCAT(id ORDER BY id) AS ids
         FROM subjects
         WHERE slug IS NOT NULL AND slug <> ""
         GROUP BY education_level_id, LOWER(slug)
         HAVING COUNT(*) > 1'
    ) as $row) {
        $ids = array_map('intval', explode(',', (string) $row['ids']));
        $groups[] = ['by' => 'slug', 'key' => $row['k'], 'ids' => $ids];
    }

    foreach (db_all(
        'SELECT education_level_id, LOWER(name) AS k, GROUP_CONCAT(id ORDER BY id) AS ids
         FROM subjects
         GROUP BY education_level_id, LOWER(name)
         HAVING COUNT(*) > 1'
    ) as $row) {
        $ids = array_map('intval', explode(',', (string) $row['ids']));
        // Drop ids already covered by a slug group.
        foreach ($groups as $g) {
            $ids = array_values(array_diff($ids, $g['ids']));
        }
        if (count($ids) > 1) {
            $groups[] = ['by' => 'name', 'key' => $row['k'], 'ids' => $ids];
        }
    }

    return $groups;
}

/**
 * Run the dedupe.
 * @param bool $apply false = dry-run, true = actually merge + delete.
 */
function dedupe_subjects_run(bool $apply = false): array
{
    $groups = dedupe_subjects_groups();
    $simple = dedupe_subjects_simple_tables();

    $report = [
        'status'         => 'ok',
        'apply'          => $apply,
        'groups'         => count($groups),
        'duplicates'     => 0,
        'rows_repointed' => 0,
        'stats_conflicts'=> 0,
        'subjects_deleted' => 0,
        'details'        => [],
    ];

    if (!$groups) {
        $report['details'][] = 'No duplicate subjects detected.';
        return $report;
    }

    foreach ($groups as $g) {
        sort($g['ids']);
        $keeper = (int) array_shift($g['ids']);
        $dupes  = $g['ids'];
        $report['duplicates'] += count($dupes);

        // Look up the keeper's display name for the report.
        $keeperRow = db_one('SELECT name FROM subjects WHERE id = ?', [$keeper]);
        $keeperName = $keeperRow['name'] ?? ('id=' . $keeper);

        $line = sprintf(
            'Group by %s "%s": keep id=%d (%s), %s id=%s',
            $g['by'], $g['key'], $keeper, $keeperName,
            $apply ? 'merged + deleted' : 'would merge + delete',
            implode(',', $dupes)
        );

        foreach ($dupes as $dupeId) {
            $dupeId = (int) $dupeId;

            foreach ($simple as $table) {
                if ($apply) {
                    db_exec("UPDATE $table SET subject_id = ? WHERE subject_id = ?", [$keeper, $dupeId]);
                    $n = db_one("SELECT ROW_COUNT() AS c")['c'] ?? 0;
                    $report['rows_repointed'] += (int) $n;
                } else {
                    $n = db_one("SELECT COUNT(*) AS c FROM $table WHERE subject_id = ?", [$dupeId])['c'] ?? 0;
                    $report['rows_repointed'] += (int) $n;
                }
            }

            // student_subject_stats has UNIQUE (user_id, subject_id) — resolve conflicts
            // by deleting the dupe row when the keeper already has stats for the same user.
            $conflictsRow = db_one(
                'SELECT COUNT(*) AS c FROM student_subject_stats s
                 WHERE s.subject_id = ?
                   AND EXISTS (SELECT 1 FROM student_subject_stats s2
                               WHERE s2.subject_id = ? AND s2.user_id = s.user_id)',
                [$dupeId, $keeper]
            );
            $conflicts = (int) ($conflictsRow['c'] ?? 0);
            $report['stats_conflicts'] += $conflicts;

            if ($apply) {
                if ($conflicts > 0) {
                    db_exec(
                        'DELETE s FROM student_subject_stats s
                         JOIN student_subject_stats s2
                              ON s2.user_id = s.user_id AND s2.subject_id = ?
                         WHERE s.subject_id = ?',
                        [$keeper, $dupeId]
                    );
                }
                db_exec('UPDATE student_subject_stats SET subject_id = ? WHERE subject_id = ?', [$keeper, $dupeId]);
                $movedRow = db_one('SELECT ROW_COUNT() AS c');
                $report['rows_repointed'] += (int) ($movedRow['c'] ?? 0);

                db_exec('DELETE FROM subjects WHERE id = ?', [$dupeId]);
                $report['subjects_deleted']++;
            } else {
                $movedRow = db_one('SELECT COUNT(*) AS c FROM student_subject_stats WHERE subject_id = ?', [$dupeId]);
                $report['rows_repointed'] += max(0, ((int) ($movedRow['c'] ?? 0)) - $conflicts);
            }
        }

        $report['details'][] = $line;
    }

    return $report;
}

if (PHP_SAPI === 'cli') {
    $apply = in_array('--apply', $argv ?? [], true);
    $r = dedupe_subjects_run($apply);
    echo ($apply ? "APPLIED" : "DRY-RUN") . " — subject dedupe report\n";
    echo "  Duplicate groups:    {$r['groups']}\n";
    echo "  Duplicate subjects:  {$r['duplicates']}\n";
    echo "  Rows re-pointed:     {$r['rows_repointed']}\n";
    echo "  Stats conflicts:     {$r['stats_conflicts']}\n";
    echo "  Subjects deleted:    {$r['subjects_deleted']}\n";
    foreach ($r['details'] as $d) {
        echo "  - $d\n";
    }
    if (!$apply && $r['groups'] > 0) {
        echo "\nRe-run with --apply to commit.\n";
    }
}
