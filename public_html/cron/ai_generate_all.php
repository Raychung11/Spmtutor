<?php
/**
 * Batch AI question generation for all topics matching a filter.
 *
 * Runnable from CLI for overnight cron usage OR from
 * /admin/ai_batch_generate.php for one-off runs.
 *
 * Skips topics that already have >= min_questions to make repeated
 * runs idempotent and safely resumable. Throttles between calls to
 * stay polite with the provider and budget-friendly.
 *
 *   php public_html/cron/ai_generate_all.php \
 *       --subject=mathematics --form=4 --min=5 --count=5 --max=20 --critic=1
 *
 *   (no args) — defaults: all subjects, min 5 Qs, generate 5 per topic,
 *               cap 10 topics per run, critic on, throttle 1500ms.
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/ai.php';
require_once __DIR__ . '/../inc/ai_questions.php';

/**
 * Run a batch generation.
 *
 * @param array $opts Options:
 *   - subject_id      (int)    0 = all subjects, or specific subject id
 *   - subject_slug    (string) optional slug shortcut for CLI use
 *   - form_level      (int)    0 = all, 4, or 5
 *   - min_questions   (int)    skip topics that already have >= this many questions
 *   - count_per_topic (int)    questions to generate per processed topic
 *   - difficulty      (string) easy | medium | hard | mixed
 *   - critique        (bool)   run two-pass AI critic
 *   - throttle_ms     (int)    sleep between topics
 *   - max_topics      (int)    hard cap on topics processed per run
 *   - dry_run         (bool)   list what would happen without calling AI
 *   - on_progress     (callable|null)  fn($idx, $total, $topicName, $result) for live updates
 *
 * @return array Summary: processed, skipped, succeeded, failed, inserted, flagged, stopped_reason, log[]
 */
function ai_generate_all_run(array $opts = []): array
{
    $opts = array_merge([
        'subject_id'      => 0,
        'subject_slug'    => '',
        'form_level'      => 0,
        'min_questions'   => 5,
        'count_per_topic' => 5,
        'difficulty'      => 'mixed',
        'critique'        => true,
        'throttle_ms'     => 1500,
        'max_topics'      => 10,
        'dry_run'         => false,
        'on_progress'     => null,
    ], $opts);

    // Resolve subject_slug to id if provided.
    if ($opts['subject_slug'] !== '' && $opts['subject_id'] === 0) {
        $row = db_one('SELECT id FROM subjects WHERE slug = ?', [$opts['subject_slug']]);
        if (!$row) {
            return ['status' => 'error', 'message' => "Subject slug '{$opts['subject_slug']}' not found."];
        }
        $opts['subject_id'] = (int) $row['id'];
    }

    if (!$opts['dry_run'] && !ai_enabled()) {
        return ['status' => 'error', 'message' => 'AI key is not configured. Set it in Admin → AI Settings, or run with --dry to preview.'];
    }

    // Build the topic query.
    $where  = [];
    $params = [];
    if ($opts['subject_id'] > 0) {
        $where[]  = 't.subject_id = ?';
        $params[] = (int) $opts['subject_id'];
    }
    if (in_array((int) $opts['form_level'], [4, 5], true)) {
        $where[]  = 't.form_level = ?';
        $params[] = (int) $opts['form_level'];
    }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $allTopics = db_all(
        "SELECT t.id, t.name, t.form_level, s.name AS subject,
                (SELECT COUNT(*) FROM questions q WHERE q.topic_id = t.id) AS qcount
         FROM topics t JOIN subjects s ON s.id = t.subject_id
         $whereSql
         ORDER BY s.sort_order, s.name, t.form_level, t.sort_order, t.id",
        $params
    );

    $eligible = [];
    $skipped  = 0;
    foreach ($allTopics as $t) {
        if ((int) $t['qcount'] >= (int) $opts['min_questions']) {
            $skipped++;
            continue;
        }
        $eligible[] = $t;
        if (count($eligible) >= (int) $opts['max_topics']) {
            break;
        }
    }

    $log = [];

    if ($opts['dry_run']) {
        foreach ($eligible as $t) {
            $log[] = sprintf('[plan] %s · %s%s · would generate %d Q(s)',
                $t['subject'],
                $t['name'],
                $t['form_level'] ? " (T{$t['form_level']})" : '',
                (int) $opts['count_per_topic']
            );
        }
        return [
            'status'         => 'ok',
            'dry_run'        => true,
            'total_matched'  => count($allTopics),
            'skipped'        => $skipped,
            'planned'        => count($eligible),
            'log'            => $log,
            'stopped_reason' => '',
        ];
    }

    $processed = $succeeded = $failed = 0;
    $totalInserted = $totalFlagged = 0;
    $stopReason = '';

    foreach ($eligible as $idx => $t) {
        // Quota check before each call so we stop cleanly when capped.
        $quota = ai_quota_status(null);
        if (!$quota['ok']) {
            $stopReason = "Quota reached: {$quota['reason']} ({$quota['used_global']}/{$quota['cap_global']}).";
            $log[] = '[stop] ' . $stopReason;
            break;
        }

        $processed++;
        try {
            $r = generate_questions_for_topic(
                (int) $t['id'],
                (int) $opts['count_per_topic'],
                (string) $opts['difficulty'],
                (bool) $opts['critique']
            );
            $inserted = (int) ($r['inserted'] ?? 0);
            $flagged  = (int) ($r['flagged']  ?? 0);
            $totalInserted += $inserted;
            $totalFlagged  += $flagged;

            if ($inserted > 0) {
                $succeeded++;
                $log[] = sprintf('[ok]   %s · %s%s → %d inserted%s',
                    $t['subject'], $t['name'],
                    $t['form_level'] ? " (T{$t['form_level']})" : '',
                    $inserted,
                    $flagged ? " ({$flagged} flagged)" : ''
                );
            } else {
                $failed++;
                $why = !empty($r['errors']) ? $r['errors'][0] : 'no questions inserted';
                $log[] = sprintf('[fail] %s · %s → %s',
                    $t['subject'], $t['name'], mb_substr($why, 0, 200)
                );
            }
        } catch (Throwable $e) {
            $failed++;
            $log[] = sprintf('[crash] %s · %s → %s',
                $t['subject'], $t['name'], $e->getMessage()
            );
        }

        if (is_callable($opts['on_progress'])) {
            ($opts['on_progress'])($idx + 1, count($eligible), $t['name'], $log[count($log) - 1] ?? '');
        }

        // Throttle between calls so we don't slam the provider.
        if ($idx < count($eligible) - 1 && (int) $opts['throttle_ms'] > 0) {
            usleep((int) $opts['throttle_ms'] * 1000);
        }
    }

    return [
        'status'         => 'ok',
        'dry_run'        => false,
        'total_matched'  => count($allTopics),
        'skipped'        => $skipped,
        'planned'        => count($eligible),
        'processed'      => $processed,
        'succeeded'      => $succeeded,
        'failed'         => $failed,
        'inserted'       => $totalInserted,
        'flagged'        => $totalFlagged,
        'stopped_reason' => $stopReason,
        'log'            => $log,
    ];
}

