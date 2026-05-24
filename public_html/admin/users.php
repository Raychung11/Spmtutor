<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id     = input_int('id');
    $status = input('status');
    $role   = input('role');
    if ($id && $id !== (int) $admin['id'] && in_array($status, ['active', 'suspended', 'pending'], true)
        && in_array($role, ['student', 'parent', 'teacher', 'admin', 'creator'], true)) {
        db_exec('UPDATE users SET status = ?, role = ? WHERE id = ?', [$status, $role, $id]);
        flash('success', 'User updated.');
    } else {
        flash('error', 'Invalid update (you cannot change your own account here).');
    }
    redirect('admin/users.php');
}

$q     = input('q');
$rows  = $q
    ? db_all('SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT 200', ["%$q%", "%$q%"])
    : db_all('SELECT * FROM users ORDER BY id DESC LIMIT 200');

admin_layout_start('Users', $admin, 'users.php');
?>
<div class="card">
  <form method="get" style="display:flex;gap:10px;margin-bottom:16px">
    <input class="input" name="q" placeholder="Search name or email" value="<?= e($q) ?>">
    <button class="btn btn--sm">Search</button>
  </form>
  <table class="table">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Update</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $u): ?>
      <tr>
        <td><?= (int)$u['id'] ?></td>
        <td><?= e($u['name']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><span class="badge"><?= e($u['role']) ?></span></td>
        <td><span class="badge <?= $u['status'] === 'active' ? 'badge--good' : 'badge--warn' ?>"><?= e($u['status']) ?></span></td>
        <td>
          <form method="post" style="display:flex;gap:6px">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <select name="role" class="input" style="width:auto;padding:6px">
              <?php foreach (['student','parent','teacher','creator','admin'] as $r): ?>
                <option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= $r ?></option>
              <?php endforeach; ?>
            </select>
            <select name="status" class="input" style="width:auto;padding:6px">
              <?php foreach (['active','suspended','pending'] as $s): ?>
                <option value="<?= $s ?>" <?= $u['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn--sm">Save</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
admin_layout_end();
