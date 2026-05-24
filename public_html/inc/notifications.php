<?php
/**
 * In-app notifications (email/WhatsApp channels are integration-ready).
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function notify(int $userId, string $title, ?string $body = null, string $type = 'general'): int
{
    return db_exec(
        'INSERT INTO notifications (user_id, title, body, type) VALUES (?,?,?,?)',
        [$userId, $title, $body, $type]
    );
}

function unread_count(int $userId): int
{
    return (int) (db_one('SELECT COUNT(*) c FROM notifications WHERE user_id = ? AND is_read = 0', [$userId])['c'] ?? 0);
}

function list_notifications(int $userId, int $limit = 50): array
{
    return db_all('SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT ?', [$userId, $limit]);
}

function mark_all_read(int $userId): void
{
    db_exec('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0', [$userId]);
}

function mark_read(int $userId, int $notificationId): void
{
    db_exec('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?', [$notificationId, $userId]);
}
