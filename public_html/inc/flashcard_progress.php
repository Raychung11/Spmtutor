<?php
/**
 * Flashcard spaced-repetition progress helpers.
 *
 * SM-2-inspired scheduler with three actions:
 *   - 'known'  → grade 4 (good)    : interval *= ease, ease += 0.10 (cap 3.0)
 *   - 'review' → grade 2 (forgot)  : interval = 1,    ease -= 0.20 (floor 1.30)
 *   - 'easy'   → grade 5 (perfect) : interval *= ease * 1.3, ease += 0.15
 *
 * Reviews scheduled into next_due_at; "due today" = next_due_at <= NOW().
 * New cards (no progress row) are treated as due immediately.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function fcp_table_ready(): bool
{
    static $ready = null;
    if ($ready !== null) return $ready;
    $ready = (bool) db_one(
        "SELECT 1 AS x FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'flashcard_progress'"
    );
    return $ready;
}

/** Load progress rows for a user × library, keyed by card_id. */
function fcp_load(int $userId, string $librarySlug): array
{
    if (!fcp_table_ready()) return [];
    $rows = db_all(
        'SELECT card_id, status, ease_factor, interval_days, review_count, last_seen_at, next_due_at
         FROM flashcard_progress WHERE user_id = ? AND library_slug = ?',
        [$userId, $librarySlug]
    );
    $out = [];
    foreach ($rows as $r) {
        $out[(int) $r['card_id']] = [
            'status'        => (string) $r['status'],
            'ease_factor'   => (float) $r['ease_factor'],
            'interval_days' => (int) $r['interval_days'],
            'review_count'  => (int) $r['review_count'],
            'last_seen_at'  => $r['last_seen_at'],
            'next_due_at'   => $r['next_due_at'],
        ];
    }
    return $out;
}

/** Count how many cards a user has due now in a library (new cards count as due). */
function fcp_due_count(int $userId, string $librarySlug, int $totalCards): int
{
    if (!fcp_table_ready() || $totalCards === 0) return $totalCards;
    $row = db_one(
        "SELECT COUNT(*) c FROM flashcard_progress
         WHERE user_id = ? AND library_slug = ? AND next_due_at IS NOT NULL AND next_due_at <= NOW()",
        [$userId, $librarySlug]
    );
    $dueWithProgress = (int) ($row['c'] ?? 0);

    $row2 = db_one(
        "SELECT COUNT(*) c FROM flashcard_progress
         WHERE user_id = ? AND library_slug = ?",
        [$userId, $librarySlug]
    );
    $hasProgress = (int) ($row2['c'] ?? 0);
    $neverSeen = max(0, $totalCards - $hasProgress);
    return $dueWithProgress + $neverSeen;
}

/** Aggregate counts: new (never seen) / due (overdue with progress) / done (not yet due) / total. */
function fcp_summary(int $userId, string $librarySlug, int $totalCards): array
{
    if (!fcp_table_ready() || $totalCards === 0) {
        return ['new' => $totalCards, 'due' => 0, 'done' => 0, 'total' => $totalCards];
    }
    $row = db_one(
        "SELECT
            SUM(CASE WHEN next_due_at IS NOT NULL AND next_due_at <= NOW() THEN 1 ELSE 0 END) AS due,
            SUM(CASE WHEN next_due_at IS NOT NULL AND next_due_at >  NOW() THEN 1 ELSE 0 END) AS done,
            COUNT(*) AS tracked
         FROM flashcard_progress WHERE user_id = ? AND library_slug = ?",
        [$userId, $librarySlug]
    );
    $due  = (int) ($row['due']  ?? 0);
    $done = (int) ($row['done'] ?? 0);
    $tracked = (int) ($row['tracked'] ?? 0);
    $new = max(0, $totalCards - $tracked);
    return [
        'new'   => $new,
        'due'   => $due,
        'done'  => $done,
        'total' => $totalCards,
    ];
}

/**
 * Apply an action to a card and persist. Returns the updated progress row.
 *
 * $action: 'known' | 'review' | 'easy'
 */
function fcp_apply(int $userId, string $librarySlug, int $cardId, string $action): array
{
    if (!fcp_table_ready()) {
        return ['ok' => false, 'error' => 'flashcard_progress table not yet migrated'];
    }
    if (!in_array($action, ['known', 'review', 'easy'], true)) {
        return ['ok' => false, 'error' => 'invalid action'];
    }

    $existing = db_one(
        'SELECT * FROM flashcard_progress WHERE user_id = ? AND library_slug = ? AND card_id = ?',
        [$userId, $librarySlug, $cardId]
    );
    $ease     = $existing ? (float) $existing['ease_factor']   : 2.50;
    $interval = $existing ? (int)   $existing['interval_days'] : 1;
    $reviews  = $existing ? (int)   $existing['review_count']  : 0;

    if ($action === 'review') {
        $interval = 1;
        $ease     = max(1.30, $ease - 0.20);
        $status   = 'review';
    } elseif ($action === 'easy') {
        $interval = max(1, (int) round($interval * $ease * 1.3));
        $ease     = min(3.00, $ease + 0.15);
        $status   = 'known';
    } else { // 'known'
        $interval = max(1, (int) round(($reviews === 0 ? 1 : $interval) * $ease));
        $ease     = min(3.00, $ease + 0.10);
        $status   = 'known';
    }

    $reviews++;
    $nextDue = date('Y-m-d H:i:s', strtotime("+{$interval} day"));

    if ($existing) {
        db_exec(
            'UPDATE flashcard_progress
             SET status = ?, ease_factor = ?, interval_days = ?, review_count = ?,
                 last_seen_at = NOW(), next_due_at = ?
             WHERE id = ?',
            [$status, $ease, $interval, $reviews, $nextDue, (int) $existing['id']]
        );
    } else {
        db_exec(
            'INSERT INTO flashcard_progress
                (user_id, library_slug, card_id, status, ease_factor, interval_days,
                 review_count, last_seen_at, next_due_at)
             VALUES (?,?,?,?,?,?,?,NOW(),?)',
            [$userId, $librarySlug, $cardId, $status, $ease, $interval, $reviews, $nextDue]
        );
    }

    return [
        'ok'           => true,
        'status'       => $status,
        'ease'         => $ease,
        'interval'     => $interval,
        'review_count' => $reviews,
        'next_due_at'  => $nextDue,
    ];
}

/**
 * Bulk due-count across multiple libraries for a given user.
 * Returns [library_slug => due_count] including unseen-as-due rollover.
 */
function fcp_due_counts(int $userId, array $librarySlugs, array $totalCardsBySlug): array
{
    $out = [];
    foreach ($librarySlugs as $slug) {
        $total = (int) ($totalCardsBySlug[$slug] ?? 0);
        $out[$slug] = fcp_due_count($userId, $slug, $total);
    }
    return $out;
}
