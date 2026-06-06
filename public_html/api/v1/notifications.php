<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';
require_once __DIR__ . '/../../inc/notifications.php';

api_boot();
$user = api_user();
$uid  = (int) $user['id'];

// POST marks all read; GET lists.
if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    mark_all_read($uid);
    api_json(['ok' => true]);
}

api_json([
    'unread' => unread_count($uid),
    'items'  => array_map(fn($n) => [
        'id'      => (int) $n['id'],
        'title'   => $n['title'],
        'body'    => $n['body'],
        'type'    => $n['type'],
        'is_read' => (bool) $n['is_read'],
        'created_at' => $n['created_at'],
    ], list_notifications($uid)),
]);
