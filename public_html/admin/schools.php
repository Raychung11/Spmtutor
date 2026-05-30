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
        db_exec('UPDATE teacher_classes SET school_id = NULL WHERE school_id = ?', [input_int('id')]);
        db_exec('DELETE FROM schools WHERE id = ?', [input_int('id')]);
        flash('success', 'Deleted.');
    }
    redirect('admin/schools.php');
}

$editId = input_int('edit');
$edit   = $editId ? db_one('SELECT * FROM schools WHERE id = ?', [$editId]) : null;

// --- Filters + pagination ---
$q      = trim(input('q'));
$status = in_array(input('status'), ['active', 'pending', 'inactive'], true) ? input('status') : '';
$page   = max(1, input_int('page', 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($q !== '') {
    $where[]  = '(s.name LIKE ? OR s.contact_email LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($status !== '') {
    $where[]  = 's.status = ?';
    $params[] = $status;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int) (db_one("SELECT COUNT(*) c FROM schools s $whereSql", $params)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$rows = db_all(
    "SELECT s.*, o.name AS owner_name,
            (SELECT COUNT(*) FROM school_members m WHERE m.school_id = s.id) AS member_count
     FROM schools s LEFT JOIN users o ON o.id = s.owner_user_id
     $whereSql
     ORDER BY (s.status = 'pending') DESC, s.id DESC
     LIMIT $perPage OFFSET $offset",
    $params
);

$pendingCount = (int) (db_one("SELECT COUNT(*) c FROM schools WHERE status = 'pending'")['c'] ?? 0);

/** Build a filter URL preserving current params. */
function schools_link(array $overrides = []): string
{
    global $q, $status, $page;
    $args = array_merge(['q' => $q, 'status' => $status, 'page' => $page], $overrides);
    $args = array_filter($args, fn($v) => $v !== '' && $v !== null && $v !== 0);
    return url('admin/schools.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Schools & Centres', $admin, 'schools.php');
?>
<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0"><?= $edit ? 'Edit school / centre' : 'Add school / learning centre' ?></h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
    <div class="grid grid--3">
      <div class="field"><label>Name *</label><input class="input" name="name" value="<?= e($edit['name'] ?? '') ?>" required></div>
      <div class="field"><label>Type</label>
        <select name="type" class="input">
          <option value="learning_center" <?= ($edit['type'] ?? '') === 'learning_center' ? 'selected' : '' ?>>Learning centre</option>
          <option value="school" <?= ($edit['type'] ?? '') === 'school' ? 'selected' : '' ?>>School</option>
        </select>
      </div>
      <div class="field"><label>Status</label>
        <select name="status" class="input">
          <option value="active" <?= ($edit['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="pending" <?= ($edit['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="inactive" <?= ($edit['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
    </div>
    <div class="grid grid--2">
      <div class="field"><label>Contact email</label><input class="input" type="email" name="contact_email" value="<?= e($edit['contact_email'] ?? '') ?>"></div>
      <div class="field"><label>Phone</label><input class="input" name="phone" value="<?= e($edit['phone'] ?? '') ?>"></div>
    </div>
    <button class="btn"><?= $edit ? 'Save changes' : 'Add' ?></button>
    <?php if ($edit): ?><a class="btn btn--ghost" href="<?= e(schools_link(['edit' => null])) ?>">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">All schools / centres <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $status === '' ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['status' => '', 'page' => 1])) ?>">All</a>
      <a class="btn btn--sm <?= $status === 'pending' ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['status' => 'pending', 'page' => 1])) ?>">Pending <?= $pendingCount ? '<span class="badge badge--warn">' . $pendingCount . '</span>' : '' ?></a>
      <a class="btn btn--sm <?= $status === 'active' ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['status' => 'active', 'page' => 1])) ?>">Active</a>
      <a class="btn btn--sm <?= $status === 'inactive' ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['status' => 'inactive', 'page' => 1])) ?>">Inactive</a>
    </div>
  </div>
  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
    <input class="input" name="q" placeholder="Search name or contact email" value="<?= e($q) ?>" style="flex:1;min-width:220px">
    <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== ''): ?><a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['q' => '', 'page' => 1])) ?>">Clear</a><?php endif; ?>
  </form>

  <table class="table">
    <thead><tr><th>Name</th><th>Owner</th><th>Members</th><th>Status</th><th></th></tr></thead>
    <tbody>
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
          <a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['edit' => (int)$s['id']])) ?>">Edit</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this school? Its classes will be detached.')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="btn btn--sm btn--danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="5" class="muted">No schools match these filters.</td></tr><?php endif; ?>
    </tbody>
  </table>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php
          // Show up to 5 page numbers around the current page.
          $from = max(1, $page - 2);
          $to   = min($pages, $page + 2);
          for ($p = $from; $p <= $to; $p++):
        ?>
          <a class="btn btn--sm <?= $p === $page ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['page' => $p])) ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['page' => $page + 1])) ?>">Next ›</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['page' => $pages])) ?>">Last »</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php
admin_layout_end();
