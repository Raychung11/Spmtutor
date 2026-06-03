<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/student_layout.php';
require_once __DIR__ . '/../inc/ai_sandbox.php';

$user = require_role('student');
$uid  = (int) $user['id'];

$tableReady = (bool) db_one(
    "SELECT 1 AS x FROM information_schema.TABLES
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sandbox_runs'"
);

$availableModels = sandbox_available_models();
$result = null;
$savedId = 0;

// Handle a vote submission on an existing run.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && input('action') === 'vote' && $tableReady) {
    csrf_check();
    $runId = input_int('run_id');
    $vote  = input('vote');
    $notes = trim(input('notes'));
    if ($runId && sandbox_record_vote($runId, $uid, $vote, $notes)) {
        flash('success', 'Vote recorded — thank you for the feedback.');
    }
    redirect('student/sandbox.php?view=' . $runId);
}

// Run a comparison.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && input('action') === 'compare' && $tableReady) {
    csrf_check();

    $systemPrompt = trim(input('system_prompt'));
    $userPrompt   = trim(input('user_prompt'));
    $temperature  = (float) (input('temperature') ?: '0.4');
    $temperature  = max(0.0, min(1.5, $temperature));
    $subjectId    = input_int('subject_id') ?: null;

    $pickA = input('pick_a');
    $pickB = input('pick_b');

    if ($userPrompt === '') {
        flash('error', 'Please write a user prompt before comparing.');
    } elseif (!isset($availableModels[$pickA]) || !isset($availableModels[$pickB])) {
        flash('error', 'Pick two available models from the dropdowns.');
    } else {
        [$providerA, $modelA] = explode(':', $pickA, 2);
        [$providerB, $modelB] = explode(':', $pickB, 2);

        @set_time_limit(180);
        $a = sandbox_call($providerA, $modelA, $systemPrompt, $userPrompt, $temperature);
        $b = sandbox_call($providerB, $modelB, $systemPrompt, $userPrompt, $temperature);
        ai_log_usage($uid, 'sandbox');
        ai_log_usage($uid, 'sandbox');

        $savedId = sandbox_save_run($uid, $subjectId, $systemPrompt, $userPrompt, $temperature, $providerA, $modelA, $a, $providerB, $modelB, $b);
        $result = [
            'id' => $savedId,
            'temperature' => $temperature,
            'system_prompt' => $systemPrompt,
            'user_prompt'   => $userPrompt,
            'a' => array_merge($a, ['provider' => $providerA, 'model' => $modelA]),
            'b' => array_merge($b, ['provider' => $providerB, 'model' => $modelB]),
            'vote' => null,
            'notes' => '',
        ];
    }
}

// Restore a run from history.
$viewId = input_int('view');
if ($viewId && $tableReady && !$result) {
    $row = db_one('SELECT * FROM sandbox_runs WHERE id = ? AND user_id = ?', [$viewId, $uid]);
    if ($row) {
        $result = [
            'id' => (int) $row['id'],
            'temperature' => (float) $row['temperature'],
            'system_prompt' => (string) $row['system_prompt'],
            'user_prompt' => (string) $row['user_prompt'],
            'a' => ['reply' => (string) $row['reply_a'], 'ms' => (int) $row['ms_a'], 'error' => $row['error_a'], 'provider' => (string) $row['provider_a'], 'model' => (string) $row['model_a']],
            'b' => ['reply' => (string) $row['reply_b'], 'ms' => (int) $row['ms_b'], 'error' => $row['error_b'], 'provider' => (string) $row['provider_b'], 'model' => (string) $row['model_b']],
            'vote' => $row['vote'],
            'notes' => (string) ($row['notes'] ?? ''),
        ];
    }
}

$history = [];
if ($tableReady) {
    $history = db_all(
        'SELECT id, user_prompt, model_a, model_b, vote, created_at
         FROM sandbox_runs WHERE user_id = ? AND status = "active"
         ORDER BY id DESC LIMIT 20',
        [$uid]
    );
}

$subjects = db_all('SELECT id, slug, name FROM subjects WHERE slug = "kepintaran-buatan" ORDER BY name');

student_layout_start('AI Sandbox', $user, 'sandbox.php');
?>
<div class="card" style="margin-bottom:18px">
  <h2 style="margin:0 0 8px">🧪 AI Sandbox — LLM Compare</h2>
  <p class="muted" style="margin:0">
    Send the same prompt to two different models side-by-side and see how they differ in
    style, accuracy, length, and reasoning. A practical tool for the Asas Kepintaran Buatan
    elective — choose a model A and B, write a prompt, and vote on which response is better.
  </p>
</div>

<?php if (!$tableReady): ?>
  <div class="flash flash--error">
    The sandbox_runs table isn't in the database yet. Ask the admin to run database migrations
    (Admin → Seeders → Run database migrations) first.
  </div>
