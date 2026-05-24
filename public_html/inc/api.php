<?php
/**
 * Mobile / REST API helpers: JSON I/O and bearer-token authentication.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** Set CORS headers and short-circuit preflight requests. */
function api_boot(): void
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function api_json(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function api_error(string $message, int $status = 400): never
{
    api_json(['error' => $message], $status);
}

/** Read JSON or form body into an array. */
function api_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw !== '' && $raw !== false) {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }
    }
    return $_POST;
}

/** Issue a new API token for a user; returns the plaintext token (shown once). */
function api_issue_token(int $userId, string $name = 'mobile'): string
{
    $plain = bin2hex(random_bytes(32));
    $hash  = hash('sha256', $plain);
    $expires = date('Y-m-d H:i:s', strtotime('+' . API_TOKEN_TTL_DAYS . ' days'));
    db_exec(
        'INSERT INTO api_tokens (user_id, token_hash, name, expires_at) VALUES (?,?,?,?)',
        [$userId, $hash, $name, $expires]
    );
    return $plain;
}

/** Resolve the authenticated user from the Authorization: Bearer header. */
function api_user(): array
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
    if ($header === '' && function_exists('apache_request_headers')) {
        $h = apache_request_headers();
        $header = $h['Authorization'] ?? '';
    }
    if (!preg_match('/Bearer\s+([0-9a-f]{64})/i', $header, $m)) {
        api_error('Missing or malformed bearer token.', 401);
    }
    $hash = hash('sha256', $m[1]);
    $row = db_one(
        'SELECT t.id AS token_id, u.* FROM api_tokens t JOIN users u ON u.id = t.user_id
         WHERE t.token_hash = ? AND (t.expires_at IS NULL OR t.expires_at > NOW())',
        [$hash]
    );
    if (!$row || $row['status'] !== 'active') {
        api_error('Invalid or expired token.', 401);
    }
    db_exec('UPDATE api_tokens SET last_used_at = NOW() WHERE id = ?', [$row['token_id']]);
    return $row;
}

/** Require a specific HTTP method. */
function api_require_method(string $method): void
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== $method) {
        api_error('Method not allowed.', 405);
    }
}
