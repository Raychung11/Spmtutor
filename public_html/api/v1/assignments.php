<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';
require_once __DIR__ . '/../../inc/classes.php';

api_boot();
$user = api_user();
$uid  = (int) $user['id'];

if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    $body = api_body();
    $assignmentId = (int) ($body['assignment_id'] ?? 0);
    $content      = (string) ($body['content'] ?? '');
    if (!$assignmentId || $content === '') {
        api_error('assignment_id and content are required.', 422);
    }
    if (!submit_assignment($assignmentId, $uid, $content, null)) {
        api_error('Not enrolled in this assignment\'s class.', 403);
    }
    api_json(['ok' => true]);
}

api_require_method('GET');
api_json([
    'assignments' => array_map(fn($a) => [
        'id'          => (int) $a['id'],
        'title'       => $a['title'],
        'description' => $a['description'],
        'class'       => $a['class_name'],
        'due_date'    => $a['due_date'],
        'submitted'   => $a['submission_id'] !== null,
        'status'      => $a['submission_status'],
        'score'       => $a['score'] !== null ? (float) $a['score'] : null,
    ], student_assignments($uid)),
]);