<?php endif; ?>

<?php if (!$availableModels): ?>
  <div class="flash flash--info">
    No AI provider is configured. Ask the admin to set <strong>Admin → AI Settings → API Key</strong>
    (and optionally <code>ai_api_key_alt</code> / <code>ai_provider_alt</code> in site_settings to
    enable cross-provider comparisons like Claude vs GPT).
  </div>
<?php endif; ?>

<div class="grid" style="grid-template-columns: 1fr 320px; gap:18px; align-items:flex-start">
  <div>
    <?php if ($result): ?>
      <div class="card" style="margin-bottom:18px">
        <div class="sb-prompt-summary">
          <?php if ($result['system_prompt'] !== ''): ?>
            <details><summary class="muted" style="font-size:12px;cursor:pointer">System prompt</summary>
              <pre class="sb-pre"><?= e($result['system_prompt']) ?></pre>
            </details>
          <?php endif; ?>
          <details open><summary class="muted" style="font-size:12px;cursor:pointer">User prompt</summary>
            <pre class="sb-pre"><?= e($result['user_prompt']) ?></pre>
          </details>
          <p class="muted" style="font-size:12px;margin:4px 0 0">Temperature: <?= e(number_format((float) $result['temperature'], 2)) ?></p>
        </div>

        <div class="sb-cols">
          <?php foreach (['a' => 'A', 'b' => 'B'] as $k => $letter):
            $r = $result[$k];
            $ms = (int) ($r['ms'] ?? 0);
            $providerLabel = ucfirst((string) ($r['provider'] ?? ''));
            $modelLabel = (string) ($r['model'] ?? '');
          ?>
            <div class="sb-col">
              <div class="sb-col__head">
                <span class="sb-letter"><?= $letter ?></span>
                <div>
                  <strong><?= e($modelLabel) ?></strong>
                  <div class="muted" style="font-size:11px"><?= e($providerLabel) ?> · <?= $ms ?> ms · <?= number_format(mb_strlen((string) ($r['reply'] ?? ''))) ?> chars</div>
                </div>
              </div>
              <?php if (!empty($r['error'])): ?>
                <div class="flash flash--error" style="font-size:13px"><strong>Error:</strong> <?= e((string) $r['error']) ?></div>
              <?php else: ?>
                <pre class="sb-reply"><?= e((string) ($r['reply'] ?? '')) ?></pre>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if (empty($result['a']['error']) && empty($result['b']['error']) && $result['id']): ?>
          <form method="post" class="sb-vote">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="vote">
            <input type="hidden" name="run_id" value="<?= (int) $result['id'] ?>">
            <h4 style="margin:0 0 8px;font-size:13px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)">Which response is better?</h4>
            <div class="sb-vote__btns">
              <?php
                $votes = [
                    'a'       => 'A wins',
                    'tie'     => 'Tie',
                    'b'       => 'B wins',
                    'neither' => 'Neither',
                ];
                foreach ($votes as $v => $label):
                  $selected = ($result['vote'] ?? '') === $v;
              ?>
                <button class="btn btn--sm <?= $selected ? '' : 'btn--ghost' ?>" type="submit" name="vote" value="<?= e($v) ?>">
                  <?= e($label) ?><?= $selected ? ' ✓' : '' ?>
                </button>
              <?php endforeach; ?>
            </div>
            <textarea name="notes" class="input" rows="2" placeholder="Why? (optional — what was better/worse about each response)" style="margin-top:8px"><?= e((string) ($result['notes'] ?? '')) ?></textarea>
          </form>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <h3 style="margin:0 0 12px">New comparison</h3>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="compare">

        <div class="grid grid--3">
          <div class="field">
            <label>Model A</label>
            <select name="pick_a" class="input">
              <?php foreach ($availableModels as $key => $label): ?>
                <option value="<?= e($key) ?>"><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label>Model B</label>
            <select name="pick_b" class="input">
              <?php
                $i = 0;
                foreach ($availableModels as $key => $label):
                  $default = ($i++ === 1);
              ?>
                <option value="<?= e($key) ?>" <?= $default ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label>Temperature</label>
            <input class="input" type="number" step="0.1" min="0" max="1.5" name="temperature" value="0.4">
          </div>
        </div>

        <div class="field">
          <label>System prompt <span class="muted">(optional — sets the AI's persona)</span></label>
          <textarea name="system_prompt" class="input" rows="3" placeholder="e.g. You are an SPM tutor. Reply in Bahasa Melayu."></textarea>
        </div>

        <div class="field">
          <label>User prompt <span class="muted">(what you want the AI to do)</span></label>
          <textarea name="user_prompt" class="input" rows="8" required placeholder="e.g. Terangkan teori sel kepada pelajar Tingkatan 4 dalam 3 perenggan ringkas."></textarea>
        </div>

        <?php if ($subjects): ?>
          <input type="hidden" name="subject_id" value="<?= (int) $subjects[0]['id'] ?>">
        <?php endif; ?>

        <button class="btn" type="submit" <?= (!$tableReady || !$availableModels) ? 'disabled' : '' ?>>
          Compare side-by-side
        </button>
        <span class="muted" style="font-size:12px;margin-left:8px">~5-30 seconds per model</span>
      </form>
    </div>

    <div class="card" style="margin-top:14px">
      <h4 style="margin:0 0 8px">💡 Ideas to explore</h4>
      <ul class="muted" style="font-size:13px;line-height:1.6;padding-left:18px;margin:0">
        <li>Ask the same factual question in BM and English — which model is more accurate in each?</li>
        <li>Test "Tulis pantun 4 baris bertemakan kemerdekaan" — how does each model handle Malay literary forms?</li>
        <li>Try a tricky reasoning problem: "Jika 3/8 daripada 24 pelajar adalah perempuan, berapakah bilangan perempuan?"</li>
        <li>Compare with and without "Fikir langkah demi langkah" (chain-of-thought) — see how the answer changes.</li>
        <li>Test hallucination: ask about a niche topic and check both replies against Wikipedia.</li>
      </ul>
    </div>
  </div>

  <aside>
    <div class="card">
      <h4 style="margin:0 0 10px">Recent comparisons</h4>
      <?php if (!$history): ?>
        <p class="muted" style="font-size:13px;margin:0">No comparisons yet. Your experiments will appear here.</p>
      <?php else: ?>
        <div class="hist-list">
          <?php foreach ($history as $h):
            $isActive = $viewId === (int) $h['id'];
            $voteLabel = $h['vote'] ? strtoupper((string) $h['vote']) : '—';
          ?>
            <a class="hist-item <?= $isActive ? 'hist-item--active' : '' ?>"
               href="<?= url('student/sandbox.php?view=' . (int) $h['id']) ?>">
              <div class="hist-item__head">
                <span class="badge"><?= e((string) $h['model_a']) ?></span>
                <span class="muted" style="font-size:11px">vs</span>
                <span class="badge"><?= e((string) $h['model_b']) ?></span>
              </div>
              <div class="hist-item__prompt">
                <?= e(mb_substr((string) $h['user_prompt'], 0, 80)) ?>
              </div>
              <div class="hist-item__foot">
                <strong>Vote: <?= e($voteLabel) ?></strong>
                <span class="muted" style="font-size:11px;margin-left:auto"><?= e(date('d M', strtotime((string) $h['created_at']))) ?></span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </aside>
</div>

<style>
.sb-pre {
  margin:6px 0 0; padding:10px 12px;
  background:var(--bg-2); border:1px solid var(--border); border-radius:6px;
  font-size:12px; line-height:1.5; white-space:pre-wrap; word-break:break-word;
  font-family:inherit; max-height:160px; overflow:auto;
}
.sb-cols { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin:14px 0 4px; }
.sb-col {
  background:var(--bg-2); border:1px solid var(--border); border-radius:10px; padding:12px;
}
.sb-col__head { display:flex; gap:10px; align-items:center; margin-bottom:8px; }
.sb-letter {
  display:inline-flex; align-items:center; justify-content:center;
  width:28px; height:28px; border-radius:999px;
  background:var(--primary); color:#fff; font-weight:700; font-size:13px;
}
.sb-reply {
  margin:0; padding:10px 12px;
  background:var(--card-2); border:1px solid var(--border); border-radius:8px;
  font-size:13px; line-height:1.55; white-space:pre-wrap; word-break:break-word;
  font-family:inherit; max-height:480px; overflow:auto;
}
.sb-prompt-summary { margin-bottom:8px; display:flex; flex-direction:column; gap:8px; }
.sb-vote { margin-top:14px; padding-top:12px; border-top:1px dashed var(--border); }
.sb-vote__btns { display:flex; gap:8px; flex-wrap:wrap; }

.hist-list { display:flex; flex-direction:column; gap:6px; }
.hist-item {
  display:block; padding:10px 12px;
  background:var(--card-2); border:1px solid var(--border); border-radius:8px;
  text-decoration:none; color:inherit;
}
.hist-item:hover { border-color:var(--primary); }
.hist-item--active { border-color:var(--primary); background:rgba(139,92,246,.08); }
.hist-item__head { display:flex; gap:6px; align-items:center; margin-bottom:4px; flex-wrap:wrap; }
.hist-item__prompt { font-size:13px; line-height:1.4; margin-bottom:6px; }
.hist-item__foot { display:flex; align-items:center; font-size:12px; gap:6px; }

@media (max-width: 980px) {
  .sb-cols { grid-template-columns: 1fr; }
  .grid[style*="320px"] { grid-template-columns: 1fr !important; }
}
</style>

<?php
student_layout_end();
