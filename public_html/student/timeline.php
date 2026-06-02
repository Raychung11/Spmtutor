<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_login();

$tableExists = (bool) db_one(
    "SELECT 1 AS x FROM information_schema.TABLES
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sejarah_timeline'"
);

$era       = trim(input('era'));
$direction = input('dir') === 'desc' ? 'desc' : 'asc';
$importance = input('importance');
$importance = in_array($importance, ['high', 'medium', 'low'], true) ? $importance : '';

$events = [];
$eraCounts = [];
$importanceCounts = ['high' => 0, 'medium' => 0, 'low' => 0];

if ($tableExists) {
    $where = ['status = "active"'];
    $params = [];
    if ($era !== '') {
        $where[] = 'era = ?';
        $params[] = $era;
    }
    if ($importance !== '') {
        $where[] = 'importance = ?';
        $params[] = $importance;
    }
    $whereSql = implode(' AND ', $where);
    $orderSql = $direction === 'desc' ? 'sort_order DESC, id DESC' : 'sort_order ASC, id ASC';

    $events = db_all(
        "SELECT id, era, year_label, event_date, title, description, importance, topic_label
         FROM sejarah_timeline WHERE $whereSql ORDER BY $orderSql",
        $params
    );

    foreach (db_all("SELECT era, COUNT(*) AS c FROM sejarah_timeline WHERE status = 'active' GROUP BY era") as $r) {
        $eraCounts[(string) $r['era']] = (int) $r['c'];
    }
    foreach (db_all("SELECT importance, COUNT(*) AS c FROM sejarah_timeline WHERE status = 'active' GROUP BY importance") as $r) {
        $importanceCounts[(string) $r['importance']] = (int) $r['c'];
    }
}

$eraLabels = [
    'pre-colonial' => 'Pra-Kolonial (sebelum 1511)',
    'colonial'     => 'Era Penjajahan Eropah (1511 – 1939)',
    'pre-war'      => 'Pra-Perang (Kebangkitan Nasionalisme)',
    'wwii'         => 'Perang Dunia Kedua & Pendudukan Jepun (1939 – 1945)',
    'post-war'     => 'Selepas Perang & Era Peralihan (1945 – 1948)',
    'emergency'    => 'Era Darurat (1948 – 1960)',
    'independence' => 'Era Kemerdekaan (1949 – 1960)',
    'malaysia'     => 'Pembentukan Malaysia (1961 – 1969)',
    'modern'       => 'Era Moden (1970 – kini)',
];
// Drop labels for eras with no events.
foreach (array_keys($eraLabels) as $k) {
    if (!isset($eraCounts[$k]) || $eraCounts[$k] === 0) {
        // Keep label so chip can show 0 — but skip rendering chips for fully-missing eras.
    }
}

function timeline_link(array $overrides = []): string
{
    global $era, $direction, $importance;
    $args = array_merge(['era' => $era, 'dir' => $direction, 'importance' => $importance], $overrides);
    $defaults = ['era' => '', 'dir' => 'asc', 'importance' => ''];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('student/timeline.php' . ($args ? '?' . http_build_query($args) : ''));
}

student_layout_start('Sejarah Timeline', $user, 'library.php');
?>
<div class="card" style="margin-bottom:18px">
  <p style="margin:0 0 8px">
    <a href="<?= url('student/library.php?lib=sejarah-timeline') ?>" class="muted">← Card view in Library</a>
  </p>
  <h2 style="margin:0 0 6px">Sejarah Timeline</h2>
  <p class="muted" style="margin:0">
    Peristiwa penting Sejarah Malaysia dari Pengasasan Kesultanan Melaka (1400)
    hingga Wawasan 2020 (1991), disusun secara kronologi. Tap mana-mana peristiwa untuk butiran penuh.
  </p>
</div>

<?php if (!$tableExists): ?>
  <div class="flash flash--error">
    The sejarah_timeline table isn't in the database yet. Ask the admin to run database migrations
    (Admin → Seeders → Run database migrations) then run the KSSM Sejarah seeder.
  </div>
<?php elseif (!$events): ?>
  <div class="card"><p class="muted">No events seeded yet. Ask the admin to run <strong>Admin → Seeders → KSSM Sejarah</strong>.</p></div>
<?php else: ?>

<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Era</h4>
  <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px">
    <a class="chip" href="<?= e(timeline_link(['era' => ''])) ?>"
       style="<?= $era === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
       All <span class="muted" style="margin-left:4px">· <?= (int) array_sum($eraCounts) ?></span>
    </a>
    <?php foreach ($eraLabels as $key => $label):
      $c = (int) ($eraCounts[$key] ?? 0);
      if ($c === 0) continue;
    ?>
      <a class="chip" href="<?= e(timeline_link(['era' => $key])) ?>"
         style="<?= $era === $key ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
         <?= e($label) ?>
         <span class="muted" style="margin-left:4px">· <?= $c ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Importance</h4>
  <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px">
    <a class="chip" href="<?= e(timeline_link(['importance' => ''])) ?>"
       style="<?= $importance === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">All</a>
    <a class="chip" href="<?= e(timeline_link(['importance' => 'high'])) ?>"
       style="<?= $importance === 'high' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">Critical <span class="muted">· <?= (int) ($importanceCounts['high'] ?? 0) ?></span></a>
    <a class="chip" href="<?= e(timeline_link(['importance' => 'medium'])) ?>"
       style="<?= $importance === 'medium' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">Important <span class="muted">· <?= (int) ($importanceCounts['medium'] ?? 0) ?></span></a>
  </div>

  <div style="display:flex;gap:6px;align-items:center">
    <span class="muted" style="font-size:13px">Order:</span>
    <a class="btn btn--sm <?= $direction === 'asc' ? '' : 'btn--ghost' ?>" href="<?= e(timeline_link(['dir' => 'asc'])) ?>">Oldest first ↓</a>
    <a class="btn btn--sm <?= $direction === 'desc' ? '' : 'btn--ghost' ?>" href="<?= e(timeline_link(['dir' => 'desc'])) ?>">Newest first ↑</a>
  </div>
