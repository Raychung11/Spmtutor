<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/student_layout.php';
require_once __DIR__ . '/../inc/reference_libraries.php';
require_once __DIR__ . '/../inc/flashcard_progress.php';

// Library is open to any logged-in role so teachers / parents can browse too.
$user = require_login();

$libs = reference_libraries();
$libBySlug = [];
foreach ($libs as $l) {
    $libBySlug[$l['slug']] = $l;
}

$selectedSlug = input('lib');
$selected     = $selectedSlug ? ($libBySlug[$selectedSlug] ?? null) : null;
$q            = trim(input('q'));
$tag          = trim(input('tag'));
$page         = max(1, input_int('page', 1));
$perPage      = 24;
$offset       = ($page - 1) * $perPage;

student_layout_start('Library', $user, 'library.php');

if (!$selected):
    // ---------- Index: all libraries grouped by subject ----------
    $subjectNames = [];
    foreach (db_all('SELECT slug, name FROM subjects ORDER BY sort_order, name') as $s) {
        $subjectNames[$s['slug']] = $s['name'];
    }
    $bySubject = reference_libraries_by_subject();
    ?>
    <div class="card" style="margin-bottom:18px">
      <h2 style="margin:0 0 8px">Reference Library</h2>
      <p class="muted" style="margin:0">
        Quick-look reference banks across every SPM subject — formulas, ayat, peribahasa,
        timeline, code, diagrams. Tap any library to browse, search, or switch to flashcard mode.
      </p>
    </div>

    <?php foreach ($bySubject as $subjectSlug => $list):
        $subjectName = $subjectNames[$subjectSlug] ?? $subjectSlug;
    ?>
      <h3 style="margin:18px 0 8px;color:var(--accent);font-size:13px;text-transform:uppercase;letter-spacing:.08em"><?= e($subjectName) ?></h3>
      <div class="grid grid--3" style="margin-bottom:12px">
        <?php foreach ($list as $lib):
            $count = reference_library_count($lib);
            $unavailable = $count === 0;
            $dueCount = (!$unavailable && !empty($lib['flashcard']))
                ? fcp_due_count((int) $user['id'], $lib['slug'], $count)
                : 0;
        ?>
          <a class="lib-card<?= $unavailable ? ' lib-card--disabled' : '' ?>"
             href="<?= $unavailable ? '#' : url('student/library.php?lib=' . urlencode($lib['slug'])) ?>">
            <div class="lib-card__title">
              <?= e($lib['label']) ?>
              <?php if ($dueCount > 0): ?>
                <span class="lib-card__due"><?= $dueCount ?> due</span>
              <?php endif; ?>
            </div>
            <div class="lib-card__desc"><?= e($lib['description']) ?></div>
            <div class="lib-card__foot">
              <?php if ($unavailable): ?>
                <span class="muted">Not yet seeded</span>
              <?php else: ?>
                <span><strong><?= $count ?></strong> entries</span>
                <?php if (!empty($lib['flashcard'])): ?>
                  <span class="muted">· flashcards available</span>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <?php
