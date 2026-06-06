<?php
/**
 * Daily study / streak reminders. Run from CLI / cron or from
 * Admin → Seeders. Sends an in-app notification (and WhatsApp if
 * configured) to students who haven't practised yet today.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/notifications.php';
require_once __DIR__ . '/../inc/whatsapp.php';

function run_reminders(): array
{
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
    $sent = 0; $whatsapp = 0;
    foreach ($students as $s) {
        $streak = (int) $s['streak'];
        $msg = $streak > 0
            ? "Hi {$s['name']}! You're on a {$streak}-day streak 🔥 — answer a few questions today to keep it alive."
            : "Hi {$s['name']}! A quick 10-minute practice session today will help you improve. Let's go!";

        notify((int) $s['id'], 'Time to practise', $msg, 'reminder');
        if (!empty($s['phone']) && send_whatsapp($s['phone'], $msg)) {
            $whatsapp++;
        }
        $sent++;
    }
    return ['sent' => $sent, 'whatsapp' => $whatsapp, 'total' => count($students)];
}

if (PHP_SAPI === 'cli') {
    $r = run_reminders();
    echo "Sent reminders to {$r['sent']} student(s) (WhatsApp: {$r['whatsapp']})." . PHP_EOL;
}
