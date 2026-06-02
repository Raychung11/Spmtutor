<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/student_layout.php';
require_once __DIR__ . '/../inc/reference_libraries.php';

$user = require_login();

$libSlug = input('lib');
$lib     = $libSlug ? reference_library_by_slug($libSlug) : null;

if (!$lib || empty($lib['flashcard'])) {
    flash('error', 'No flashcard library selected — pick one from the Library page.');
    redirect('student/library.php');
}
if (!reference_library_available($lib)) {
    flash('error', 'This library is not yet seeded. Ask the admin to run migrations and seeders first.');
    redirect('student/library.php');
}

// Optional filters (re-use the library schema).
$tag = trim(input('tag'));
$where  = ['1=1'];
$params = [];
if ($tag !== '' && !empty($lib['tag_col'])) {
    $where[]  = '`' . $lib['tag_col'] . '` = ?';
    $params[] = $tag;
}

try {
    $rows = db_all(
        "SELECT * FROM `{$lib['table']}` WHERE " . implode(' AND ', $where) . " AND status = 'active' ORDER BY sort_order, id LIMIT 200",
        $params
    );
} catch (Throwable $e) {
    $rows = db_all(
        "SELECT * FROM `{$lib['table']}` WHERE " . implode(' AND ', $where) . " ORDER BY sort_order, id LIMIT 200",
        $params
    );
}

// Shuffle once on the server so each visit gives a fresh order.
mt_srand((int) (microtime(true) * 1000) % 0x7FFFFFFF);
shuffle($rows);

$total = count($rows);

// Project cards to a small JSON the client can flip through.
$frontCol = $lib['flashcard']['front'];
$backCol  = $lib['flashcard']['back'];
$extraCol = $lib['flashcard']['back_extra'] ?? null;

$cards = [];
foreach ($rows as $r) {
    $front = trim((string) ($r[$frontCol] ?? ''));
    $back  = trim((string) ($r[$backCol]  ?? ''));
    $extra = $extraCol ? trim((string) ($r[$extraCol] ?? '')) : '';
    if ($front === '' || $back === '') {
        continue;
    }
    $cards[] = [
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

student_layout_start('Flashcards · ' . $lib['label'], $user, 'library.php');
?>
<div class="card" style="margin-bottom:18px">
  <p style="margin:0 0 8px">
    <a href="<?= url('student/library.php?lib=' . urlencode($lib['slug'])) ?>" class="muted">← Browse <?= e($lib['label']) ?></a>
  </p>
  <h2 style="margin:0 0 6px">Flashcards · <?= e($lib['label']) ?></h2>
  <p class="muted" style="margin:0"><?= e($lib['description']) ?></p>
</div>

<?php if ($tags): ?>
<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 8px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Filter</h4>
  <div style="display:flex;flex-wrap:wrap;gap:6px">
    <a class="chip" href="<?= url('student/flashcards.php?lib=' . urlencode($lib['slug'])) ?>"
       style="<?= $tag === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">All</a>
    <?php foreach ($tags as $t): ?>
      <a class="chip" href="<?= url('student/flashcards.php?lib=' . urlencode($lib['slug']) . '&tag=' . urlencode($t)) ?>"
         style="<?= $tag === $t ? 'border-color:var(--primary);color:var(--primary)' : '' ?>"><?= e($t) ?></a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php if (!$cards): ?>
  <div class="card"><p class="muted">No cards match. Try clearing the filter.</p></div>
<?php else: ?>
<div class="card flashcard-shell">
  <div class="flashcard-progress">
    <span id="fc-progress">Card 1 of <?= count($cards) ?></span>
    <div class="flashcard-bar"><div id="fc-bar"></div></div>
    <span class="flashcard-counts">
      <span class="muted">Known:</span> <strong id="fc-known">0</strong> ·
      <span class="muted">Review:</span> <strong id="fc-review">0</strong>
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
    <button class="btn btn--ghost" id="fc-review-btn">Review again (R)</button>
    <button class="btn btn--ghost" id="fc-known-btn">Known (K)</button>
    <button class="btn btn--ghost" id="fc-shuffle">Shuffle deck</button>
  </div>
</div>
<?php endif; ?>

<style>
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
@media (max-width: 640px) {
  .flashcard__face { font-size:18px; padding:20px 16px; }
}
</style>

<script>
(function(){
  const cards = <?= json_encode($cards, JSON_UNESCAPED_UNICODE) ?>;
  if (!cards.length) return;

  let idx = 0;
  let flipped = false;
  const known = new Set();
  const review = new Set();

  const $front = document.getElementById('fc-front');
  const $back  = document.getElementById('fc-back');
  const $card  = document.getElementById('fc');
  const $prog  = document.getElementById('fc-progress');
  const $bar   = document.getElementById('fc-bar');
  const $kn    = document.getElementById('fc-known');
  const $rv    = document.getElementById('fc-review');

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
    $kn.textContent = known.size;
    $rv.textContent = review.size;
  }
  function flip() { flipped = !flipped; $card.setAttribute('data-flipped', flipped ? 'true' : 'false'); }
  function next() { idx = (idx + 1) % cards.length; render(); }
  function prev() { idx = (idx - 1 + cards.length) % cards.length; render(); }
  function markKnown()  { known.add(idx); review.delete(idx); $kn.textContent = known.size; $rv.textContent = review.size; next(); }
  function markReview() { review.add(idx); known.delete(idx); $kn.textContent = known.size; $rv.textContent = review.size; next(); }
  function shuffle() {
    for (let i = cards.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [cards[i], cards[j]] = [cards[j], cards[i]];
    }
    idx = 0;
    known.clear();
    review.clear();
    render();
  }

  document.getElementById('fc-flip').addEventListener('click', flip);
  document.getElementById('fc-next').addEventListener('click', next);
  document.getElementById('fc-prev').addEventListener('click', prev);
  document.getElementById('fc-known-btn').addEventListener('click', markKnown);
  document.getElementById('fc-review-btn').addEventListener('click', markReview);
  document.getElementById('fc-shuffle').addEventListener('click', shuffle);
  $card.addEventListener('click', flip);

  document.addEventListener('keydown', function(e) {
    if (e.target.matches('input, textarea, select')) return;
    if (e.key === ' ' || e.key === 'Enter') { e.preventDefault(); flip(); }
    else if (e.key === 'ArrowRight') next();
    else if (e.key === 'ArrowLeft') prev();
    else if (e.key.toLowerCase() === 'k') markKnown();
    else if (e.key.toLowerCase() === 'r') markReview();
  });

  render();
})();
</script>
<?php
student_layout_end();
