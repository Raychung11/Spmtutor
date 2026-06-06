<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';

api_boot();
api_require_method('GET');
api_user();

$subjectId = (int) ($_GET['subject_id'] ?? 0);
if ($subjectId) {
    api_json([
        'topics' => db_all('SELECT id, name FROM topics WHERE subject_id = ? AND status = "active" ORDER BY sort_order', [$subjectId]),
    ]);
}

api_json([
    'subjects' => db_all(
        'SELECT s.id, s.name, s.description, l.name AS level
         FROM subjects s LEFT JOIN education_levels l ON l.id = s.education_level_id
         WHERE s.status = "active" ORDER BY s.sort_order'
    ),
]);
