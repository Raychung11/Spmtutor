<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/schools.php';
require_once __DIR__ . '/../inc/teacher_layout.php';

$user = require_role('teacher');
$uid  = (int) $user['id'];

// ---- Scope: my students (via teacher_classes -> class_students) ----
$myStudentIds = array_map(
    fn($r) => (int) $r['id'],
    db_all(
        'SELECT DISTINCT cs.student_user_id AS id
         FROM teacher_classes tc JOIN class_students cs ON cs.class_id = tc.id
         WHERE tc.teacher_user_id = ?',
        [$uid]
    )
);
$hasStudents = (bool) $myStudentIds;
$studentScope = $hasStudents ? $myStudentIds : null;

// ---- Action counts ----
$essaysToReview = 0;
$essaysReady = (bool) db_one(
    "SELECT 1 AS x FROM information_schema.TABLES
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'essay_submissions'"
);
$reviewColsReady = $essaysReady && (bool) db_one(
    "SELECT 1 AS x FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'essay_submissions' AND COLUMN_NAME = 'reviewed_at'"
);
if ($reviewColsReady) {
    if ($studentScope) {
        $place = implode(',', array_fill(0, count($studentScope), '?'));
        $essaysToReview = (int) (db_one(
            "SELECT COUNT(*) c FROM essay_submissions
             WHERE status = 'marked' AND reviewed_at IS NULL AND user_id IN ($place)",
            $studentScope
        )['c'] ?? 0);
    } else {
        // No classes yet — show all
        $essaysToReview = (int) (db_one(
            "SELECT COUNT(*) c FROM essay_submissions WHERE status = 'marked' AND reviewed_at IS NULL"
        )['c'] ?? 0);
    }
}

// Snap & Check uploads awaiting review.
$snapCount = 0;
if ($studentScope) {
    $place = implode(',', array_fill(0, count($studentScope), '?'));
    $snapCount = (int) (db_one(
        "SELECT COUNT(*) c FROM answer_uploads
         WHERE status IN ('uploaded','marked') AND user_id IN ($place)",
        $studentScope
    )['c'] ?? 0);
} else {
    $snapCount = (int) (db_one(
        "SELECT COUNT(*) c FROM answer_uploads WHERE status IN ('uploaded','marked')"
    )['c'] ?? 0);
}

// Students needing attention: low average, no recent activity, or zero activity.
// Each student gets a list of reasons; we use that for the dashboard counter and the table flag.
$studentsRaw = [];
if ($studentScope) {
    $place = implode(',', array_fill(0, count($studentScope), '?'));
    $studentsRaw = db_all(
        "SELECT u.id, u.name, u.email,
                COALESCE(ps.total_questions,0) AS answered,
                COALESCE(ps.avg_score,0) AS avg_score,
                COALESCE(st.current_streak,0) AS streak,
                (SELECT MAX(attempted_at) FROM question_attempts WHERE user_id = u.id) AS last_attempt
         FROM users u
         LEFT JOIN student_progress_summary ps ON ps.user_id = u.id
         LEFT JOIN student_streaks st ON st.user_id = u.id
         WHERE u.id IN ($place) AND u.status = 'active'",
        $studentScope
    );
} else {
    // Fall back to all active students if teacher has no classes set up.
    $studentsRaw = db_all(
        "SELECT u.id, u.name, u.email,
                COALESCE(ps.total_questions,0) AS answered,
                COALESCE(ps.avg_score,0) AS avg_score,
                COALESCE(st.current_streak,0) AS streak,
                (SELECT MAX(attempted_at) FROM question_attempts WHERE user_id = u.id) AS last_attempt
         FROM users u
         LEFT JOIN student_progress_summary ps ON ps.user_id = u.id
         LEFT JOIN student_streaks st ON st.user_id = u.id
         WHERE u.role = 'student' AND u.status = 'active'
         LIMIT 100"
    );
}

