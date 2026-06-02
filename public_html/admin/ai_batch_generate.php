<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';
require_once __DIR__ . '/../inc/ai.php';
require_once __DIR__ . '/../cron/ai_generate_all.php';

$admin = require_role('admin');

$subjects = db_all('SELECT id, name FROM subjects ORDER BY sort_order, name');

// Per-subject question coverage stats — what's been done, what's left.
$coverage = db_all("
    SELECT s.id, s.name,
           COUNT(t.id) AS total_topics,
           SUM(CASE WHEN q.qc >= 5 THEN 1 ELSE 0 END) AS covered_topics,
           COALESCE(SUM(q.qc), 0) AS total_questions
    FROM subjects s
    LEFT JOIN topics t ON t.subject_id = s.id
    LEFT JOIN (SELECT topic_id, COUNT(*) AS qc FROM questions GROUP BY topic_id) q ON q.topic_id = t.id
    GROUP BY s.id, s.name, s.sort_order
    HAVING total_topics > 0
    ORDER BY s.sort_order, s.name
");

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    @set_time_limit(0);

    $opts = [
        'subject_id'      => input_int('subject_id'),
        'form_level'      => input_int('form_level'),
        'min_questions'   => max(0, min(50, input_int('min_questions', 5))),
        'count_per_topic' => max(1, min(20, input_int('count_per_topic', 5))),
        'difficulty'      => in_array(input('difficulty'), ['mixed', 'easy', 'medium', 'hard'], true) ? input('difficulty') : 'mixed',
        'critique'        => input('critique') === '1',
        'throttle_ms'     => max(0, min(10000, input_int('throttle_ms', 1500))),
        'max_topics'      => max(1, min(200, input_int('max_topics', 10))),
        'dry_run'         => input('dry_run') === '1',
    ];
    $result = ai_generate_all_run($opts);
}

$quota = ai_quota_status((int) $admin['id']);

admin_layout_start('AI Batch Generation', $admin, 'ai_batch_generate.php');
?>
<div class="card" style="margin-bottom:18px">
  <h2 style="margin:0 0 8px">Batch question generation</h2>
  <p class="muted" style="margin:0">
    Generate questions for many topics in one run. Topics that already have <em>at least</em>
    the minimum questions are skipped — re-running picks up where you left off.
    For runs over 30 topics, schedule via cron (see snippet below) so it doesn't tie up your browser.
  </p>
</div>

<?php if (!ai_enabled()): ?>
  <div class="flash flash--error">
    AI key is not configured. Set it under <a href="<?= url('admin/ai_settings.php') ?>">Admin → AI Settings</a> before running a live batch
    (you can still run with "dry run" to preview).
  </div>
<?php endif; ?>

