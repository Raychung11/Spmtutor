<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';

api_boot();
api_require_method('GET');
$user = api_user();
$uid  = (int) $user['id'];

api_json([
    'summary' => db_one('SELECT total_questions, total_correct, avg_score FROM student_progress_summary WHERE user_id = ?', [$uid])
        ?: ['total_questions' => 0, 'total_correct' => 0, 'avg_score' => 0],
    'subjects' => db_all(
        'SELECT s.name AS subject, ss.total_attempts, ss.total_correct, ss.avg_score
         FROM student_subject_stats ss JOIN subjects s ON s.id = ss.subject_id
         WHERE ss.user_id = ? ORDER BY ss.avg_score DESC',
        [$uid]
    ),
    'topics' => db_all(
        'SELECT t.name AS topic, ts.total_attempts, ts.mastery
         FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id
         WHERE ts.user_id = ? ORDER BY ts.mastery ASC',
        [$uid]
    ),
]);
