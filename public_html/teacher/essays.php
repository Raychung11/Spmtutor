<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/notifications.php';
require_once __DIR__ . '/../inc/teacher_layout.php';

$user = require_role('teacher');
$tid  = (int) $user['id'];

$tableReady = (bool) db_one(
    "SELECT 1 AS x FROM information_schema.TABLES
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'essay_submissions'"
);
$reviewColsReady = $tableReady && (bool) db_one(
    "SELECT 1 AS x FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'essay_submissions' AND COLUMN_NAME = 'teacher_user_id'"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reviewColsReady) {
    csrf_check();
    $action = input('action');

    if ($action === 'review') {
        $essayId  = input_int('essay_id');
        $override = input('override_score');
        $comment  = input('teacher_comment');
        $essay = db_one('SELECT * FROM essay_submissions WHERE id = ?', [$essayId]);
        if ($essay) {
            db_exec(
                'UPDATE essay_submissions
                 SET teacher_user_id = ?, teacher_score_override = ?, teacher_comment = ?, reviewed_at = NOW()
                 WHERE id = ?',
                [$tid, $override === '' ? null : (int) $override, $comment ?: null, $essayId]
            );
            $score = $override !== '' ? $override : ($essay['score'] ?? 0);
            $msg   = 'Your teacher reviewed your ' . $essay['task_type'] . '. Score: ' . $score . '/' . $essay['max_score'];
            if ($comment) {
                $msg .= ' — ' . mb_substr($comment, 0, 200);
            }
            notify((int) $essay['user_id'], 'Your writing has been reviewed', $msg, 'essay_review');
            flash('success', 'Review saved and student notified.');
        }
    } elseif ($action === 'unreview') {
        $essayId = input_int('essay_id');
        db_exec(
            'UPDATE essay_submissions
             SET teacher_user_id = NULL, teacher_score_override = NULL, teacher_comment = NULL, reviewed_at = NULL
             WHERE id = ?',
            [$essayId]
        );
        flash('success', 'Review cleared.');
    }
    redirect('teacher/essays.php' . (input('return') ? '?' . input('return') : ''));
}

// --- Find this teacher's students (via teacher_classes → class_students).
$studentIds = [];
if ($tableReady) {
    $rows = db_all(
        'SELECT DISTINCT cs.student_user_id AS id
         FROM teacher_classes tc JOIN class_students cs ON cs.class_id = tc.id
         WHERE tc.teacher_user_id = ?',
        [$tid]
    );
    $studentIds = array_map(fn($r) => (int) $r['id'], $rows);
}

// --- Filters + pagination ---
$q          = trim(input('q'));
$language   = in_array(input('language'), ['bm', 'en'], true) ? input('language') : '';
$taskType   = in_array(input('task_type'), ['karangan', 'rumusan', 'tatabahasa'], true) ? input('task_type') : '';
$scope      = input('scope') === 'all' ? 'all' : 'mine';  // mine = my students, all = all submissions
$reviewedF  = in_array(input('reviewed'), ['yes', 'no'], true) ? input('reviewed') : '';
$page       = max(1, input_int('page', 1));
$perPage    = max(20, min(200, input_int('per_page', 50)));
$offset     = ($page - 1) * $perPage;

