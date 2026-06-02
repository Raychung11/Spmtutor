<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/student_layout.php';
require_once __DIR__ . '/../inc/ai_marker.php';

$user = require_role('student');
$uid  = (int) $user['id'];

// Whether the schema is ready (phase31 applied).
$tableReady = (bool) db_one(
    "SELECT 1 AS x FROM information_schema.TABLES
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'essay_submissions'"
);

$result   = null;
$savedId  = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tableReady) {
    csrf_check();
    $taskType  = input('task_type');
    $taskType  = in_array($taskType, ['karangan', 'rumusan', 'tatabahasa'], true) ? $taskType : 'karangan';
    $language  = input('language') === 'en' ? 'en' : 'bm';
    $prompt    = trim(input('prompt'));
    $source    = trim(input('source_passage'));
    $sub       = trim(input('submission'));
    $subjectId = input_int('subject_id') ?: null;

    if ($sub === '') {
        flash('error', 'Please write something to submit.');
    } else {
        @set_time_limit(120);
        if ($taskType === 'karangan') {
            $result = mark_karangan($sub, $prompt, $language);
        } elseif ($taskType === 'rumusan') {
            $result = mark_rumusan($sub, $source);
        } else {
            $result = mark_tatabahasa($sub, $language);
        }
        $savedId = save_submission($uid, [
            'subject_id'     => $subjectId,
            'task_type'      => $taskType,
            'language'       => $language,
            'prompt'         => $prompt,
            'source_passage' => $source,
            'submission'     => $sub,
        ], $result);
    }
}

// Preselect a saved submission to view (from history click).
$viewId = input_int('view');
$view   = null;
if ($viewId && $tableReady) {
    $view = db_one('SELECT * FROM essay_submissions WHERE id = ? AND user_id = ?', [$viewId, $uid]);
    if ($view) {
        $result = [
            'ok'          => $view['status'] === 'marked',
            'word_count'  => (int) ($view['word_count'] ?? 0),
            'score'       => (int) ($view['score'] ?? 0),
            'max_score'   => (int) ($view['max_score'] ?? 0),
            'band'        => (string) ($view['band'] ?? ''),
            'rubric'      => json_decode((string) $view['rubric_json'],      true) ?: [],
            'strengths'   => json_decode((string) $view['strengths_json'],   true) ?: [],
            'weaknesses'  => json_decode((string) $view['weaknesses_json'],  true) ?: [],
            'suggestions' => json_decode((string) $view['suggestions_json'], true) ?: [],
            'errors'      => json_decode((string) $view['errors_json'],      true) ?: [],
            'error'       => $view['error_message'],
        ];
    }
}

$history = [];
if ($tableReady) {
    $history = db_all(
        'SELECT id, task_type, language, score, max_score, band, status, word_count, created_at, prompt
         FROM essay_submissions WHERE user_id = ? ORDER BY id DESC LIMIT 20',
        [$uid]
    );
}

$subjects = db_all('SELECT id, slug, name FROM subjects WHERE slug IN ("bahasa-melayu", "english") ORDER BY name');

student_layout_start('Writing Marker', $user, 'writing.php');
?>
<div class="card" style="margin-bottom:18px">
  <h2 style="margin:0 0 8px">AI Writing Marker</h2>
  <p class="muted" style="margin:0">
    Hantar karangan, rumusan atau ayat untuk semakan tatabahasa. AI memarkahkan mengikut rubrik SPM
    (1103 Bahasa Melayu / 1119 English) dan memberi cadangan untuk meningkatkan markah anda.
  </p>
</div>

<?php if (!$tableReady): ?>
  <div class="flash flash--error">
    The submissions table isn't in the database yet. Ask the admin to run database migrations
    (Admin → Seeders → Run database migrations) first.
  </div>
<?php endif; ?>

<?php if (!ai_enabled()): ?>
  <div class="flash flash--info">
    AI is in demo mode — no API key set. Submissions will not be marked. Ask the admin to configure it under <strong>Admin → AI Settings</strong>.
  </div>
