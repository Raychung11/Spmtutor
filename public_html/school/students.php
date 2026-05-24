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
$sid = (int) $school['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!school_approved($school)) {
        flash('error', 'Your school must be approved before managing members.');
        redirect('school/students.php');
    }
    if (input('action') === 'add') {
        [$ok, $msg] = add_school_member($sid, strtolower(input('email')), 'student');
        flash($ok ? 'success' : 'error', $msg);
    } elseif (input('action') === 'remove') {
        remove_school_member($sid, input_int('member_id'));
        flash('success', 'Student removed from school.');
    }
    redirect('school/students.php');
}

$students = school_members($sid, 'student');

school_layout_start('Students', $user, 'students.php');
?>
<?php if (!school_approved($school)): ?>
  <div class="flash flash--info">Enrolling students unlocks once your school is approved.</div>
<?php endif; ?>
<div class="card">
  <h3>Enrol a student</h3>
  <p class="muted">The student must already have a student account. Enter their email to enrol them.</p>
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add">
    <div class="field" style="margin:0;flex:1;min-width:220px"><label>Student email</label><input class="input" type="email" name="email" required <?= school_approved($school) ? '' : 'disabled' ?>></div>
    <button class="btn" <?= school_approved($school) ? '' : 'disabled' ?>>Enrol student</button>
  </form>
</div>

<div class="card" style="margin-top:18px">
  <h3>Students (<?= count($students) ?>)</h3>
  <table class="table"><thead><tr><th>Name</th><th>Email</th><th>Answered</th><th>Avg</th><th>Streak</th><th></th></tr></thead><tbody>
  <?php foreach ($students as $s): ?>
    <tr>
      <td><?= e($s['name']) ?></td>
      <td class="muted"><?= e($s['email']) ?></td>
      <td><?= (int)$s['answered'] ?></td>
      <td><?= e(number_format((float)$s['avg_score'], 0)) ?>%</td>
      <td><?= (int)$s['streak'] ?>🔥</td>
      <td>
        <form method="post" onsubmit="return confirm('Remove this student from the school?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="remove">
          <input type="hidden" name="member_id" value="<?= (int)$s['member_id'] ?>">
          <button class="btn btn--sm btn--danger">Remove</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$students): ?><tr><td colspan="6" class="muted">No students yet.</td></tr><?php endif; ?>
  </tbody></table>
</div>
<?php
school_layout_end();
