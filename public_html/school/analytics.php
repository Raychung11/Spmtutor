<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/schools.php';
require_once __DIR__ . '/../inc/school_layout.php';

$user   = require_role('school_admin');
$school = current_school((int) $user['id']);
if (!$school) {
    redirect('school/dashboard.php');
}
$stats = school_analytics((int) $school['id']);

school_layout_start('School Analytics', $user, 'analytics.php');
?>
<div class="grid grid--4">
  <div class="card stat"><div class="stat__value"><?= (int)$stats['students'] ?></div><div class="stat__label">Students</div></div>
  <div class="card stat"><div class="stat__value"><?= e(number_format($stats['avg_score'], 0)) ?>%</div><div class="stat__label">Average score</div></div>
  <div class="card stat"><div class="stat__value"><?= (int)$stats['total_attempts'] ?></div><div class="stat__label">Total attempts</div></div>
  <div class="card stat"><div class="stat__value"><?= (int)$stats['active_today'] ?></div><div class="stat__label">Active today</div></div>
</div>

<div class="grid grid--2" style="margin-top:18px">
  <div class="card">
    <h3>Weakest topics (school-wide)</h3>
    <?php if ($stats['weak_topics']): ?>
      <?php foreach ($stats['weak_topics'] as $w): ?>
        <p style="margin:6px 0"><?= e($w['name']) ?> <span class="muted">(<?= e(number_format((float)$w['m'], 0)) ?>%)</span></p>
        <div class="progress-bar"><span style="width: <?= (int)round((float)$w['m']) ?>%"></span></div>
      <?php endforeach; ?>
    <?php else: ?><p class="muted">Not enough data yet.</p><?php endif; ?>
  </div>
  <div class="card">
    <h3>Top students</h3>
    <?php if ($stats['top_students']): ?>
      <table class="table"><tbody>
      <?php foreach ($stats['top_students'] as $s): ?>
        <tr><td><?= e($s['name']) ?></td><td><?= e(number_format((float)$s['avg_score'], 0)) ?>%</td></tr>
      <?php endforeach; ?>
      </tbody></table>
    <?php else: ?><p class="muted">No student data yet.</p><?php endif; ?>
  </div>
</div>
<?php
school_layout_end();
