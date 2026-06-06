<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';

header('Content-Type: application/json');
require_login();

$subjectId = (int) ($_GET['subject_id'] ?? 0);
$rows = $subjectId
    ? db_all('SELECT id, name FROM topics WHERE subject_id = ? AND status = "active" ORDER BY sort_order', [$subjectId])
    : [];
echo json_encode($rows);
