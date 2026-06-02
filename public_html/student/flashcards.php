<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/student_layout.php';
require_once __DIR__ . '/../inc/reference_libraries.php';
require_once __DIR__ . '/../inc/flashcard_progress.php';

$user = require_login();
$uid  = (int) $user['id'];

$libSlug = input('lib');
$lib     = $libSlug ? reference_library_by_slug($libSlug) : null;

// ---- AJAX endpoint: persist a single card action ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && input('ajax') === '1') {
    header('Content-Type: application/json; charset=utf-8');
    if (!$lib || empty($lib['flashcard'])) {
        echo json_encode(['ok' => false, 'error' => 'invalid library']);
        return;
    }
    csrf_check();
    $cardId = input_int('card_id');
    $action = input('act');
    if ($cardId <= 0) {
        echo json_encode(['ok' => false, 'error' => 'card_id required']);
        return;
    }
    $res = fcp_apply($uid, $lib['slug'], $cardId, $action);
    echo json_encode($res);
    return;
}

if (!$lib || empty($lib['flashcard'])) {
    flash('error', 'No flashcard library selected — pick one from the Library page.');
    redirect('student/library.php');
}
if (!reference_library_available($lib)) {
    flash('error', 'This library is not yet seeded. Ask the admin to run migrations and seeders first.');
    redirect('student/library.php');
}

$mode = in_array(input('mode'), ['due', 'unseen', 'all'], true) ? input('mode') : 'due';
$tag  = trim(input('tag'));

// Filters mirror library.php so users can drill in further.
$where  = ['1=1'];
$params = [];
if ($tag !== '' && !empty($lib['tag_col'])) {
    $where[]  = '`' . $lib['tag_col'] . '` = ?';
    $params[] = $tag;
}

try {
    $allRows = db_all(
        "SELECT * FROM `{$lib['table']}` WHERE " . implode(' AND ', $where) . " AND status = 'active' ORDER BY sort_order, id LIMIT 500",
        $params
    );
} catch (Throwable $e) {
    $allRows = db_all(
        "SELECT * FROM `{$lib['table']}` WHERE " . implode(' AND ', $where) . " ORDER BY sort_order, id LIMIT 500",
        $params
    );
}
$totalAll = count($allRows);

// Load progress and partition rows by mode.
$progress = fcp_load($uid, $lib['slug']);
$summary  = fcp_summary($uid, $lib['slug'], $totalAll);

$filtered = [];
foreach ($allRows as $r) {
    $cid = (int) $r['id'];
    $p   = $progress[$cid] ?? null;
    $isNew = $p === null;
    $isDue = $isNew || ($p['next_due_at'] && strtotime($p['next_due_at']) <= time());

    if ($mode === 'due'    && !$isDue) continue;
    if ($mode === 'unseen' && !$isNew) continue;
    $filtered[] = $r;
}

// Shuffle once on the server so each visit gives a fresh order.
mt_srand((int) (microtime(true) * 1000) % 0x7FFFFFFF);
shuffle($filtered);

$frontCol = $lib['flashcard']['front'];
$backCol  = $lib['flashcard']['back'];
$extraCol = $lib['flashcard']['back_extra'] ?? null;

$cards = [];
foreach ($filtered as $r) {
    $front = trim((string) ($r[$frontCol] ?? ''));
    $back  = trim((string) ($r[$backCol]  ?? ''));
    $extra = $extraCol ? trim((string) ($r[$extraCol] ?? '')) : '';
    if ($front === '' || $back === '') {
        continue;
    }
    $cards[] = [
        'id'    => (int) $r['id'],
        'front' => $front,
        'back'  => $back,
        'extra' => $extra,
    ];
}

$tags = [];
if (!empty($lib['tag_col'])) {
    try {
        $tagRows = db_all(
            "SELECT DISTINCT `{$lib['tag_col']}` AS t FROM `{$lib['table']}` WHERE `{$lib['tag_col']}` IS NOT NULL AND `{$lib['tag_col']}` <> '' ORDER BY t LIMIT 30"
        );
        $tags = array_filter(array_column($tagRows, 't'));
    } catch (Throwable $e) {}
}

$isRtl = ($lib['language'] ?? 'en') === 'ar';
$tableReady = fcp_table_ready();

student_layout_start('Flashcards · ' . $lib['label'], $user, 'library.php');
?>
<div class="card" style="margin-bottom:18px">
  <p style="margin:0 0 8px">
    <a href="<?= url('student/library.php?lib=' . urlencode($lib['slug'])) ?>" class="muted">← Browse <?= e($lib['label']) ?></a>
  </p>
  <h2 style="margin:0 0 6px">Flashcards · <?= e($lib['label']) ?></h2>
  <p class="muted" style="margin:0"><?= e($lib['description']) ?></p>
</div>

<?php if (!$tableReady): ?>
  <div class="flash flash--info">
    Spaced-repetition tracking isn't enabled yet — ask the admin to run database migrations
    (<strong>Admin → Seeders → Run database migrations</strong>) so your progress can be saved between sessions.
  </div>