// -------------------------------------------------------------------
// CLI runner
// -------------------------------------------------------------------
if (PHP_SAPI === 'cli') {
    $args = [];
    foreach ($argv as $a) {
        if (preg_match('/^--([a-z_]+)=(.*)$/', $a, $m)) {
            $args[$m[1]] = $m[2];
        }
    }
    $opts = [
        'subject_slug'    => $args['subject'] ?? '',
        'form_level'      => (int) ($args['form']   ?? 0),
        'min_questions'   => (int) ($args['min']    ?? 5),
        'count_per_topic' => (int) ($args['count']  ?? 5),
        'difficulty'      => $args['difficulty'] ?? 'mixed',
        'critique'        => isset($args['critic']) ? (bool) (int) $args['critic'] : true,
        'throttle_ms'     => (int) ($args['throttle'] ?? 1500),
        'max_topics'      => (int) ($args['max']    ?? 10),
        'dry_run'         => isset($args['dry']) ? (bool) (int) $args['dry'] : false,
    ];
    @set_time_limit(0);
    fwrite(STDOUT, "AI batch generation starting…\n");
    foreach ($opts as $k => $v) {
        fwrite(STDOUT, sprintf("  %-15s = %s\n", $k, is_bool($v) ? ($v ? 'true' : 'false') : (string) $v));
    }
    fwrite(STDOUT, str_repeat('-', 50) . "\n");

    $r = ai_generate_all_run($opts);
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }

    foreach ($r['log'] as $line) {
        fwrite(STDOUT, $line . "\n");
    }

    fwrite(STDOUT, str_repeat('-', 50) . "\n");
    fwrite(STDOUT, sprintf(
        "Summary: matched %d, skipped %d (already have >= %d), planned %d, processed %d, succeeded %d, failed %d, inserted %d (flagged %d).%s\n",
        $r['total_matched'], $r['skipped'], (int) $opts['min_questions'],
        $r['planned'], $r['processed'] ?? 0, $r['succeeded'] ?? 0, $r['failed'] ?? 0,
        $r['inserted'] ?? 0, $r['flagged'] ?? 0,
        $r['stopped_reason'] ? "\n  Stopped: " . $r['stopped_reason'] : ''
    ));
}