<?php endif; ?>

<div class="grid" style="grid-template-columns: 1fr 320px; gap:18px; align-items:flex-start">
  <div>
    <?php if ($result): ?>
      <div class="card" style="margin-bottom:18px">
        <?php if (!$result['ok']): ?>
          <div class="flash flash--error"><strong>Marking failed:</strong> <?= e($result['error'] ?? 'unknown error') ?></div>
        <?php else: ?>
          <?= render_marking_result($result) ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <h3 style="margin:0 0 12px">New submission</h3>
      <form method="post" id="writeForm">
        <?= csrf_field() ?>
        <div class="grid grid--3">
          <div class="field"><label>Task type</label>
            <select name="task_type" id="taskSel" class="input">
              <option value="karangan">Karangan / Essay</option>
              <option value="rumusan">Rumusan (BM)</option>
              <option value="tatabahasa">Tatabahasa / Grammar check</option>
            </select>
          </div>
          <div class="field"><label>Language</label>
            <select name="language" id="langSel" class="input">
              <option value="bm">Bahasa Melayu</option>
              <option value="en">English</option>
            </select>
          </div>
          <div class="field"><label>Subject (optional)</label>
            <select name="subject_id" class="input">
              <option value="">-- not linked --</option>
              <?php foreach ($subjects as $s): ?>
                <option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="field" id="promptField">
          <label>Soalan / Essay prompt <span class="muted">(optional)</span></label>
          <input class="input" name="prompt" placeholder="contoh: Amalan gaya hidup sihat dalam kalangan remaja.">
        </div>

        <div class="field" id="sourceField" style="display:none">
          <label>Petikan asal <span class="muted">(untuk rumusan)</span></label>
          <textarea name="source_passage" rows="6" placeholder="Tampal petikan asal di sini..."></textarea>
        </div>

        <div class="field">
          <label id="subLabel">Karangan anda</label>
          <textarea name="submission" id="submission" rows="14" required placeholder="Tulis atau tampal karangan anda di sini..." oninput="updateCount()"></textarea>
          <p class="muted" style="font-size:12px;margin:4px 0 0">
            <span id="wc">0</span> perkataan
          </p>
        </div>

        <button class="btn" type="submit" <?= (!$tableReady || !ai_enabled()) ? 'disabled' : '' ?>>
          Submit for AI marking
        </button>
        <span class="muted" style="font-size:12px;margin-left:8px">~10–30 seconds</span>
      </form>
    </div>
  </div>

  <aside>
    <div class="card">
      <h4 style="margin:0 0 10px">Recent submissions</h4>
      <?php if (!$history): ?>
        <p class="muted" style="font-size:13px;margin:0">No submissions yet. Your past work will appear here.</p>
      <?php else: ?>
        <div class="hist-list">
          <?php foreach ($history as $h):
            $isActive = $viewId === (int) $h['id'];
            $scoreText = ($h['score'] !== null && $h['max_score'])
              ? ((int) $h['score'] . ' / ' . (int) $h['max_score'])
              : ($h['status'] === 'failed' ? '✗ failed' : '—');
          ?>
            <a class="hist-item <?= $isActive ? 'hist-item--active' : '' ?>"
               href="<?= url('student/writing.php?view=' . (int) $h['id']) ?>">
              <div class="hist-item__head">
                <span class="badge"><?= e($h['task_type']) ?></span>
                <span class="muted" style="font-size:11px"><?= e(strtoupper($h['language'])) ?></span>
              </div>
              <div class="hist-item__prompt">
                <?= e($h['prompt'] ? mb_substr((string) $h['prompt'], 0, 80) : '(no prompt)') ?>
              </div>
              <div class="hist-item__foot">
                <strong><?= e($scoreText) ?></strong>
                <?php if ($h['band']): ?><span class="muted"> · <?= e($h['band']) ?></span><?php endif; ?>
                <span class="muted" style="font-size:11px;margin-left:auto"><?= e(date('d M', strtotime($h['created_at']))) ?></span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="card" style="margin-top:14px">
      <h4 style="margin:0 0 8px">How to score higher</h4>
      <ul class="muted" style="font-size:13px;line-height:1.6;padding-left:18px;margin:0">
        <li>Tunjukkan 2–3 peribahasa / idioms berkaitan</li>
        <li>Variasi panjang ayat (pendek &amp; panjang)</li>
        <li>Gunakan penanda wacana yang berbeza-beza</li>
        <li>Kembangkan pendahuluan + isi + penutup yang seimbang</li>
        <li>Beri contoh khusus (statistik, tokoh, kejadian)</li>
      </ul>
    </div>
  </aside>
