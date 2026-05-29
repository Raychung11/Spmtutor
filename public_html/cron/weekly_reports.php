<?php
/**
 * Weekly AI parent-report generator. Run from CLI / cron or from
 * Admin → Seeders.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/reports.php';

function run_weekly_reports(): array
{
    $students = db_all("SELECT id, name FROM users WHERE role = 'student' AND status = 'active'");
    $done = 0; $failed = 0;
    foreach ($students as $s) {
        try {
            generate_weekly_report((int) $s['id']);
            $done++;
        } catch (Throwable $e) {
            $failed++;
            error_log('weekly_reports: failed for user ' . $s['id'] . ': ' . $e->getMessage());
        }
    }
    return ['generated' => $done, 'failed' => $failed, 'total' => count($students)];
}

if (PHP_SAPI === 'cli') {
    $r = run_weekly_reports();
    echo "Generated weekly reports for {$r['generated']}/{$r['total']} student(s)." . PHP_EOL;
}
