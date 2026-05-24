<?php
/**
 * Weekly AI parent-report generator. Run from CLI / cron only, e.g.:
 *   php /path/to/public_html/cron/weekly_reports.php
 * On Hostinger, schedule weekly via the cron jobs panel.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../inc/reports.php';

$students = db_all("SELECT id, name FROM users WHERE role = 'student' AND status = 'active'");
$done = 0;
foreach ($students as $s) {
    try {
        generate_weekly_report((int) $s['id']);
        $done++;
    } catch (Throwable $e) {
        fwrite(STDERR, 'Failed for user ' . $s['id'] . ': ' . $e->getMessage() . PHP_EOL);
    }
}
echo "Generated weekly reports for {$done} student(s)." . PHP_EOL;