</div>

<div class="timeline">
  <div class="timeline__line"></div>

  <?php
  $currentEra = null;
  $sideIdx = 0;
  foreach ($events as $i => $ev):
    $thisEra = (string) $ev['era'];
    if ($thisEra !== $currentEra):
      $currentEra = $thisEra;
      $sideIdx = 0;  // reset alternation when entering a new era
  ?>
    <div class="timeline__era">
      <span><?= e($eraLabels[$thisEra] ?? ucfirst($thisEra)) ?></span>
    </div>
  <?php endif;
    $side = ($sideIdx++ % 2 === 0) ? 'left' : 'right';
    $impClass = 'timeline__event--' . ($ev['importance'] ?: 'medium');
    $dateText = (string) $ev['year_label'] . ($ev['event_date'] ? ' · ' . $ev['event_date'] : '');
  ?>
    <div class="timeline__row timeline__row--<?= $side ?>">
      <div class="timeline__dot <?= $impClass ?>"></div>
      <div class="timeline__year"><?= e((string) $ev['year_label']) ?></div>
      <details class="timeline__event <?= $impClass ?>">
        <summary>
          <div class="timeline__event-date muted"><?= e($dateText) ?></div>
          <div class="timeline__event-title"><?= e((string) $ev['title']) ?></div>
        </summary>
        <?php if (!empty($ev['description'])): ?>
          <p class="timeline__event-desc"><?= e((string) $ev['description']) ?></p>
        <?php endif; ?>
        <?php if (!empty($ev['topic_label'])): ?>
          <div class="muted" style="font-size:11px;margin-top:6px">Bab: <?= e((string) $ev['topic_label']) ?></div>
        <?php endif; ?>
      </details>
    </div>
  <?php endforeach; ?>
</div>

<?php endif; ?>

<style>
.timeline { position:relative; margin:24px 0; padding:8px 0; }
.timeline__line {
  position:absolute; top:0; bottom:0; left:50%; transform:translateX(-50%);
  width:3px;
  background: linear-gradient(180deg, transparent 0%, var(--primary) 5%, var(--primary) 95%, transparent 100%);
  border-radius: 999px;
  z-index:0;
}

/* Era full-width header inside the timeline */
.timeline__era {
  position:relative;
  display:flex; justify-content:center;
  margin: 28px 0 12px;
  z-index:1;
}
.timeline__era span {
  background: var(--card-2);
  border:1px solid var(--primary);
  color: var(--primary);
  font-size:12px; font-weight:600;
  text-transform: uppercase; letter-spacing:.08em;
  padding:6px 14px; border-radius:999px;
}

/* A row in the timeline */
.timeline__row {
  position:relative;
  display:grid;
  grid-template-columns: 1fr 80px 1fr;
  align-items:flex-start;
  margin: 12px 0;
  z-index:1;
}
.timeline__year {
  text-align:center;
  font-weight:700;
  color: var(--accent);
  font-size:14px;
  padding-top:8px;
}
.timeline__dot {
  position:absolute;
  top:14px; left:50%; transform:translate(-50%, 0);
  width:14px; height:14px; border-radius:50%;
  background: var(--primary); border:3px solid var(--bg);
  z-index:2;
}
.timeline__dot.timeline__event--high   { width:18px; height:18px; box-shadow:0 0 12px var(--primary); }
.timeline__dot.timeline__event--medium { width:14px; height:14px; }
.timeline__dot.timeline__event--low    { width:10px; height:10px; opacity:.7; }

.timeline__event {
  background: var(--card-2); border:1px solid var(--border); border-radius:12px;
  padding:12px 14px;
  transition: border-color .15s, transform .15s;
}
.timeline__event:hover { border-color: var(--primary); transform: translateY(-1px); }
.timeline__event > summary {
  list-style: none; cursor: pointer;
}
.timeline__event > summary::-webkit-details-marker { display: none; }
.timeline__event-date { font-size:11px; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; }
.timeline__event-title { font-weight:600; font-size:14px; line-height:1.45; }
.timeline__event-desc { font-size:13px; line-height:1.55; margin:10px 0 0; color: var(--text); }

/* Importance styling on the card */
.timeline__event.timeline__event--high { border-left:3px solid var(--primary); }
.timeline__event.timeline__event--low  { opacity:.85; font-size:13px; }

/* Left side: card sits in column 1, year in column 2 */
.timeline__row--left  .timeline__event { grid-column: 1; }
.timeline__row--left  .timeline__year  { grid-column: 2; }
.timeline__row--right .timeline__year  { grid-column: 2; }
.timeline__row--right .timeline__event { grid-column: 3; }

/* Mobile: stack everything to the left, line moves to left edge */
@media (max-width: 720px) {
  .timeline__line { left: 24px; transform:none; }
  .timeline__row {
    grid-template-columns: 60px 1fr;
    gap:8px;
  }
  .timeline__dot { left: 24px; transform: translate(-50%, 0); }
  .timeline__year { grid-column: 1 !important; text-align:right; padding-right:8px; padding-top:8px; font-size:13px; }
  .timeline__row--left .timeline__event,
  .timeline__row--right .timeline__event { grid-column: 2 !important; }
}
</style>
<?php
student_layout_end();
