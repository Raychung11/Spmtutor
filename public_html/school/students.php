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
    $a = input('action');
    if ($a === 'add') {
        [$ok, $msg] = invite_or_add_member($sid, strtolower(input('email')), 'student', (int) $user['id']);
        flash($ok ? 'success' : 'error', $msg);
    } elseif ($a === 'remove') {
        remove_school_member($sid, input_int('member_id'));
        flash('success', 'Student removed from school.');
    } elseif ($a === 'revoke_invite') {
        revoke_invitation($sid, input_int('invite_id'));
        flash('success', 'Invitation revoked.');
    } elseif ($a === 'resend_invite') {
        resend_invitation($sid, input_int('invite_id'));
        flash('success', 'Invitation resent.');
    } elseif ($a === 'approve_request') {
        approve_member_request($sid, input_int('member_id'));
        flash('success', 'Join request approved.');
    } elseif ($a === 'decline_request') {
        remove_school_member($sid, input_int('member_id'));
        flash('success', 'Join request declined.');
    }
    redirect('school/students.php');
}

$students = school_members($sid, 'student');
$invites  = array_filter(pending_invitations($sid), fn($i) => $i['member_role'] === 'student');
$requests = pending_member_requests($sid, 'student');

school_layout_start('Students', $user, 'students.php');
?>
<?php if (!school_approved($school)): ?>
  <div class="flash flash--info">Enrolling students unlocks once your school is approved.</div>
<?php endif; ?>
<div class="card">
  <h3>Enrol or invite a student</h3>
  <p class="muted">If they already have a student account they're enrolled instantly. Otherwise we email them an invitation to register and join.</p>
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add">
    <div class="field" style="margin:0;flex:1;min-width:220px"><label>Student email</label><input class="input" type="email" name="email" required <?= school_approved($school) ? '' : 'disabled' ?>></div>
    <button class="btn" <?= school_approved($school) ? '' : 'disabled' ?>>Enrol / invite</button>
  </form>
</div>

<?php if ($requests): ?>
  <div class="card" style="margin-top:18px">
    <h3>Pending join requests (<?= count($requests) ?>)</h3>
    <p class="muted" style="font-size:13px">Students who registered and asked to join your school.</p>
    <table class="table"><thead><tr><th>Name</th><th>Email</th><th>Requested</th><th></th></tr></thead><tbody>
    <?php foreach ($requests as $r): ?>
      <tr>
        <td><?= e($r['name']) ?></td>
        <td class="muted"><?= e($r['email']) ?></td>
        <td class="muted"><?= e(date('d M', strtotime((string)$r['created_at']))) ?></td>
        <td style="white-space:nowrap">
          <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="approve_request"><input type="hidden" name="member_id" value="<?= (int)$r['member_id'] ?>"><button class="btn btn--sm">Approve</button></form>
          <form method="post" style="display:inline" onsubmit="return confirm('Decline this join request?')"><?= csrf_field() ?><input type="hidden" name="action" value="decline_request"><input type="hidden" name="member_id" value="<?= (int)$r['member_id'] ?>"><button class="btn btn--sm btn--danger">Decline</button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  </div>
<?php endif; ?>

<?php if ($invites): ?>
  <div class="card" style="margin-top:18px">
    <h3>Pending invitations (<?= count($invites) ?>)</h3>
    <table class="table"><thead><tr><th>Email</th><th>Invited</th><th>Expires</th><th></th></tr></thead><tbody>
    <?php foreach ($invites as $inv): ?>
      <tr>
        <td><?= e($inv['email']) ?></td>
        <td class="muted"><?= e(date('d M', strtotime((string)$inv['created_at']))) ?></td>
        <td class="muted"><?= $inv['expires_at'] ? e(date('d M', strtotime((string)$inv['expires_at']))) : '—' ?></td>
        <td style="white-space:nowrap">
          <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="resend_invite"><input type="hidden" name="invite_id" value="<?= (int)$inv['id'] ?>"><button class="btn btn--sm btn--ghost">Resend</button></form>
          <form method="post" style="display:inline" onsubmit="return confirm('Revoke this invitation?')"><?= csrf_field() ?><input type="hidden" name="action" value="revoke_invite"><input type="hidden" name="invite_id" value="<?= (int)$inv['id'] ?>"><button class="btn btn--sm btn--danger">Revoke</button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  </div>
<?php endif; ?>

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
