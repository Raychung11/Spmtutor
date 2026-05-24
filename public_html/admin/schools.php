<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/notifications.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');

    if ($action === 'approve') {
        $school = db_one('SELECT * FROM schools WHERE id = ?', [input_int('id')]);
        if ($school) {
            db_exec('UPDATE schools SET status = "active" WHERE id = ?', [$school['id']]);
            if ($school['owner_user_id']) {
                notify((int) $school['owner_user_id'], 'Your school has been approved 🎉', 'You can now invite teachers and enrol students.', 'school');
            }
            flash('success', $school['name'] . ' approved.');
        }
        redirect('admin/schools.php');
    }

    if ($action === 'create' || $action === 'update') {
        $name  = input('name');
        $type  = in_array(input('type'), ['school', 'learning_center'], true) ? input('type') : 'learning_center';
        $email = input('contact_email');
        $phone = input('phone');
        $status = in_array(input('status'), ['active', 'inactive', 'pending'], true) ? input('status') : 'active';

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Enter a valid contact email.');
        } elseif ($name === '') {
            flash('error', 'Name is required.');
        } elseif ($action === 'create') {
            db_exec(
                'INSERT INTO schools (name, type, contact_email, phone, status) VALUES (?,?,?,?,?)',
                [$name, $type, $email ?: null, $phone ?: null, $status]
            );
            flash('success', 'School / centre added.');
        } else {
            db_exec(
                'UPDATE schools SET name = ?, type = ?, contact_email = ?, phone = ?, status = ? WHERE id = ?',
                [$name, $type, $email ?: null, $phone ?: null, $status, input_int('id')]
            );
            flash('success', 'Updated.');
        }
    } elseif ($action === 'delete') {
        // Detach classes first (school_id is nullable), then delete the school.
        db_exec('UPDATE teacher_classes SET school_id = NULL WHERE school_id = ?', [input_int('id')]);
        db_exec('DELETE FROM schools WHERE id = ?', [input_int('id')]);
        flash('success', 'Deleted.');
    }
    redirect('admin/schools.php');
}

$editId = input_int('edit');
$edit   = $editId ? db_one('SELECT * FROM schools WHERE id = ?', [$editId]) : null;

$rows = db_all(
    'SELECT s.*, o.name AS owner_name,
            (SELECT COUNT(*) FROM teacher_classes c WHERE c.school_id = s.id) AS class_count,
            (SELECT COUNT(*) FROM school_members m WHERE m.school_id = s.id) AS member_count
     FROM schools s LEFT JOIN users o ON o.id = s.owner_user_id
     ORDER BY (s.status = "pending") DESC, s.id DESC'
);

admin_layout_start('Schools & Centres', $admin, 'schools.php');
?>
<div class="grid grid--2">
  <div class="card">
    <h3><?= $edit ? 'Edit' : 'Add' ?> school / learning centre</h3>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
      <div class="field"><label>Name</label><input class="input" name="name" value="<?= e($edit['name'] ?? '') ?>" required></div>
      <div class="field"><label>Type</label>
        <select name="type" class="input">
          <option value="learning_center" <?= ($edit['type'] ?? '') === 'learning_center' ? 'selected' : '' ?>>Learning centre</option>
          <option value="school" <?= ($edit['type'] ?? '') === 'school' ? 'selected' : '' ?>>School</option>
        </select>
      </div>
      <div class="field"><label>Contact email</label><input class="input" type="email" name="contact_email" value="<?= e($edit['contact_email'] ?? '') ?>"></div>
      <div class="field"><label>Phone</label><input class="input" name="phone" value="<?= e($edit['phone'] ?? '') ?>"></div>
      <div class="field"><label>Status</label>
        <select name="status" class="input">
          <option value="active" <?= ($edit['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="pending" <?= ($edit['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="inactive" <?= ($edit['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
      <button class="btn"><?= $edit ? 'Save changes' : 'Add' ?></button>
      <?php if ($edit): ?><a class="btn btn--ghost" href="<?= url('admin/schools.php') ?>">Cancel</a><?php endif; ?>
    </form>
  </div>
  <div class="card">
    <h3>All schools / centres</h3>
    <table class="table"><thead><tr><th>Name</th><th>Owner</th><th>Members</th><th>Status</th><th></th></tr></thead><tbody>
    <?php foreach ($rows as $s): ?>
      <tr>
        <td><?= e($s['name']) ?><br><span class="muted" style="font-size:12px"><?= e($s['type'] === 'school' ? 'School' : 'Centre') ?><?= $s['contact_email'] ? ' · ' . e($s['contact_email']) : '' ?></span></td>
        <td class="muted"><?= e($s['owner_name'] ?? '—') ?></td>
        <td><?= (int)$s['member_count'] ?></td>
        <td><span class="badge <?= $s['status'] === 'active' ? 'badge--good' : 'badge--warn' ?>"><?= e($s['status']) ?></span></td>
        <td style="white-space:nowrap">
          <?php if ($s['status'] === 'pending'): ?>
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
              <button class="btn btn--sm">Approve</button>
            </form>
          <?php endif; ?>
          <a class="btn btn--sm btn--ghost" href="<?= url('admin/schools.php?edit=' . (int)$s['id']) ?>">Edit</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this school? Its classes will be detached.')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="btn btn--sm btn--danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="5" class="muted">No schools yet.</td></tr><?php endif; ?>
    </tbody></table>
  </div>
</div>
<?php
admin_layout_end();
