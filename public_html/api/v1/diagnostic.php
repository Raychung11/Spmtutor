<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';
require_once __DIR__ . '/../../inc/diagnostic.php';

api_boot();
$user = api_user();
$uid  = (int) $user['id'];

if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    $body      = api_body();
    $subjectId = (int) ($body['subject_id'] ?? 0);
    $answers   = $body['answers'] ?? [];
    if (!$subjectId || !is_array($answers) || !$answers) {
        api_error('subject_id and a non-empty answers map are required.', 422);
    }
    $attemptId = grade_diagnostic($uid, $subjectId, array_map('intval', $answers));
    $attempt   = db_one('SELECT score FROM diagnostic_attempts WHERE id = ?', [$attemptId]);
    $result    = db_one('SELECT strong_topics, weak_topics, recommendation FROM diagnostic_results WHERE attempt_id = ?', [$attemptId]);
    api_json([
        'attempt_id'     => $attemptId,
        'score'          => (float) ($attempt['score'] ?? 0),
        'strong_topics'  => array_values(array_filter(array_map('trim', explode(',', (string) ($result['strong_topics'] ?? ''))))),
        'weak_topics'    => array_values(array_filter(array_map('trim', explode(',', (string) ($result['weak_topics'] ?? ''))))),
        'recommendation' => $result['recommendation'] ?? '',
    ]);
}

// GET: questions to take for a subject, or past attempts.
$subjectId = (int) ($_GET['subject_id'] ?? 0);
if ($subjectId) {
    $questions = array_map(function ($q) {
        return [
            'id'       => (int) $q['id'],
            'question' => $q['question_text'],
            'options'  => array_map(fn($o) => ['id' => (int) $o['id'], 'label' => $o['label'], 'text' => $o['option_text']], diagnostic_options((int) $q['id'])),
        ];
    }, diagnostic_questions($subjectId));
    api_json(['subject_id' => $subjectId, 'questions' => $questions]);
}

api_json([
    'attempts' => db_all(
        'SELECT da.id, da.score, da.completed_at, s.name AS subject
         FROM diagnostic_attempts da JOIN diagnostic_tests dt ON dt.id = da.test_id JOIN subjects s ON s.id = dt.subject_id
         WHERE da.user_id = ? AND da.status = "completed" ORDER BY da.id DESC LIMIT 20',
        [$uid]
    ),
]);
