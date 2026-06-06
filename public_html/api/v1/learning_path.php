<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';
require_once __DIR__ . '/../../inc/learning.php';

api_boot();

if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    $user = api_user();
    $body = api_body();
    $itemId = (int) ($body['item_id'] ?? 0);
    $status = (string) ($body['status'] ?? '');
    if (!$itemId || !set_path_item_status((int) $user['id'], $itemId, $status)) {
        api_error('Invalid item_id or status.', 422);
    }
    api_json(['ok' => true]);
}

api_require_method('GET');
$user = api_user();
$uid  = (int) $user['id'];

$paths = array_map(function ($p) {
    return [
        'id'       => (int) $p['id'],
        'title'    => $p['title'],
        'subject'  => $p['subject'],
        'progress' => path_progress((int) $p['id']),
        'items'    => array_map(fn($i) => [
            'id'     => (int) $i['id'],
            'title'  => $i['title'],
            'status' => $i['status'],
        ], learning_path_items((int) $p['id'])),
    ];
}, active_learning_paths($uid));

api_json(['paths' => $paths]);