// Annotate each student with attention reasons.
$now = time();
$students = [];
$studentsAtRisk = 0;
foreach ($studentsRaw as $s) {
    $reasons = [];
    $lastTs = $s['last_attempt'] ? strtotime($s['last_attempt']) : null;
    if ((int) $s['answered'] === 0) {
        $reasons[] = 'No activity yet';
    } elseif ($lastTs && ($now - $lastTs) > 7 * 86400) {
        $days = (int) floor(($now - $lastTs) / 86400);
        $reasons[] = $days . 'd silent';
    }
    if ((float) $s['avg_score'] > 0 && (float) $s['avg_score'] < 50) {
        $reasons[] = 'Low avg ' . (int) round((float) $s['avg_score']) . '%';
    }
    $s['attention'] = $reasons;
    $students[] = $s;
    if ($reasons) {
        $studentsAtRisk++;
    }
}

// Sort: attention first, then by avg ASC so weakest at top.
usort($students, function ($a, $b) {
    $ra = count($a['attention']);
    $rb = count($b['attention']);
    if ($ra !== $rb) return $rb <=> $ra;
    return ((float) $a['avg_score']) <=> ((float) $b['avg_score']);
});

$pendingUploads = db_all(
    'SELECT au.id, u.name, au.created_at FROM answer_uploads au
     JOIN users u ON u.id = au.user_id WHERE au.status IN ("uploaded","marked") ORDER BY au.id DESC LIMIT 5'
);
$schoolLinks = user_schools($uid, 'teacher');

function rel_days_teacher(?string $when): string
{
    if (!$when) return 'never';
    $t = strtotime($when);
    if (!$t) return 'never';
    $diff = time() - $t;
    if ($diff < 3600)        return floor($diff / 60) . 'm ago';
    if ($diff < 86400)       return floor($diff / 3600) . 'h ago';
    if ($diff < 86400 * 30)  return floor($diff / 86400) . 'd ago';
    return date('d M Y', $t);
}

teacher_layout_start('Teacher Dashboard', $user, 'dashboard.php');
?>
<?php if ($schoolLinks): ?>
  <div class="card" style="margin-bottom:18px">
    <h3 style="margin-top:0">My school</h3>
    <?php foreach ($schoolLinks as $sl): ?>
      <p style="margin:6px 0"><strong><?= e($sl['name']) ?></strong>
        <span class="badge <?= $sl['status'] === 'active' ? 'badge--good' : 'badge--warn' ?>"><?= $sl['status'] === 'active' ? 'joined' : 'pending approval' ?></span>
        <span class="muted" style="font-size:13px">&middot; <?= e($sl['type'] === 'school' ? 'School' : 'Learning centre') ?></span>
      </p>
      <?php if ($sl['status'] !== 'active'): ?>
        <p class="muted" style="font-size:13px;margin:0">Your join request is waiting for the school admin to approve it.</p>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<h3 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Today's actions</h3>
<div class="grid grid--3 actions-grid">
  <a class="action-card action-card--<?= $essaysToReview > 0 ? 'live' : 'idle' ?>" href="<?= url('teacher/essays.php?reviewed=no') ?>">
    <div class="action-card__num"><?= $essaysToReview ?></div>
    <div class="action-card__label">Essays to review</div>
    <div class="action-card__hint muted">
      <?= $essaysToReview > 0 ? 'Open Essay Reviews →' : 'All caught up' ?>
    </div>
  </a>

  <a class="action-card action-card--<?= $snapCount > 0 ? 'live' : 'idle' ?>" href="<?= url('teacher/review.php') ?>">
    <div class="action-card__num"><?= $snapCount ?></div>
    <div class="action-card__label">Snap &amp; Check uploads</div>
    <div class="action-card__hint muted">
      <?= $snapCount > 0 ? 'Open review queue →' : 'No image uploads waiting' ?>
    </div>
  </a>

  <a class="action-card action-card--<?= $studentsAtRisk > 0 ? 'live' : 'idle' ?>" href="#students">
    <div class="action-card__num"><?= $studentsAtRisk ?></div>
    <div class="action-card__label">Students need attention</div>
    <div class="action-card__hint muted">
      <?= $studentsAtRisk > 0 ? 'Scroll to the list ↓' : 'Everyone is on track' ?>
    </div>
  </a>
</div>

<?php if (!$hasStudents): ?>
<div class="flash flash--info" style="margin-top:14px">
  You don't have any students enrolled in your classes yet. Showing all platform students for now.
  Add students via <a href="<?= url('teacher/classes.php') ?>">Teacher → Classes</a> so the counts above scope to just yours.
</div>
<?php endif; ?>

