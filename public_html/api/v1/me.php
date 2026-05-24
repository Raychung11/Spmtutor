<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';

api_boot();
api_require_method('GET');
$user = api_user();
$uid  = (int) $user['id'];

$summary = db_one('SELECT total_questions, total_correct, avg_score FROM student_progress_summary WHERE user_id = ?', [$uid]);
$streak  = db_one('SELECT current_streak, longest_streak FROM student_streaks WHERE user_id = ?', [$uid]);
$profile = db_one('SELECT xp, level FROM student_profiles WHERE user_id = ?', [$uid]);

api_json([
    'user' => [
        'id' => $uid, 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role'],
    ],
    'xp'      => (int) ($profile['xp'] ?? 0),
    'level'   => (int) ($profile['level'] ?? 1),
    'streak'  => (int) ($streak['current_streak'] ?? 0),
    'longest_streak' => (int) ($streak['longest_streak'] ?? 0),
    'progress' => [
        'total_questions' => (int) ($summary['total_questions'] ?? 0),
        'total_correct'   => (int) ($summary['total_correct'] ?? 0),
        'avg_score'       => (float) ($summary['avg_score'] ?? 0),
    ],
    'unread_notifications' => (int) (db_one('SELECT COUNT(*) c FROM notifications WHERE user_id = ? AND is_read = 0', [$uid])['c'] ?? 0),
]);
