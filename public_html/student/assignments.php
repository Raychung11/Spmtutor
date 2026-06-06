<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/classes.php';
require_once __DIR__ . '/../inc/helpers.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $assignmentId = input_int('assignment_id');
    $content      = input('content');
    $filePath     = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $filePath = store_upload($_FILES['file'], 'assignments');
    }
    if ($assignmentId && submit_assignment($assignmentId, $uid, $content, $filePath)) {
        flash('success', 'Assignment submitted.');
    } else {
        flash('error', 'Could not submit (not enrolled in this assignment\'s class).');
    }
    redirect('student/assignments.php');
}

$assignments = student_assignments($uid);

student_layout_start('Assignments', $user, 'assignments.php');
?>
<?php if (!$assignments): ?>
  <div class="card"><p class="muted">No assignments yet. When a teacher adds you to a class and sets work, it'll appear here.</p></div>
<?php else: foreach ($assignments as $a): $submitted = $a['submission_id'] !== null; ?>
  <div class="card" style="margin-bottom:18px">
    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px">
      <h3 style="margin:0"><?= e($a['title']) ?> <span class="muted" style="font-size:13px"><?= e($a['class_name']) ?></span></h3>
      <span class="badge <?= $submitted ? 'badge--good' : 'badge--warn' ?>"><?= $submitted ? e($a['submission_status']) : 'pending' ?></span>
    </div>
    <?php if ($a['description']): ?><p class="muted"><?= nl2br(e($a['description'])) ?></p><?php endif; ?>
    <?php if ($a['due_date']): ?><p class="muted">Due: <?= e(date('d M Y', strtotime((string)$a['due_date']))) ?></p><?php endif; ?>
    <?php if ($a['score'] !== null): ?><p>Score: <strong><?= e((string)$a['score']) ?></strong></p><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="assignment_id" value="<?= (int)$a['id'] ?>">
      <div class="field"><label>Your answer</label><textarea name="content" placeholder="Type your answer / working"></textarea></div>
      <div class="field"><label>Attach a file (optional)</label><input class="input" type="file" name="file" accept="image/*"></div>
      <button class="btn btn--sm"><?= $submitted ? 'Resubmit' : 'Submit' ?></button>
    </form>
  </div>
<?php endforeach; endif; ?>
<?php
student_layout_end();
