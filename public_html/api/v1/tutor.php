<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';
require_once __DIR__ . '/../../inc/ai.php';

api_boot();
api_require_method('POST');
$user = api_user();
$uid  = (int) $user['id'];

$body      = api_body();
$message   = trim((string) ($body['message'] ?? ''));
$sessionId = (int) ($body['session_id'] ?? 0);
$subjectId = (int) ($body['subject_id'] ?? 0) ?: null;

if ($message === '') {
    api_error('message is required.', 422);
}

if ($sessionId) {
    $session = db_one('SELECT id FROM ai_chat_sessions WHERE id = ? AND user_id = ?', [$sessionId, $uid]);
    if (!$session) {
        $sessionId = 0;
    }
}
if (!$sessionId) {
    $sessionId = db_exec('INSERT INTO ai_chat_sessions (user_id, subject_id, title) VALUES (?,?,?)', [$uid, $subjectId, mb_substr($message, 0, 60)]);
}

db_exec('INSERT INTO ai_chat_messages (session_id, role, content) VALUES (?,?,?)', [$sessionId, 'user', $message]);

$tpl    = ai_prompt('tutor');
$system = $tpl['system_prompt'];
if ($subjectId) {
    $subject = db_one('SELECT s.name, l.name AS level FROM subjects s LEFT JOIN education_levels l ON l.id = s.education_level_id WHERE s.id = ?', [$subjectId]);
    if ($subject) {
        $system .= "\n\nThe student is studying " . $subject['name'] . ($subject['level'] ? ' at ' . $subject['level'] . ' level' : '') . '.';
    }
}

$history  = db_all('SELECT role, content FROM ai_chat_messages WHERE session_id = ? ORDER BY id ASC LIMIT 20', [$sessionId]);
$messages = array_map(fn($m) => ['role' => $m['role'], 'content' => $m['content']], $history);
$reply    = ai_chat($system, $messages, (float) $tpl['temperature'], $tpl['model']);

db_exec('INSERT INTO ai_chat_messages (session_id, role, content) VALUES (?,?,?)', [$sessionId, 'assistant', $reply]);
db_exec('UPDATE ai_chat_sessions SET updated_at = NOW() WHERE id = ?', [$sessionId]);

api_json(['reply' => $reply, 'session_id' => $sessionId]);
