<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/schools.php';
require_once __DIR__ . '/../inc/school_layout.php';

$user   = require_role('school_admin');
$school = current_school((int) $user['id']);

school_layout_start('School Dashboard', $user, 'dashboard.php');

if (!$school) {
    echo '<div class="card"><p class="muted">No school is linked to your account. Please contact support.</p></div>';
    school_layout_end();
    return;
}

$teachers = school_member_count((int) $school['id'], 'teacher');
$students = school_member_count((int) $school['id'], 'student');
$stats    = school_analytics((int) $school['id']);
?>
<?php if ($school['status'] === 'pending'): ?>
  <div class="flash flash--info"><strong><?= e($school['name']) ?> is pending approval.</strong> You can explore the portal now; inviting teachers and students unlocks once an administrator approves your school.</div>
<?php elseif ($school['status'] !== 'active'): ?>
  <div class="flash flash--error">Your school is currently <?= e($school['status']) ?>. Please contact support.</div>
<?php endif; ?>

<div class="card">
  <h2><?= e($school['name']) ?> <span class="badge <?= $school['status'] === 'active' ? 'badge--good' : 'badge--warn' ?>"><?= e($school['status']) ?></span></h2>
  <p class="muted"><?= e($school['type'] === 'school' ? 'School' : 'Learning centre') ?> · <?= e($school['contact_email'] ?? '') ?></p>
</div>

<div class="grid grid--4" style="margin-top:18px">
  <div class="card stat"><div class="stat__value"><?= $teachers ?></div><div class="stat__label">Teachers</div></div>
  <div class="card stat"><div class="stat__value"><?= $students ?></div><div class="stat__label">Students</div></div>
  <div class="card stat"><div class="stat__value"><?= e(number_format($stats['avg_score'], 0)) ?>%</div><div class="stat__label">Avg score</div></div>
  <div class="card stat"><div class="stat__value"><?= $stats['active_today'] ?></div><div class="stat__label">Active today</div></div>
</div>

<div class="card" style="margin-top:18px">
  <h3>Quick actions</h3>
  <a class="btn btn--sm" href="<?= url('school/teachers.php') ?>">Manage teachers</a>
  <a class="btn btn--sm btn--ghost" href="<?= url('school/students.php') ?>">Manage students</a>
  <a class="btn btn--sm btn--ghost" href="<?= url('school/analytics.php') ?>">View analytics</a>
  <a class="btn btn--sm btn--ghost" href="<?= url('school/billing.php') ?>">Billing</a>
</div>
<?php
school_layout_end();
