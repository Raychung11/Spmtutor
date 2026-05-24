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
        redirect('school/teachers.php');
    }
    if (input('action') === 'add') {
        [$ok, $msg] = add_school_member($sid, strtolower(input('email')), 'teacher');
        flash($ok ? 'success' : 'error', $msg);
    } elseif (input('action') === 'remove') {
        remove_school_member($sid, input_int('member_id'));
        flash('success', 'Teacher removed from school.');
    }
    redirect('school/teachers.php');
}

$teachers = school_members($sid, 'teacher');

school_layout_start('Teachers', $user, 'teachers.php');
?>
<?php if (!school_approved($school)): ?>
  <div class="flash flash--info">Inviting teachers unlocks once your school is approved.</div>
<?php endif; ?>
<div class="card">
  <h3>Add a teacher</h3>
  <p class="muted">The teacher must already have a teacher account. Enter their email to add them to <?= e($school['name']) ?>.</p>
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add">
    <div class="field" style="margin:0;flex:1;min-width:220px"><label>Teacher email</label><input class="input" type="email" name="email" required <?= school_approved($school) ? '' : 'disabled' ?>></div>
    <button class="btn" <?= school_approved($school) ? '' : 'disabled' ?>>Add teacher</button>
  </form>
</div>

<div class="card" style="margin-top:18px">
  <h3>Teachers (<?= count($teachers) ?>)</h3>
  <table class="table"><thead><tr><th>Name</th><th>Email</th><th>Classes</th><th></th></tr></thead><tbody>
  <?php foreach ($teachers as $t): ?>
    <tr>
      <td><?= e($t['name']) ?></td>
      <td class="muted"><?= e($t['email']) ?></td>
      <td><?= (int)$t['class_count'] ?></td>
      <td>
        <form method="post" onsubmit="return confirm('Remove this teacher from the school?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="remove">
          <input type="hidden" name="member_id" value="<?= (int)$t['member_id'] ?>">
          <button class="btn btn--sm btn--danger">Remove</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$teachers): ?><tr><td colspan="4" class="muted">No teachers yet.</td></tr><?php endif; ?>
  </tbody></table>
</div>
<?php
school_layout_end();