else:
    // ---------- Drill into one library ----------
    if (!reference_library_available($selected)) {
        echo '<div class="flash flash--error">This library\'s table isn\'t in the database yet. Ask the admin to run the database migrations.</div>';
    } else {
        // Build the query (parameterised against catalog only — table name is from registry, not user input).
        $where  = ['1=1'];
        $params = [];

        if ($q !== '' && !empty($selected['search_cols'])) {
            $ors = [];
            foreach ($selected['search_cols'] as $col) {
                $ors[]    = '`' . $col . '` LIKE ?';
                $params[] = '%' . $q . '%';
            }
            $where[] = '(' . implode(' OR ', $ors) . ')';
        }
        if ($tag !== '' && !empty($selected['tag_col'])) {
            $where[]  = '`' . $selected['tag_col'] . '` = ?';
            $params[] = $tag;
        }
        // status filter (some tables may not have it; try/catch handles that case in counts).
        try {
            $countRow = db_one(
                "SELECT COUNT(*) AS c FROM `{$selected['table']}` WHERE " . implode(' AND ', $where) . " AND status = 'active'",
                $params
            );
            $hasStatus = true;
        } catch (Throwable $e) {
            $countRow = db_one(
                "SELECT COUNT(*) AS c FROM `{$selected['table']}` WHERE " . implode(' AND ', $where),
                $params
            );
            $hasStatus = false;
        }
        $total = (int) ($countRow['c'] ?? 0);
        $pages = max(1, (int) ceil($total / $perPage));
        if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

        $rows = db_all(
            "SELECT * FROM `{$selected['table']}` WHERE " . implode(' AND ', $where)
            . ($hasStatus ? " AND status = 'active'" : '')
            . " ORDER BY sort_order, id LIMIT $perPage OFFSET $offset",
            $params
        );

        // Distinct tag values for the chip row.
        $tags = [];
        if (!empty($selected['tag_col'])) {
            try {
                $tagRows = db_all(
                    "SELECT DISTINCT `{$selected['tag_col']}` AS t FROM `{$selected['table']}` WHERE `{$selected['tag_col']}` IS NOT NULL AND `{$selected['tag_col']}` <> '' ORDER BY t LIMIT 30"
                );
                $tags = array_filter(array_column($tagRows, 't'));
            } catch (Throwable $e) {}
        }
        ?>
        <div class="card" style="margin-bottom:18px">
          <p style="margin:0 0 8px"><a href="<?= url('student/library.php') ?>" class="muted">← All libraries</a></p>
          <h2 style="margin:0 0 6px"><?= e($selected['label']) ?> <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h2>
          <p class="muted" style="margin:0 0 12px"><?= e($selected['description']) ?></p>
          <?php if ($selected['slug'] === 'sejarah-timeline'): ?>
            <a class="btn" href="<?= url('student/timeline.php') ?>">Open chronological timeline →</a>
          <?php endif; ?>
          <?php if (!empty($selected['flashcard'])): ?>
            <a class="btn <?= $selected['slug'] === 'sejarah-timeline' ? 'btn--ghost' : '' ?>" href="<?= url('student/flashcards.php?lib=' . urlencode($selected['slug'])) ?>">Switch to flashcards →</a>
          <?php endif; ?>
        </div>

        <div class="card" style="margin-bottom:18px">
          <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
            <input type="hidden" name="lib" value="<?= e($selected['slug']) ?>">
            <input class="input" name="q" placeholder="Search…" value="<?= e($q) ?>" style="flex:1;min-width:200px">
            <button class="btn btn--sm">Search</button>
            <?php if ($q !== '' || $tag !== ''): ?>
              <a class="btn btn--sm btn--ghost" href="<?= url('student/library.php?lib=' . urlencode($selected['slug'])) ?>">Clear</a>
            <?php endif; ?>
          </form>
          <?php if ($tags): ?>
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px">
              <a class="chip" href="<?= url('student/library.php?lib=' . urlencode($selected['slug']) . ($q !== '' ? '&q=' . urlencode($q) : '')) ?>"
                 style="<?= $tag === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">All</a>
              <?php foreach ($tags as $t): ?>
                <a class="chip" href="<?= url('student/library.php?lib=' . urlencode($selected['slug']) . '&tag=' . urlencode($t) . ($q !== '' ? '&q=' . urlencode($q) : '')) ?>"
                   style="<?= $tag === $t ? 'border-color:var(--primary);color:var(--primary)' : '' ?>"><?= e($t) ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <?php if (!$rows): ?>
          <div class="card"><p class="muted">No entries match. Try clearing filters.</p></div>
        <?php else: ?>
          <div class="grid grid--2">
            <?php foreach ($rows as $row): ?>
              <?php library_render_card($row, $selected); ?>
            <?php endforeach; ?>
          </div>

          <?php if ($pages > 1): ?>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
              <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
              <div style="display:flex;gap:6px;flex-wrap:wrap">
                <?php $base = url('student/library.php?lib=' . urlencode($selected['slug']) . ($q !== '' ? '&q=' . urlencode($q) : '') . ($tag !== '' ? '&tag=' . urlencode($tag) : ''));
                $linkP = fn($p) => $base . '&page=' . $p; ?>
                <?php if ($page > 1): ?>
                  <a class="btn btn--sm btn--ghost" href="<?= e($linkP(1)) ?>">« First</a>
                  <a class="btn btn--sm btn--ghost" href="<?= e($linkP($page - 1)) ?>">‹ Prev</a>
                <?php endif; ?>
                <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
                  <a class="btn btn--sm <?= $p === $page ? '' : 'btn--ghost' ?>" href="<?= e($linkP($p)) ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($page < $pages): ?>
                  <a class="btn btn--sm btn--ghost" href="<?= e($linkP($page + 1)) ?>">Next ›</a>
                  <a class="btn btn--sm btn--ghost" href="<?= e($linkP($pages)) ?>">Last »</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php
    }