<?php endif; ?>

<div class="card" style="margin-bottom:18px">
  <div class="grid grid--4" style="gap:10px;margin-bottom:14px">
    <div class="fc-stat"><div class="fc-stat__label">Total</div><div class="fc-stat__num"><?= $summary['total'] ?></div></div>
    <div class="fc-stat fc-stat--due"><div class="fc-stat__label">Due now</div><div class="fc-stat__num"><?= $summary['due'] + $summary['new'] ?></div></div>
    <div class="fc-stat fc-stat--new"><div class="fc-stat__label">Unseen</div><div class="fc-stat__num"><?= $summary['new'] ?></div></div>
    <div class="fc-stat fc-stat--done"><div class="fc-stat__label">Scheduled</div><div class="fc-stat__num"><?= $summary['done'] ?></div></div>
  </div>

  <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px">
    <span class="muted" style="font-size:13px;align-self:center;margin-right:6px">Mode:</span>
    <a class="btn btn--sm <?= $mode === 'due'    ? '' : 'btn--ghost' ?>" href="<?= url('student/flashcards.php?lib=' . urlencode($lib['slug']) . '&mode=due' . ($tag ? '&tag=' . urlencode($tag) : '')) ?>">Due today</a>
    <a class="btn btn--sm <?= $mode === 'unseen' ? '' : 'btn--ghost' ?>" href="<?= url('student/flashcards.php?lib=' . urlencode($lib['slug']) . '&mode=unseen' . ($tag ? '&tag=' . urlencode($tag) : '')) ?>">Unseen only</a>
    <a class="btn btn--sm <?= $mode === 'all'    ? '' : 'btn--ghost' ?>" href="<?= url('student/flashcards.php?lib=' . urlencode($lib['slug']) . '&mode=all' . ($tag ? '&tag=' . urlencode($tag) : '')) ?>">All cards</a>
  </div>

  <?php if ($tags): ?>
    <div style="display:flex;flex-wrap:wrap;gap:6px">
      <span class="muted" style="font-size:13px;align-self:center;margin-right:6px">Filter:</span>
      <a class="chip" href="<?= url('student/flashcards.php?lib=' . urlencode($lib['slug']) . '&mode=' . $mode) ?>"
         style="<?= $tag === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">All</a>
      <?php foreach ($tags as $t): ?>
        <a class="chip" href="<?= url('student/flashcards.php?lib=' . urlencode($lib['slug']) . '&mode=' . $mode . '&tag=' . urlencode($t)) ?>"
           style="<?= $tag === $t ? 'border-color:var(--primary);color:var(--primary)' : '' ?>"><?= e($t) ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php if (!$cards): ?>
  <div class="card">
    <p class="muted">
      <?php if ($mode === 'due'): ?>
        Nothing due right now — all your cards in this library are scheduled for the future. Switch to <strong>Unseen only</strong> or <strong>All cards</strong> to keep studying.
      <?php elseif ($mode === 'unseen'): ?>
        You've seen every card in this library at least once. Switch to <strong>Due today</strong> or <strong>All cards</strong>.
      <?php else: ?>
        No cards match. Try clearing the filter.
      <?php endif; ?>
    </p>
  </div>
<?php else: ?>
<div class="card flashcard-shell">
  <div class="flashcard-progress">
    <span id="fc-progress">Card 1 of <?= count($cards) ?></span>
    <div class="flashcard-bar"><div id="fc-bar"></div></div>
    <span class="flashcard-counts">
      <span class="muted">Done:</span> <strong id="fc-done">0</strong>
    </span>
  </div>

  <div class="flashcard" id="fc" data-flipped="false">
    <div class="flashcard__face flashcard__front <?= $isRtl ? 'rtl' : '' ?>" id="fc-front"></div>
    <div class="flashcard__face flashcard__back" id="fc-back"></div>
  </div>

  <div class="flashcard-actions">
    <button class="btn btn--ghost" id="fc-prev">‹ Prev</button>
    <button class="btn" id="fc-flip">Flip / Show answer (Space)</button>
    <button class="btn btn--ghost" id="fc-next">Next ›</button>
  </div>
  <div class="flashcard-actions" style="margin-top:8px">
    <button class="btn fc-grade fc-grade--review" data-act="review">Review again (R)</button>
    <button class="btn fc-grade fc-grade--known" data-act="known">Good (K)</button>
    <button class="btn fc-grade fc-grade--easy" data-act="easy">Easy (E)</button>
  </div>
  <p class="muted" style="font-size:11px;text-align:center;margin:8px 0 0">
    Spaced repetition: "Good" pushes the card further; "Easy" pushes further still; "Review again" resets to tomorrow.
  </p>
</div>
<?php endif; ?>

