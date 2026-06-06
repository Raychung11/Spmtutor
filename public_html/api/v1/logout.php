<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';

api_boot();
api_require_method('POST');
$user = api_user();

db_exec('DELETE FROM api_tokens WHERE id = ?', [(int) $user['token_id']]);
api_json(['ok' => true]);