endif;

/** Render a single reference card based on the library's field map. */
function library_render_card(array $row, array $lib): void
{
    $c     = $lib['card'];
    $lang  = $lib['language'] ?? 'en';
    $isRtl = $lang === 'ar';
    $title = $c['title']      ?? null;
    $expr  = $c['expression'] ?? null;
    $sub   = $c['subtitle']   ?? null;
    $body  = $c['body']       ?? null;
    ?>
    <div class="lib-item">
      <?php if ($sub && !empty($row[$sub])): ?>
        <div class="lib-item__sub"><?= e((string) $row[$sub]) ?></div>
      <?php endif; ?>
      <?php if ($title && !empty($row[$title])): ?>
        <div class="lib-item__title <?= $isRtl ? 'rtl' : '' ?>"><?= e((string) $row[$title]) ?></div>
      <?php endif; ?>
      <?php if ($expr && !empty($row[$expr])):
          $isCode = ($lib['slug'] === 'cs-concepts');
          $rtl    = $isRtl ? 'rtl' : '';
      ?>
        <?php if ($isCode): ?>
          <pre class="lib-item__code"><?= e((string) $row[$expr]) ?></pre>
        <?php else: ?>
          <div class="lib-item__expr <?= $rtl ?>"><?= e((string) $row[$expr]) ?></div>
        <?php endif; ?>
      <?php endif; ?>
      <?php if ($body && !empty($row[$body])): ?>
        <div class="lib-item__body"><?= nl2br(e((string) $row[$body])) ?></div>
      <?php endif; ?>
    </div>
    <?php
}
?>

<style>
/* --- index cards --- */
.lib-card {
  display:flex; flex-direction:column; gap:8px;
  padding:14px 16px;
  background:var(--card-2); border:1px solid var(--border); border-radius:12px;
  text-decoration:none; color:inherit;
  transition: border-color .15s, transform .15s;
}
.lib-card:hover { border-color:var(--primary); transform: translateY(-1px); }
.lib-card--disabled { opacity:.5; pointer-events:none; }
.lib-card__title { font-weight:600; font-size:15px; display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
.lib-card__due {
  background: var(--primary); color: #fff;
  font-size:10px; font-weight:700; letter-spacing:.04em;
  padding:2px 8px; border-radius:999px;
}
.lib-card__desc { font-size:13px; color:var(--muted); line-height:1.45; }
.lib-card__foot { font-size:12px; color:var(--muted); display:flex; gap:6px; margin-top:auto; }

/* --- inside-library card grid --- */
.lib-item {
  display:flex; flex-direction:column; gap:10px;
  padding:14px 16px;
  background:var(--card-2); border:1px solid var(--border); border-radius:12px;
}
.lib-item__sub { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); }
.lib-item__title { font-weight:600; font-size:16px; line-height:1.4; }
.lib-item__title.rtl { direction:rtl; text-align:right; font-size:22px; line-height:1.6; }
.lib-item__expr {
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  font-size:14px;
  padding:10px 12px;
  background:var(--bg-2); border:1px solid var(--border); border-radius:8px;
  white-space:pre-wrap; word-break:break-word;
}
.lib-item__expr.rtl { direction:rtl; text-align:right; font-family: serif; font-size:18px; line-height:1.8; }
.lib-item__code {
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  font-size:12px; line-height:1.5;
  padding:10px 12px; margin:0;
  background:#0d0a1f; color:#e9e4ff; border:1px solid var(--border); border-radius:8px;
  white-space:pre; overflow:auto;
}
.lib-item__body { font-size:13px; line-height:1.55; color:var(--text); }
</style>
<?php
student_layout_end();
