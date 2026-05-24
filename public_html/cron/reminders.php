<?php
/**
 * Daily study / streak reminders. Run from CLI / cron, e.g.:
 *   php /path/to/public_html/cron/reminders.php
 * Sends an in-app notification and (when configured) a WhatsApp message to
 * students who have not practised yet today.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/notifications.php';
require_once __DIR__ . '/../inc/whatsapp.php';

$today = date('Y-m-d');

$students = db_all(
    "SELECT u.id, u.name, u.phone, COALESCE(st.current_streak,0) streak
     FROM users u
     LEFT JOIN student_streaks st ON st.user_id = u.id
     WHERE u.role = 'student' AND u.status = 'active'
       AND NOT EXISTS (
         SELECT 1 FROM question_attempts qa
         WHERE qa.user_id = u.id AND DATE(qa.created_at) = ?
       )",
    [$today]
);

$sent = 0;
foreach ($students as $s) {
    $streak = (int) $s['streak'];
    $msg = $streak > 0
        ? "Hi {$s['name']}! You're on a {$streak}-day streak 🔥 — answer a few questions today to keep it alive."
        : "Hi {$s['name']}! A quick 10-minute practice session today will help you improve. Let's go!";

    notify((int) $s['id'], 'Time to practise', $msg, 'reminder');
    if (!empty($s['phone'])) {
        send_whatsapp($s['phone'], $msg);
    }
    $sent++;
}

echo "Sent reminders to {$sent} student(s)." . PHP_EOL;
