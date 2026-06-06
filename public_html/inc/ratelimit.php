<?php
/**
 * Simple fixed-window rate limiter backed by the rate_limits table.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Returns true if the action is allowed, false if the limit is exceeded.
 *
 * @param string $key       Unique bucket key (e.g. "ai:USERID" or "login:IP").
 * @param int    $max       Max hits allowed within the window.
 * @param int    $windowSec Window length in seconds.
 */
function rate_limit(string $key, int $max, int $windowSec): bool
{
    $key = substr($key, 0, 190);
    $now = time();

    $row = db_one('SELECT id, UNIX_TIMESTAMP(window_start) AS ws, hits FROM rate_limits WHERE rl_key = ?', [$key]);

    if (!$row) {
        try {
            db_exec('INSERT INTO rate_limits (rl_key, window_start, hits) VALUES (?, NOW(), 1)', [$key]);
            return true;
        } catch (Throwable $e) {
            return true; // fail open rather than block legitimate users
        }
    }

    if (($now - (int) $row['ws']) > $windowSec) {
        // Window expired — reset.
        db_exec('UPDATE rate_limits SET window_start = NOW(), hits = 1 WHERE id = ?', [$row['id']]);
        return true;
    }

    if ((int) $row['hits'] >= $max) {
        return false;
    }

    db_exec('UPDATE rate_limits SET hits = hits + 1 WHERE id = ?', [$row['id']]);
    return true;
}

/** Best-effort client IP. */
function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
