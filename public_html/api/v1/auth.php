<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';
require_once __DIR__ . '/../../inc/db.php';

api_boot();
api_require_method('POST');

$body  = api_body();
$email = strtolower(trim((string) ($body['email'] ?? '')));
$pass  = (string) ($body['password'] ?? '');

if ($email === '' || $pass === '') {
    api_error('Email and password are required.', 422);
}

$user = db_one('SELECT * FROM users WHERE email = ?', [$email]);
$ok   = $user && password_verify($pass, $user['password_hash']);

db_exec(
    'INSERT INTO login_logs (user_id, email, ip_address, user_agent, result) VALUES (?,?,?,?,?)',
    [$user['id'] ?? null, $email, $_SERVER['REMOTE_ADDR'] ?? null, 'api', $ok ? 'success' : 'failed']
);

if (!$ok) {
    api_error('Invalid credentials.', 401);
}
if ($user['status'] !== 'active') {
    api_error('Account is ' . $user['status'] . '.', 403);
}

$token = api_issue_token((int) $user['id'], 'mobile');
db_exec('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);

api_json([
    'token' => $token,
    'token_type' => 'Bearer',
    'expires_in_days' => API_TOKEN_TTL_DAYS,
    'user'  => [
        'id'    => (int) $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role'],
    ],
]);