<div class="card" id="students" style="margin-top:18px">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0"><?= $hasStudents ? 'My students' : 'All students' ?> <span class="muted" style="font-size:14px">(<?= count($students) ?>)</span></h3>
    <?php if ($studentsAtRisk > 0): ?>
      <span class="badge badge--warn"><?= $studentsAtRisk ?> need attention</span>
    <?php endif; ?>
  </div>

  <?php if (!$students): ?>
    <p class="muted">No students yet.</p>
  <?php else: ?>
    <div class="stu-list">
      <?php foreach ($students as $s):
        $atRisk = !empty($s['attention']);
      ?>
        <div class="stu-row<?= $atRisk ? ' stu-row--risk' : '' ?>">
          <div class="stu-row__main">
            <div class="stu-row__name">
              <?= e($s['name']) ?>
              <?php foreach ($s['attention'] as $r): ?>
                <span class="badge badge--warn" style="margin-left:4px;font-size:10px"><?= e($r) ?></span>
              <?php endforeach; ?>
            </div>
            <div class="stu-row__meta muted">
              <?= e($s['email']) ?> · last active <?= e(rel_days_teacher($s['last_attempt'])) ?>
            </div>
          </div>
          <div class="stu-row__stat">
            <span class="muted">Answered</span>
            <strong><?= (int) $s['answered'] ?></strong>
          </div>
          <div class="stu-row__stat">
            <span class="muted">Avg</span>
            <strong style="<?= (float) $s['avg_score'] >= 70 ? 'color:var(--good)' : ((float) $s['avg_score'] < 50 && $s['answered'] > 0 ? 'color:var(--bad)' : '') ?>">
              <?= e(number_format((float) $s['avg_score'], 0)) ?>%
            </strong>
          </div>
          <div class="stu-row__stat">
            <span class="muted">Streak</span>
            <strong><?= (int) $s['streak'] ?> 🔥</strong>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php if ($pendingUploads): ?>
<div class="card" style="margin-top:18px">
  <h3 style="margin-top:0">Latest Snap &amp; Check uploads</h3>
  <p><a class="btn btn--sm" href="<?= url('teacher/review.php') ?>">Open review queue</a></p>
  <table class="table"><tbody>
  <?php foreach ($pendingUploads as $u): ?>
    <tr><td><?= e($u['name']) ?></td><td class="muted"><?= e(date('d M, H:i', strtotime($u['created_at']))) ?></td></tr>
  <?php endforeach; ?>
  </tbody></table>
</div>
<?php endif; ?>

<style>
.actions-grid { gap: 14px; }
.action-card {
  display:flex; flex-direction:column; gap:8px;
  padding:18px 20px;
  background:var(--card-2);
  border:1px solid var(--border);
  border-radius:14px;
  text-decoration:none;
  color:inherit;
  transition: border-color .15s, transform .15s, box-shadow .15s;
}
.action-card:hover { border-color:var(--primary); transform: translateY(-1px); box-shadow:0 4px 18px rgba(139,92,246,.12); }
.action-card__num { font-size:42px; font-weight:800; line-height:1; color:var(--primary); }
.action-card__label { font-size:14px; font-weight:600; }
.action-card__hint { font-size:12px; }
.action-card--idle .action-card__num { color: var(--muted); }
.action-card--live { border-left: 3px solid var(--primary); }

.stu-list { display:flex; flex-direction:column; gap:6px; }
.stu-row {
  display:flex; align-items:center; gap:14px;
  padding:10px 14px;
  border:1px solid var(--border);
  border-radius:10px;
  background:var(--card-2);
}
.stu-row:hover { border-color:var(--primary); }
.stu-row--risk { border-left: 3px solid var(--warn); }
.stu-row__main { flex:1; min-width:200px; }
.stu-row__name { font-weight:600; font-size:14px; display:flex; align-items:center; flex-wrap:wrap; }
.stu-row__meta { font-size:12px; margin-top:3px; }
.stu-row__stat {
  display:flex; flex-direction:column; align-items:center;
  width:80px; text-align:center; font-size:12px;
}
.stu-row__stat strong { font-size:14px; font-weight:700; margin:2px 0; color:var(--text); }
@media (max-width: 720px) {
  .stu-row { flex-wrap:wrap; }
  .stu-row__stat { width:auto; min-width:64px; flex:1; }
}
</style>
<?php
teacher_layout_end();
