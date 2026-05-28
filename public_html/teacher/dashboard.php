<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/schools.php';
require_once __DIR__ . '/../inc/teacher_layout.php';

$user = require_role('teacher');
$uid  = (int) $user['id'];

// Aggregate view of all students (class management is Phase 4).
$students = db_all(
    'SELECT u.id, u.name, u.email,
            COALESCE(ps.total_questions,0) AS answered,
            COALESCE(ps.avg_score,0) AS avg_score,
            COALESCE(st.current_streak,0) AS streak
     FROM users u
     LEFT JOIN student_progress_summary ps ON ps.user_id = u.id
     LEFT JOIN student_streaks st ON st.user_id = u.id
     WHERE u.role = "student" AND u.status = "active"
     ORDER BY ps.avg_score DESC LIMIT 100'
);
$pendingUploads = db_all(
    'SELECT au.id, u.name, au.created_at FROM answer_uploads au
     JOIN users u ON u.id = au.user_id WHERE au.status IN ("uploaded","marked") ORDER BY au.id DESC LIMIT 10'
);
$schoolLinks = user_schools($uid, 'teacher');

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

<div class="card">
  <h3>Students</h3>
  <table class="table">
    <thead><tr><th>Name</th><th>Email</th><th>Answered</th><th>Avg</th><th>Streak</th></tr></thead>
    <tbody>
    <?php foreach ($students as $s): ?>
      <tr>
        <td><?= e($s['name']) ?></td>
        <td class="muted"><?= e($s['email']) ?></td>
        <td><?= (int)$s['answered'] ?></td>
        <td><?= e(number_format((float)$s['avg_score'],0)) ?>%</td>
        <td><?= (int)$s['streak'] ?> 🔥</td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$students): ?><tr><td colspan="5" class="muted">No students yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="card" style="margin-top:18px">
  <h3>AI marking awaiting review</h3>
  <p><a class="btn btn--sm" href="<?= url('teacher/review.php') ?>">Open review queue</a></p>
  <?php if ($pendingUploads): ?>
    <table class="table"><tbody>
    <?php foreach ($pendingUploads as $u): ?>
      <tr><td><?= e($u['name']) ?></td><td class="muted"><?= e(date('d M, H:i', strtotime($u['created_at']))) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table>
  <?php else: ?>
    <p class="muted">Nothing to review. Snap &amp; Check uploads (Phase 3) will appear here for override.</p>
  <?php endif; ?>
</div>
<?php
teacher_layout_end();