</div>

<style>
.hist-list { display:flex; flex-direction:column; gap:6px; }
.hist-item {
  display:block; padding:10px 12px;
  background:var(--card-2); border:1px solid var(--border); border-radius:8px;
  text-decoration:none; color:inherit;
}
.hist-item:hover { border-color:var(--primary); }
.hist-item--active { border-color:var(--primary); background:rgba(139,92,246,.08); }
.hist-item__head { display:flex; gap:8px; align-items:center; margin-bottom:4px; }
.hist-item__prompt { font-size:13px; line-height:1.4; margin-bottom:6px; }
.hist-item__foot { display:flex; align-items:center; font-size:13px; gap:6px; }

.rubric-grid { display:flex; flex-direction:column; gap:10px; margin:12px 0; }
.rubric-row { display:grid; grid-template-columns: 140px 60px 1fr; gap:10px; align-items:center; padding:8px 10px; background:var(--bg-2); border-radius:8px; }
.rubric-row__bar { flex:1; height:6px; background:var(--card-2); border-radius:999px; overflow:hidden; }
.rubric-row__bar > div { height:100%; background:var(--primary); }

.score-hero { display:flex; align-items:center; gap:20px; margin-bottom:12px; }
.score-hero__num { font-size:48px; font-weight:800; color:var(--primary); line-height:1; }
.score-hero__max { font-size:18px; color:var(--muted); }
.score-hero__band { font-size:14px; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }

.err-list { display:flex; flex-direction:column; gap:6px; }
.err-row {
  padding:8px 12px; background:var(--bg-2); border:1px solid var(--border); border-radius:8px; font-size:13px;
}
.err-row code { color:var(--bad); background:rgba(251,113,133,.1); padding:1px 4px; border-radius:4px; }
.err-row code.fixed { color:var(--good); background:rgba(52,211,153,.1); }

@media (max-width: 880px) {
  .grid[style*="320px"] { grid-template-columns: 1fr !important; }
}
</style>

<script>
function updateCount() {
  var t = document.getElementById('submission').value.trim();
  var n = t === '' ? 0 : t.split(/\s+/).length;
  document.getElementById('wc').textContent = n;
}
(function () {
  var task = document.getElementById('taskSel');
  var lang = document.getElementById('langSel');
  var promptField = document.getElementById('promptField');
  var sourceField = document.getElementById('sourceField');
  var subLabel = document.getElementById('subLabel');

  function refresh() {
    var t = task.value, l = lang.value;
    if (t === 'rumusan') {
      sourceField.style.display = '';
      promptField.style.display = '';
      subLabel.textContent = 'Rumusan anda (~120 perkataan)';
    } else if (t === 'tatabahasa') {
      sourceField.style.display = 'none';
      promptField.style.display = 'none';
      subLabel.textContent = l === 'en' ? 'Sentence(s) to check' : 'Ayat untuk disemak';
    } else {
      sourceField.style.display = 'none';
      promptField.style.display = '';
      subLabel.textContent = l === 'en' ? 'Your essay' : 'Karangan anda';
    }
  }
  task.addEventListener('change', refresh);
  lang.addEventListener('change', refresh);
  refresh();
  updateCount();
})();
</script>

