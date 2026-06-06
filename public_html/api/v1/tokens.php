<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';

api_boot();
$user = api_user();
$uid  = (int) $user['id'];

// POST {action: "revoke", id: N} -> delete a token belonging to the user.
if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    $body = api_body();
    if (($body['action'] ?? '') !== 'revoke') {
        api_error('Unsupported action.', 422);
    }
    $id = (int) ($body['id'] ?? 0);
    db_exec('DELETE FROM api_tokens WHERE id = ? AND user_id = ?', [$id, $uid]);
    api_json(['ok' => true]);
}

api_require_method('GET');
api_json([
    'tokens' => array_map(fn($t) => [
        'id'           => (int) $t['id'],
        'name'         => $t['name'],
        'last_used_at' => $t['last_used_at'],
        'expires_at'   => $t['expires_at'],
        'created_at'   => $t['created_at'],
        'current'      => (int) $t['id'] === (int) $user['token_id'],
    ], db_all('SELECT id, name, last_used_at, expires_at, created_at FROM api_tokens WHERE user_id = ? ORDER BY id DESC', [$uid])),
]);
