<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/ai.php';

header('Content-Type: application/json');

$user = current_user();
if (!$user || $user['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorised.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}
csrf_check();

$uid       = (int) $user['id'];
$message   = trim((string) ($_POST['message'] ?? ''));
$sessionId = (int) ($_POST['session_id'] ?? 0);
$subjectId = (int) ($_POST['subject_id'] ?? 0) ?: null;

if ($message === '') {
    echo json_encode(['error' => 'Empty message.']);
    exit;
}

// Find or create the chat session (and verify ownership).
if ($sessionId) {
    $session = db_one('SELECT * FROM ai_chat_sessions WHERE id = ? AND user_id = ?', [$sessionId, $uid]);
    if (!$session) {
        $sessionId = 0;
    }
}
if (!$sessionId) {
    $title     = mb_substr($message, 0, 60);
    $sessionId = db_exec('INSERT INTO ai_chat_sessions (user_id, subject_id, title) VALUES (?,?,?)', [$uid, $subjectId, $title]);
}

// Save the user message.
db_exec('INSERT INTO ai_chat_messages (session_id, role, content) VALUES (?,?,?)', [$sessionId, 'user', $message]);

// Build context: system prompt + subject + recent history.
$tpl    = ai_prompt('tutor');
$system = $tpl['system_prompt'];
if ($subjectId) {
    $subject = db_one('SELECT s.name, l.name AS level FROM subjects s LEFT JOIN education_levels l ON l.id = s.education_level_id WHERE s.id = ?', [$subjectId]);
    if ($subject) {
        $system .= "\n\nThe student is studying " . $subject['name'] . ($subject['level'] ? ' at ' . $subject['level'] . ' level' : '') . '.';
    }
}

$history = db_all('SELECT role, content FROM ai_chat_messages WHERE session_id = ? ORDER BY id ASC LIMIT 20', [$sessionId]);
$messages = array_map(fn($m) => ['role' => $m['role'], 'content' => $m['content']], $history);

$reply = ai_chat($system, $messages, (float) $tpl['temperature'], $tpl['model']);

db_exec('INSERT INTO ai_chat_messages (session_id, role, content) VALUES (?,?,?)', [$sessionId, 'assistant', $reply]);
db_exec('UPDATE ai_chat_sessions SET updated_at = NOW(), subject_id = COALESCE(subject_id, ?) WHERE id = ?', [$subjectId, $sessionId]);

echo json_encode(['reply' => $reply, 'session_id' => $sessionId]);