<style>
.fc-stat { background:var(--card-2); border:1px solid var(--border); border-radius:10px; padding:10px 12px; text-align:center; }
.fc-stat__label { font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
.fc-stat__num { font-size:22px; font-weight:700; margin-top:2px; }
.fc-stat--due  { border-color:var(--primary); }
.fc-stat--due  .fc-stat__num { color:var(--primary); }
.fc-stat--new  .fc-stat__num { color:var(--warn); }
.fc-stat--done .fc-stat__num { color:var(--good); }

.flashcard-shell { display:flex; flex-direction:column; gap:14px; }
.flashcard-progress { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; font-size:12px; color:var(--muted); }
.flashcard-bar { flex:1; min-width:160px; height:6px; background:var(--card-2); border-radius:999px; overflow:hidden; }
.flashcard-bar > div { height:100%; width:0%; background:var(--primary); transition: width .2s; }
.flashcard-counts strong { font-weight:700; color:var(--text); }

.flashcard {
  perspective: 1500px;
  position:relative;
  min-height: 280px;
  cursor: pointer;
}
.flashcard__face {
  border:1px solid var(--border);
  border-radius:14px;
  padding:28px 24px;
  min-height: 280px;
  display:flex; align-items:center; justify-content:center;
  text-align:center; font-size:22px; line-height:1.5;
  white-space: pre-wrap;
  backface-visibility: hidden;
  position:absolute; top:0; left:0; right:0;
  transition: transform .45s ease;
}
.flashcard__front { background:var(--card-2); transform: rotateY(0deg); }
.flashcard__back  { background:#1c1738; color:#ece6ff; transform: rotateY(180deg); }
.flashcard[data-flipped="true"] .flashcard__front { transform: rotateY(-180deg); }
.flashcard[data-flipped="true"] .flashcard__back  { transform: rotateY(0deg); }
.flashcard__front.rtl, .flashcard__back.rtl { direction:rtl; font-family: serif; font-size:30px; line-height:1.7; }

.flashcard-actions { display:flex; gap:8px; flex-wrap:wrap; justify-content:center; }
.fc-grade--review { background: linear-gradient(180deg, #b04050, #8a2030); }
.fc-grade--known  { background: var(--primary); }
.fc-grade--easy   { background: linear-gradient(180deg, #2e9a6b, #1f6a47); }

@media (max-width: 640px) {
  .flashcard__face { font-size:18px; padding:20px 16px; }
}
</style>

<?php if ($cards): ?>
<script>
(function () {
  const cards = <?= json_encode($cards, JSON_UNESCAPED_UNICODE) ?>;
  const csrf  = <?= json_encode(csrf_token(), JSON_UNESCAPED_UNICODE) ?>;
  const ajaxUrl = '<?= url('student/flashcards.php?lib=' . urlencode($lib['slug']) . '&ajax=1') ?>';
  const canSave = <?= $tableReady ? 'true' : 'false' ?>;

  let idx = 0;
  let flipped = false;
  let done = 0;

  const $front = document.getElementById('fc-front');
  const $back  = document.getElementById('fc-back');
  const $card  = document.getElementById('fc');
  const $prog  = document.getElementById('fc-progress');
  const $bar   = document.getElementById('fc-bar');
  const $done  = document.getElementById('fc-done');

  function render() {
    const c = cards[idx];
    $front.textContent = c.front;
    let back = c.back;
    if (c.extra) back += '\n\n' + c.extra;
    $back.textContent = back;
    flipped = false;
    $card.setAttribute('data-flipped', 'false');
    $prog.textContent = 'Card ' + (idx + 1) + ' of ' + cards.length;
    $bar.style.width = (((idx + 1) / cards.length) * 100) + '%';
    $done.textContent = done;
  }
  function flip() { flipped = !flipped; $card.setAttribute('data-flipped', flipped ? 'true' : 'false'); }
  function next() { idx = (idx + 1) % cards.length; render(); }
  function prev() { idx = (idx - 1 + cards.length) % cards.length; render(); }

  function persist(action) {
    if (!canSave) return Promise.resolve();
    const c = cards[idx];
    const fd = new FormData();
    fd.append('csrf_token', csrf);
    fd.append('card_id', String(c.id));
    fd.append('act', action);
    return fetch(ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(r => r.json()).catch(() => ({ ok: false }));
  }
  function grade(action) {
    done++;
    $done.textContent = done;
    persist(action);
    next();
  }

  document.getElementById('fc-flip').addEventListener('click', flip);
  document.getElementById('fc-next').addEventListener('click', next);
  document.getElementById('fc-prev').addEventListener('click', prev);
  document.querySelectorAll('.fc-grade').forEach(btn => {
    btn.addEventListener('click', () => grade(btn.getAttribute('data-act')));
  });
  $card.addEventListener('click', flip);

  document.addEventListener('keydown', function (e) {
    if (e.target.matches('input, textarea, select')) return;
    if (e.key === ' ' || e.key === 'Enter') { e.preventDefault(); flip(); }
    else if (e.key === 'ArrowRight') next();
    else if (e.key === 'ArrowLeft')  prev();
    else if (e.key.toLowerCase() === 'k') grade('known');
    else if (e.key.toLowerCase() === 'r') grade('review');
    else if (e.key.toLowerCase() === 'e') grade('easy');
  });

  render();
})();
</script>
<?php endif; ?>
<?php
student_layout_end();