<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0">Per-subject coverage</h3>
  <p class="muted" style="font-size:13px;margin:0 0 12px">
    Today's AI usage: <strong><?= $quota['used_global'] ?></strong> of <?= $quota['cap_global'] ?> calls
    platform-wide (you personally <?= $quota['used_user'] ?>/<?= $quota['cap_user'] ?>).
  </p>
  <table class="table">
    <thead>
      <tr><th>Subject</th><th style="width:120px">Topics</th><th style="width:160px">Covered (≥5 Qs)</th><th style="width:140px">Total Qs</th><th>Coverage</th></tr>
    </thead>
    <tbody>
      <?php foreach ($coverage as $row):
        $total = max(1, (int) $row['total_topics']);
        $covered = (int) $row['covered_topics'];
        $pct = (int) round(($covered / $total) * 100);
        $colour = $pct >= 80 ? 'var(--good)' : ($pct >= 40 ? 'var(--warn)' : 'var(--bad)');
      ?>
        <tr>
          <td><?= e($row['name']) ?></td>
          <td><?= $total ?></td>
          <td><?= $covered ?></td>
          <td><?= (int) $row['total_questions'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <div style="flex:1;height:6px;background:var(--card-2);border-radius:999px;overflow:hidden">
                <div style="width:<?= $pct ?>%;height:100%;background:<?= $colour ?>"></div>
              </div>
              <span class="muted" style="font-size:12px;min-width:36px;text-align:right"><?= $pct ?>%</span>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0">Configure the batch</h3>
  <form method="post">
    <?= csrf_field() ?>
    <div class="grid grid--3">
      <div class="field"><label>Subject</label>
        <select name="subject_id" class="input">
          <option value="0">All subjects</option>
          <?php foreach ($subjects as $s): ?>
            <option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Form level</label>
        <select name="form_level" class="input">
          <option value="0">All forms</option>
          <option value="4">Form 4 only</option>
          <option value="5">Form 5 only</option>
        </select>
      </div>
      <div class="field"><label>Skip topics with ≥ this many Qs</label>
        <input class="input" type="number" name="min_questions" min="0" max="50" value="5">
      </div>
    </div>
    <div class="grid grid--3">
      <div class="field"><label>Questions per topic</label>
        <input class="input" type="number" name="count_per_topic" min="1" max="20" value="5">
      </div>
      <div class="field"><label>Difficulty mix</label>
        <select name="difficulty" class="input">
          <option value="mixed">Mixed (easy / medium / hard)</option>
          <option value="easy">All easy</option>
          <option value="medium">All medium</option>
          <option value="hard">All hard</option>
        </select>
      </div>
      <div class="field"><label>AI critic (2-pass)</label>
        <select name="critique" class="input">
          <option value="1" selected>On (safer, ~2× slower)</option>
          <option value="0">Off (faster, no flagging)</option>
        </select>
      </div>
    </div>
    <div class="grid grid--3">
      <div class="field"><label>Max topics per run</label>
        <input class="input" type="number" name="max_topics" min="1" max="200" value="10">
        <p class="muted" style="font-size:12px;margin:4px 0 0">Hard safety cap. Use cron for big batches.</p>
      </div>
      <div class="field"><label>Throttle between topics (ms)</label>
        <input class="input" type="number" name="throttle_ms" min="0" max="10000" step="100" value="1500">
        <p class="muted" style="font-size:12px;margin:4px 0 0">Sleep between AI calls — stays polite to provider.</p>
      </div>
      <div class="field"><label>Dry run</label>
        <select name="dry_run" class="input">
          <option value="0">No — generate for real</option>
          <option value="1">Yes — just list what would happen</option>
        </select>
      </div>
    </div>
    <button class="btn" type="submit">Run batch</button>
    <a class="btn btn--ghost" href="<?= url('admin/ai_batch_generate.php') ?>">Reset</a>
  </form>
</div>

<?php if ($result): ?>
  <div class="card" style="margin-bottom:18px">
    <h3 style="margin-top:0">Run result</h3>
    <?php if (($result['status'] ?? '') !== 'ok'): ?>
      <div class="flash flash--error"><?= e($result['message'] ?? 'Unknown error.') ?></div>
    <?php else: ?>
      <div class="grid grid--4" style="margin-bottom:12px">
        <div class="stat"><div class="stat__label">Matched</div><div class="stat__value"><?= (int) $result['total_matched'] ?></div></div>
        <div class="stat"><div class="stat__label">Skipped (already covered)</div><div class="stat__value"><?= (int) $result['skipped'] ?></div></div>
        <div class="stat"><div class="stat__label"><?= !empty($result['dry_run']) ? 'Planned' : 'Processed' ?></div><div class="stat__value"><?= (int) ($result['dry_run'] ? $result['planned'] : ($result['processed'] ?? 0)) ?></div></div>
        <div class="stat"><div class="stat__label">Questions inserted</div><div class="stat__value"><?= (int) ($result['inserted'] ?? 0) ?></div></div>
      </div>
      <?php if (!empty($result['stopped_reason'])): ?>
        <div class="flash flash--info"><strong>Stopped early:</strong> <?= e($result['stopped_reason']) ?></div>
      <?php endif; ?>
      <?php if (($result['failed'] ?? 0) > 0): ?>
        <p class="muted" style="font-size:13px">
          Succeeded: <strong><?= (int) $result['succeeded'] ?></strong> ·
          Failed: <strong><?= (int) $result['failed'] ?></strong> ·
          Flagged by critic: <strong><?= (int) $result['flagged'] ?></strong>
        </p>
      <?php endif; ?>
      <details open>
        <summary style="cursor:pointer;font-weight:600;margin-bottom:8px">Per-topic log (<?= count($result['log']) ?> lines)</summary>
        <pre style="white-space:pre-wrap;font-family:monospace;font-size:12px;background:var(--bg-2);padding:14px;border-radius:10px;border:1px solid var(--border);max-height:420px;overflow:auto;margin:0"><?php
          foreach ($result['log'] as $line) {
              echo e($line) . "\n";
          }
        ?></pre>
      </details>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="card">
  <h3 style="margin-top:0">CLI usage (overnight cron)</h3>
  <p class="muted" style="font-size:13px">
    Run the same generator from SSH or a Hostinger scheduled cron job — no browser timeout, no max_topics cap:
  </p>
  <pre style="white-space:pre-wrap;font-family:monospace;font-size:12px;background:var(--bg-2);padding:14px;border-radius:10px;border:1px solid var(--border);margin:0">
# Run every night at 2am: generate 5 questions per uncovered topic, up to 100 topics
0 2 * * *  /usr/bin/php /home/USER/public_html/cron/ai_generate_all.php --max=100 --count=5 --critic=1

# One subject only:
php public_html/cron/ai_generate_all.php --subject=mathematics --form=4 --max=30

# Preview without calling AI:
php public_html/cron/ai_generate_all.php --dry=1 --max=200
</pre>
</div>

<style>
.stat { background: var(--card-2); border:1px solid var(--border); border-radius:10px; padding:14px; }
.stat__label { font-size:12px; color: var(--muted); }
.stat__value { font-size:24px; font-weight:700; margin-top:4px; }
</style>
<?php
admin_layout_end();