$where  = ['es.status = "marked"'];
$params = [];
if ($q !== '') {
    $where[]  = '(u.name LIKE ? OR es.prompt LIKE ? OR es.submission LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($language !== '') {
    $where[]  = 'es.language = ?';
    $params[] = $language;
}
if ($taskType !== '') {
    $where[]  = 'es.task_type = ?';
    $params[] = $taskType;
}
if ($scope === 'mine') {
    if (!$studentIds) {
        // No students enrolled — show none rather than everyone.
        $where[] = '0';
    } else {
        $place = implode(',', array_fill(0, count($studentIds), '?'));
        $where[] = 'es.user_id IN (' . $place . ')';
        foreach ($studentIds as $sid) { $params[] = $sid; }
    }
}
if ($reviewColsReady && $reviewedF === 'yes') {
    $where[] = 'es.reviewed_at IS NOT NULL';
} elseif ($reviewColsReady && $reviewedF === 'no') {
    $where[] = 'es.reviewed_at IS NULL';
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $tableReady ? (int) (db_one(
    "SELECT COUNT(*) c FROM essay_submissions es JOIN users u ON u.id = es.user_id $whereSql",
    $params
)['c'] ?? 0) : 0;
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$rows = $tableReady ? db_all(
    "SELECT es.*, u.name AS student_name, u.email AS student_email
     FROM essay_submissions es JOIN users u ON u.id = es.user_id
     $whereSql
     ORDER BY (es.reviewed_at IS NULL) DESC, es.id DESC
     LIMIT $perPage OFFSET $offset",
    $params
) : [];

// Stats for chip counts (respect scope but not other filters so counts stay informative).
$baseScope = ['es.status = "marked"'];
$baseScopeParams = [];
if ($scope === 'mine') {
    if (!$studentIds) {
        $baseScope[] = '0';
    } else {
        $place = implode(',', array_fill(0, count($studentIds), '?'));
        $baseScope[] = 'es.user_id IN (' . $place . ')';
        foreach ($studentIds as $sid) { $baseScopeParams[] = $sid; }
    }
}
$baseScopeSql = 'WHERE ' . implode(' AND ', $baseScope);

$langCounts = ['bm' => 0, 'en' => 0];
$taskCounts = ['karangan' => 0, 'rumusan' => 0, 'tatabahasa' => 0];
$reviewedCount = 0;
$unreviewedCount = 0;
$totalSubs = 0;
if ($tableReady) {
    foreach (db_all("SELECT es.language, COUNT(*) c FROM essay_submissions es $baseScopeSql GROUP BY es.language", $baseScopeParams) as $r) {
        $langCounts[(string) $r['language']] = (int) $r['c'];
    }
    foreach (db_all("SELECT es.task_type, COUNT(*) c FROM essay_submissions es $baseScopeSql GROUP BY es.task_type", $baseScopeParams) as $r) {
        $taskCounts[(string) $r['task_type']] = (int) $r['c'];
    }
    if ($reviewColsReady) {
        $reviewedCount   = (int) (db_one("SELECT COUNT(*) c FROM essay_submissions es $baseScopeSql AND es.reviewed_at IS NOT NULL", $baseScopeParams)['c'] ?? 0);
        $unreviewedCount = (int) (db_one("SELECT COUNT(*) c FROM essay_submissions es $baseScopeSql AND es.reviewed_at IS NULL",     $baseScopeParams)['c'] ?? 0);
    }
    $totalSubs = $reviewedCount + $unreviewedCount;
}

function essays_link(array $overrides = []): string
{
    global $q, $language, $taskType, $scope, $reviewedF, $page, $perPage;
    $args = array_merge([
        'q' => $q, 'language' => $language, 'task_type' => $taskType,
        'scope' => $scope, 'reviewed' => $reviewedF, 'page' => $page, 'per_page' => $perPage,
    ], $overrides);
    $defaults = ['q' => '', 'language' => '', 'task_type' => '', 'scope' => 'mine', 'reviewed' => '', 'page' => 1, 'per_page' => 50];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('teacher/essays.php' . ($args ? '?' . http_build_query($args) : ''));
}

teacher_layout_start('Essay Reviews', $user, 'essays.php');
?>
<div class="card" style="margin-bottom:18px">
  <h2 style="margin:0 0 6px">Student writing submissions</h2>
  <p class="muted" style="margin:0">
    Review the karangan, rumusan and tatabahasa your students submit through the AI Writing Marker.
    You can accept the AI score, override it, and leave a comment that pings the student.
  </p>
</div>

<?php if (!$tableReady): ?>
  <div class="flash flash--error">
    The essay_submissions table isn't in the database yet. Ask the admin to run database migrations
    (<strong>Admin → Seeders → Run database migrations</strong>) first.
  </div>
<?php elseif (!$reviewColsReady): ?>
  <div class="flash flash--info">
    Teacher review columns aren't applied yet. Ask the admin to run database migrations to enable
    score overrides and comments. You can still browse the submissions.
  </div>
<?php endif; ?>

<?php if ($tableReady): ?>
<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Scope</h4>
  <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px">
    <a class="chip" href="<?= e(essays_link(['scope' => 'mine', 'page' => 1])) ?>"
       style="<?= $scope === 'mine' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
      My students <span class="muted" style="margin-left:4px">· <?= count($studentIds) ?></span>
    </a>
    <a class="chip" href="<?= e(essays_link(['scope' => 'all', 'page' => 1])) ?>"
       style="<?= $scope === 'all' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
      All students
    </a>
  </div>

  <?php if ($scope === 'mine' && !$studentIds): ?>
    <p class="muted" style="font-size:13px;margin:0">
      You don't have any students enrolled in your classes yet. Add students via
      <a href="<?= url('teacher/classes.php') ?>">Teacher → Classes</a>, or browse
      <a href="<?= e(essays_link(['scope' => 'all'])) ?>">all submissions</a>.
    </p>
  <?php else: ?>
    <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Task type</h4>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px">
      <a class="chip" href="<?= e(essays_link(['task_type' => '', 'page' => 1])) ?>"
         style="<?= $taskType === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
        All <span class="muted" style="margin-left:4px">· <?= $totalSubs ?></span>
      </a>
      <?php foreach (['karangan' => 'Karangan / Essay', 'rumusan' => 'Rumusan', 'tatabahasa' => 'Tatabahasa / Grammar'] as $key => $label):
        $c = (int) ($taskCounts[$key] ?? 0);
        if ($c === 0) continue;
      ?>
        <a class="chip" href="<?= e(essays_link(['task_type' => $key, 'page' => 1])) ?>"
           style="<?= $taskType === $key ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
          <?= e($label) ?>
          <span class="muted" style="margin-left:4px">· <?= $c ?></span>
        </a>
      <?php endforeach; ?>
    </div>

    <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Language</h4>
    <div style="display:flex;flex-wrap:wrap;gap:8px">
      <a class="chip" href="<?= e(essays_link(['language' => '', 'page' => 1])) ?>"
         style="<?= $language === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
        Both <span class="muted" style="margin-left:4px">· <?= $totalSubs ?></span>
      </a>
      <a class="chip" href="<?= e(essays_link(['language' => 'bm', 'page' => 1])) ?>"
         style="<?= $language === 'bm' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
        Bahasa Melayu <span class="muted" style="margin-left:4px">· <?= (int) $langCounts['bm'] ?></span>
      </a>
      <a class="chip" href="<?= e(essays_link(['language' => 'en', 'page' => 1])) ?>"
         style="<?= $language === 'en' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
        English <span class="muted" style="margin-left:4px">· <?= (int) $langCounts['en'] ?></span>
      </a>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">Submissions <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <?php if ($reviewColsReady): ?>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $reviewedF === '' ? '' : 'btn--ghost' ?>" href="<?= e(essays_link(['reviewed' => '', 'page' => 1])) ?>">
        All <span class="muted" style="font-size:11px;margin-left:4px">· <?= $totalSubs ?></span>
      </a>
      <a class="btn btn--sm <?= $reviewedF === 'no' ? '' : 'btn--ghost' ?>" href="<?= e(essays_link(['reviewed' => 'no', 'page' => 1])) ?>">
        Unreviewed
        <?php if ($unreviewedCount > 0): ?>
          <span class="badge badge--warn" style="margin-left:4px"><?= $unreviewedCount ?></span>
        <?php else: ?>
          <span class="muted" style="font-size:11px;margin-left:4px">· 0</span>
        <?php endif; ?>
      </a>
      <a class="btn btn--sm <?= $reviewedF === 'yes' ? '' : 'btn--ghost' ?>" href="<?= e(essays_link(['reviewed' => 'yes', 'page' => 1])) ?>">
        Reviewed <span class="muted" style="font-size:11px;margin-left:4px">· <?= $reviewedCount ?></span>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input class="input" name="q" placeholder="Search student name, prompt or essay text" value="<?= e($q) ?>" style="flex:1;min-width:240px">
    <?php if ($scope !== 'mine'): ?><input type="hidden" name="scope" value="<?= e($scope) ?>"><?php endif; ?>
    <?php if ($language !== ''): ?><input type="hidden" name="language" value="<?= e($language) ?>"><?php endif; ?>
    <?php if ($taskType !== ''): ?><input type="hidden" name="task_type" value="<?= e($taskType) ?>"><?php endif; ?>
    <?php if ($reviewedF !== ''): ?><input type="hidden" name="reviewed" value="<?= e($reviewedF) ?>"><?php endif; ?>
    <select name="per_page" class="input" style="max-width:130px" onchange="this.form.submit()">
      <?php foreach ([20, 50, 100, 200] as $pp): ?>
        <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?> / page</option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $language !== '' || $taskType !== '' || $reviewedF !== '' || $scope !== 'mine'): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(essays_link(['q' => '', 'language' => '', 'task_type' => '', 'scope' => 'mine', 'reviewed' => '', 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php if (!$rows): ?>
    <p class="muted">No submissions match these filters.</p>
  <?php else: ?>
    <div class="ess-list">
      <?php foreach ($rows as $es):
        $taskLabel = ['karangan' => 'Karangan', 'rumusan' => 'Rumusan', 'tatabahasa' => 'Tatabahasa'][$es['task_type']] ?? $es['task_type'];
        $isReviewed = $reviewColsReady && !empty($es['reviewed_at']);
        $finalScore = (int) ($es['teacher_score_override'] ?? $es['score'] ?? 0);
        $maxScore   = (int) ($es['max_score'] ?? 0);
        $rubric     = json_decode((string) $es['rubric_json'],     true) ?: [];
        $strengths  = json_decode((string) $es['strengths_json'],  true) ?: [];
        $weaknesses = json_decode((string) $es['weaknesses_json'], true) ?: [];
        $suggest    = json_decode((string) $es['suggestions_json'], true) ?: [];
        $errors     = json_decode((string) $es['errors_json'],     true) ?: [];
      ?>
        <details class="ess-row<?= $isReviewed ? ' ess-row--reviewed' : '' ?>">
          <summary>
            <div class="ess-row__main">
              <div class="ess-row__name">
                <?= e($es['student_name']) ?>
                <span class="badge" style="margin-left:6px;font-size:10px"><?= e($taskLabel) ?></span>
                <span class="badge" style="margin-left:4px;font-size:10px"><?= e(strtoupper($es['language'])) ?></span>
                <?php if ($isReviewed): ?>
                  <span class="badge badge--good" style="margin-left:4px;font-size:10px">Reviewed</span>
                <?php else: ?>
                  <span class="badge badge--warn" style="margin-left:4px;font-size:10px">Awaiting review</span>
                <?php endif; ?>
              </div>
              <div class="ess-row__meta muted">
                <?php if ($es['prompt']): ?>
                  <em>"<?= e(mb_substr((string) $es['prompt'], 0, 140)) ?>"</em>
                <?php else: ?>
                  <em>(no prompt)</em>
                <?php endif; ?>
                · <?= (int) ($es['word_count'] ?? 0) ?> words · <?= e(date('d M, H:i', strtotime($es['created_at']))) ?>
              </div>
            </div>
            <div class="ess-row__score">
              <div class="ess-row__score-num"><?= $finalScore ?></div>
              <div class="ess-row__score-max">/ <?= $maxScore ?></div>
              <?php if (!empty($es['teacher_score_override'])): ?>
                <div class="muted" style="font-size:10px">(was <?= (int) $es['score'] ?>)</div>
              <?php endif; ?>
              <?php if (!empty($es['band'])): ?>
                <div class="muted" style="font-size:11px"><?= e((string) $es['band']) ?></div>
              <?php endif; ?>
            </div>
            <span class="ess-row__expand muted">▾</span>
          </summary>

          <div class="ess-row__detail">
            <?php if ($rubric): ?>
              <h4 class="ess-section-title">AI rubric</h4>
              <div class="rubric-grid">
                <?php foreach ($rubric as $key => $b):
                  $s = (int) ($b['score'] ?? 0);
                  $m = max(1, (int) ($b['max'] ?? 0));
                  $pct = (int) round(($s / $m) * 100);
                  $label = ucwords(str_replace('_', ' ', (string) $key));
                ?>
                  <div class="rubric-row">
                    <div><strong><?= e($label) ?></strong></div>
                    <div><?= $s ?> / <?= $m ?></div>
                    <div>
                      <div class="rubric-bar"><div style="width:<?= $pct ?>%"></div></div>
                      <?php if (!empty($b['comment'])): ?>
                        <div class="muted" style="font-size:12px;margin-top:3px"><?= e((string) $b['comment']) ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if ($strengths || $weaknesses || $suggest): ?>
              <div class="grid grid--3" style="margin-top:14px">
                <?php if ($strengths): ?>
                  <div>
                    <h4 class="ess-section-title" style="color:var(--good)">✓ Strengths</h4>
                    <ul style="padding-left:18px;margin:0;font-size:13px;line-height:1.5">
                      <?php foreach ($strengths as $s): ?><li><?= e((string) $s) ?></li><?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>
                <?php if ($weaknesses): ?>
                  <div>
                    <h4 class="ess-section-title" style="color:var(--bad)">✗ Weaknesses</h4>
                    <ul style="padding-left:18px;margin:0;font-size:13px;line-height:1.5">
                      <?php foreach ($weaknesses as $s): ?><li><?= e((string) $s) ?></li><?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>
                <?php if ($suggest): ?>
                  <div>
                    <h4 class="ess-section-title" style="color:var(--primary)">→ Suggestions</h4>
                    <ul style="padding-left:18px;margin:0;font-size:13px;line-height:1.5">
                      <?php foreach ($suggest as $s): ?><li><?= e((string) $s) ?></li><?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if ($errors): ?>
              <h4 class="ess-section-title" style="margin-top:14px">Errors flagged (<?= count($errors) ?>)</h4>
              <div class="err-list">
                <?php foreach (array_slice($errors, 0, 8) as $err): ?>
                  <div class="err-row">
                    <code><?= e((string) ($err['original'] ?? '')) ?></code>
                    → <code class="fixed"><?= e((string) ($err['corrected'] ?? '')) ?></code>
                    <?php if (!empty($err['rule'])): ?>
                      <div class="muted" style="font-size:11px;margin-top:3px"><?= e((string) $err['rule']) ?></div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
                <?php if (count($errors) > 8): ?>
                  <p class="muted" style="font-size:12px;margin:4px 0 0">+ <?= count($errors) - 8 ?> more not shown</p>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <h4 class="ess-section-title" style="margin-top:14px">Student submission</h4>
            <pre class="ess-text"><?= e((string) $es['submission']) ?></pre>

            <?php if ($es['source_passage']): ?>
              <h4 class="ess-section-title" style="margin-top:14px">Source passage (for rumusan)</h4>
              <pre class="ess-text ess-text--source"><?= e((string) $es['source_passage']) ?></pre>
            <?php endif; ?>

            <?php if ($reviewColsReady): ?>
              <h4 class="ess-section-title" style="margin-top:18px">Your review</h4>
              <form method="post" class="ess-review-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="review">
                <input type="hidden" name="essay_id" value="<?= (int) $es['id'] ?>">
                <input type="hidden" name="return" value="<?= e(http_build_query(['scope' => $scope, 'reviewed' => $reviewedF, 'language' => $language, 'task_type' => $taskType, 'page' => $page])) ?>">
                <div class="grid grid--2">
                  <div class="field">
                    <label>Override score <span class="muted" style="font-size:12px">(leave blank to accept AI score <?= (int) ($es['score'] ?? 0) ?>/<?= $maxScore ?>)</span></label>
                    <input class="input" type="number" name="override_score" min="0" max="<?= $maxScore ?>" value="<?= e((string) ($es['teacher_score_override'] ?? '')) ?>">
                  </div>
                  <div class="field">
                    <label>Comment to student</label>
                    <textarea name="teacher_comment" rows="2" placeholder="Optional note..."><?= e((string) ($es['teacher_comment'] ?? '')) ?></textarea>
                  </div>
                </div>
                <button class="btn"><?= $isReviewed ? 'Update review' : 'Save review &amp; notify student' ?></button>
                <?php if ($isReviewed): ?>
                  <button class="btn btn--ghost" type="submit" formaction="?<?= e(http_build_query(['return' => http_build_query(['scope' => $scope, 'reviewed' => $reviewedF])])) ?>" name="action" value="unreview" onclick="return confirm('Clear your review?')">Clear review</button>
                  <span class="muted" style="font-size:12px;margin-left:8px">
                    Reviewed <?= e(date('d M Y, H:i', strtotime($es['reviewed_at']))) ?>
                  </span>
                <?php endif; ?>
              </form>
            <?php endif; ?>
          </div>
        </details>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(essays_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(essays_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
          <a class="btn btn--sm <?= $p === $page ? '' : 'btn--ghost' ?>" href="<?= e(essays_link(['page' => $p])) ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(essays_link(['page' => $page + 1])) ?>">Next ›</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(essays_link(['page' => $pages])) ?>">Last »</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<style>
.ess-list { display:flex; flex-direction:column; gap:8px; }
.ess-row {
  border:1px solid var(--border);
  border-radius:12px;
  background:var(--card-2);
}
.ess-row:hover { border-color:var(--primary); }
.ess-row--reviewed { border-left: 3px solid var(--good); }
.ess-row:not(.ess-row--reviewed) { border-left: 3px solid var(--warn); }

.ess-row > summary {
  display:flex; align-items:center; gap:14px;
  padding:14px 16px;
  cursor:pointer;
  list-style: none;
}
.ess-row > summary::-webkit-details-marker { display:none; }
.ess-row__main { flex:1; min-width:240px; }
.ess-row__name { font-weight:600; font-size:14px; display:flex; align-items:center; flex-wrap:wrap; }
.ess-row__meta { font-size:12px; margin-top:5px; line-height:1.5; }
.ess-row__score {
  display:flex; flex-direction:column; align-items:flex-end;
  min-width:80px;
}
.ess-row__score-num { font-size:24px; font-weight:800; color:var(--primary); line-height:1; }
.ess-row__score-max { font-size:14px; color:var(--muted); margin-top:2px; }
.ess-row__expand { font-size:14px; transition: transform .15s; }
.ess-row[open] .ess-row__expand { transform: rotate(180deg); }
.ess-row__detail {
  padding:16px;
  border-top:1px solid var(--border);
  background: var(--bg-2);
  border-bottom-left-radius:12px;
  border-bottom-right-radius:12px;
}
.ess-section-title { margin:0 0 8px; font-size:12px; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }

.rubric-grid { display:flex; flex-direction:column; gap:8px; }
.rubric-row { display:grid; grid-template-columns: 140px 60px 1fr; gap:10px; align-items:center; padding:6px 10px; background:var(--card-2); border-radius:8px; }
.rubric-bar { height:6px; background:var(--bg); border-radius:999px; overflow:hidden; }
.rubric-bar > div { height:100%; background:var(--primary); }

.err-list { display:flex; flex-direction:column; gap:6px; }
.err-row { padding:6px 10px; background:var(--card-2); border:1px solid var(--border); border-radius:8px; font-size:12px; }
.err-row code { color:var(--bad); background:rgba(251,113,133,.1); padding:1px 4px; border-radius:4px; }
.err-row code.fixed { color:var(--good); background:rgba(52,211,153,.1); }

.ess-text {
  white-space:pre-wrap;
  font-family: inherit; font-size:13px; line-height:1.6;
  padding:14px 16px;
  background:var(--card-2); border:1px solid var(--border); border-radius:10px;
  max-height: 360px; overflow:auto;
  margin:0;
}
.ess-text--source { background:#1c1738; }

.ess-review-form {
  padding:14px;
  background:var(--card-2); border:1px solid var(--border); border-radius:10px;
}

@media (max-width: 720px) {
  .ess-row > summary { flex-wrap:wrap; }
  .ess-row__score { width:100%; flex-direction:row; align-items:center; gap:6px; margin-top:6px; }
  .ess-row__score-num { font-size:20px; }
  .rubric-row { grid-template-columns: 1fr; gap:4px; }
}
</style>
<?php
teacher_layout_end();