<?php
student_layout_end();

/** Renders the marking result block. */
function render_marking_result(array $r): string
{
    ob_start();
    $hasRubric = !empty($r['rubric']);
    $score   = (int) ($r['score'] ?? 0);
    $maxScore = (int) ($r['max_score'] ?? 0);
    ?>
    <div class="score-hero">
      <div>
        <div class="score-hero__num"><?= $score ?><span class="score-hero__max"> / <?= $maxScore ?></span></div>
        <?php if (!empty($r['band'])): ?>
          <div class="score-hero__band"><?= e((string) $r['band']) ?></div>
        <?php endif; ?>
      </div>
      <div class="muted" style="font-size:13px">
        Word count: <strong><?= (int) ($r['word_count'] ?? 0) ?></strong><br>
        AI marked using SPM rubric.
      </div>
    </div>

    <?php if ($hasRubric): ?>
      <h4 style="margin:18px 0 6px;font-size:13px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)">Breakdown</h4>
      <div class="rubric-grid">
        <?php foreach ($r['rubric'] as $key => $b):
          $s = (int) ($b['score'] ?? 0);
          $m = max(1, (int) ($b['max'] ?? 0));
          $pct = (int) round(($s / $m) * 100);
          $label = ucwords(str_replace('_', ' ', (string) $key));
        ?>
          <div class="rubric-row">
            <div><strong><?= e($label) ?></strong></div>
            <div><?= $s ?> / <?= $m ?></div>
            <div>
              <div class="rubric-row__bar"><div style="width:<?= $pct ?>%"></div></div>
              <?php if (!empty($b['comment'])): ?>
                <div class="muted" style="font-size:12px;margin-top:4px"><?= e((string) $b['comment']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($r['strengths']) || !empty($r['weaknesses']) || !empty($r['suggestions'])): ?>
      <div class="grid grid--3" style="margin-top:14px">
        <?php if (!empty($r['strengths'])): ?>
          <div>
            <h4 style="margin:0 0 6px;font-size:13px;color:var(--good);text-transform:uppercase;letter-spacing:.06em">✓ Strengths</h4>
            <ul style="padding-left:18px;margin:0;font-size:13px;line-height:1.5">
              <?php foreach ($r['strengths'] as $s): ?><li><?= e((string) $s) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <?php if (!empty($r['weaknesses'])): ?>
          <div>
            <h4 style="margin:0 0 6px;font-size:13px;color:var(--bad);text-transform:uppercase;letter-spacing:.06em">✗ Weaknesses</h4>
            <ul style="padding-left:18px;margin:0;font-size:13px;line-height:1.5">
              <?php foreach ($r['weaknesses'] as $s): ?><li><?= e((string) $s) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <?php if (!empty($r['suggestions'])): ?>
          <div>
            <h4 style="margin:0 0 6px;font-size:13px;color:var(--primary);text-transform:uppercase;letter-spacing:.06em">→ Cadangan</h4>
            <ul style="padding-left:18px;margin:0;font-size:13px;line-height:1.5">
              <?php foreach ($r['suggestions'] as $s): ?><li><?= e((string) $s) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($r['errors'])): ?>
      <h4 style="margin:18px 0 6px;font-size:13px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)">Errors found (<?= count($r['errors']) ?>)</h4>
      <div class="err-list">
        <?php foreach ($r['errors'] as $err): ?>
          <div class="err-row">
            <code><?= e((string) ($err['original'] ?? '')) ?></code>
            → <code class="fixed"><?= e((string) ($err['corrected'] ?? '')) ?></code>
            <?php if (!empty($err['rule'])): ?>
              <div class="muted" style="font-size:12px;margin-top:4px"><?= e((string) $err['rule']) ?>
              <?php if (!empty($err['type'])): ?> · <em><?= e((string) $err['type']) ?></em><?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php
    return (string) ob_get_clean();
}
