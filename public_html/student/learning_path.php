<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/learning.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $itemId = input_int('item_id');
    $status = input('status');
    if ($itemId && set_path_item_status($uid, $itemId, $status)) {
        flash('success', 'Updated.');
    }
    redirect('student/learning_path.php');
}

$paths = active_learning_paths($uid);

student_layout_start('Learning Path', $user, 'learning_path.php');
?>
<?php if (!$paths): ?>
  <div class="card">
    <h2>No learning path yet</h2>
    <p class="muted">Take a diagnostic and we'll build a personalised, adaptive plan focused on your weak topics.</p>
    <a class="btn" href="<?= url('student/diagnostic.php') ?>">Take a diagnostic</a>
  </div>
<?php else: ?>
  <?php foreach ($paths as $p): $items = learning_path_items((int) $p['id']); $pct = path_progress((int) $p['id']); ?>
    <div class="card" style="margin-bottom:18px">
      <h2><?= e($p['title']) ?> <span class="muted" style="font-size:14px"><?= e($p['subject'] ?? '') ?></span></h2>
      <?php if ($p['target_date']): ?><p class="muted">Exam target: <?= e(date('d M Y', strtotime((string) $p['target_date']))) ?></p><?php endif; ?>
      <div class="progress-bar"><span style="width: <?= $pct ?>%"></span></div>
      <p class="muted" style="margin-top:6px"><?= $pct ?>% complete</p>
      <table class="table"><tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td>
            <?php if ($it['status'] === 'done'): ?>✅<?php elseif ($it['status'] === 'in_progress'): ?>🟡<?php else: ?>⬜<?php endif; ?>
            <?= e($it['title']) ?>
          </td>
          <td style="text-align:right">
            <form method="post" style="display:inline-flex;gap:6px">
              <?= csrf_field() ?>
              <input type="hidden" name="item_id" value="<?= (int) $it['id'] ?>">
              <?php if ($it['status'] !== 'in_progress'): ?>
                <button class="btn btn--sm btn--ghost" name="status" value="in_progress">Start</button>
              <?php endif; ?>
              <?php if ($it['status'] !== 'done'): ?>
                <button class="btn btn--sm" name="status" value="done">Done</button>
              <?php else: ?>
                <button class="btn btn--sm btn--ghost" name="status" value="pending">Reset</button>
              <?php endif; ?>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
<?php
student_layout_end();
