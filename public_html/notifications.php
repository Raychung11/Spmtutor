<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/notifications.php';
require_once __DIR__ . '/inc/student_layout.php';
require_once __DIR__ . '/inc/teacher_layout.php';
require_once __DIR__ . '/inc/parent_layout.php';
require_once __DIR__ . '/inc/admin_layout.php';
require_once __DIR__ . '/inc/school_layout.php';

$user = require_login();
$uid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    mark_all_read($uid);
    redirect('notifications.php');
}

// Mark everything read on view (after rendering we flip them), but keep the
// list's read/unread styling for this render.
$items = list_notifications($uid);

// Choose the layout matching the user's role.
[$start, $end] = match ($user['role']) {
    'teacher'      => ['teacher_layout_start', 'teacher_layout_end'],
    'parent'       => ['parent_layout_start', 'parent_layout_end'],
    'school_admin' => ['school_layout_start', 'school_layout_end'],
    'admin', 'creator' => ['admin_layout_start', 'admin_layout_end'],
    default        => ['student_layout_start', 'student_layout_end'],
};

$start('Notifications', $user, 'notifications.php');
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2 style="margin:0">Notifications</h2>
    <?php if ($items): ?>
      <form method="post"><?= csrf_field() ?><button class="btn btn--sm btn--ghost">Mark all read</button></form>
    <?php endif; ?>
  </div>
  <div style="margin-top:14px">
    <?php if (!$items): ?>
      <p class="muted">No notifications yet.</p>
    <?php else: foreach ($items as $n): ?>
      <div class="notif <?= $n['is_read'] ? '' : 'notif--unread' ?>">
        <strong><?= e($n['title']) ?></strong>
        <?php if ($n['body']): ?><div class="muted"><?= nl2br(e($n['body'])) ?></div><?php endif; ?>
        <div class="muted" style="font-size:12px;margin-top:4px"><?= e(date('d M Y, H:i', strtotime((string) $n['created_at']))) ?></div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>
<?php
$end();

// Flip to read now that the user has seen them.
mark_all_read($uid);
