<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');

    if ($action === 'update') {
        $id     = input_int('id');
        $status = input('status');
        $role   = input('role');
        if ($id && $id !== (int) $admin['id']
            && in_array($status, ['active', 'suspended', 'pending'], true)
            && in_array($role, ['student', 'parent', 'teacher', 'admin', 'creator', 'school_admin'], true)) {
            db_exec('UPDATE users SET status = ?, role = ? WHERE id = ?', [$status, $role, $id]);
            flash('success', 'User updated.');
        } else {
            flash('error', 'Invalid update (you cannot change your own account here).');
        }
    } elseif ($action === 'approve_bulk') {
        $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
        $ids = array_values(array_filter($ids, fn($i) => $i !== (int) $admin['id']));
        if ($ids) {
            $place = implode(',', array_fill(0, count($ids), '?'));
            db_exec("UPDATE users SET status = 'active' WHERE id IN ($place)", $ids);
            flash('success', count($ids) . ' user(s) approved.');
        }
        redirect('admin/users.php?status=pending');
    } elseif ($action === 'suspend') {
        $id = input_int('id');
        if ($id && $id !== (int) $admin['id']) {
            db_exec("UPDATE users SET status = 'suspended' WHERE id = ?", [$id]);
            flash('success', 'User suspended.');
        }
    }
    redirect('admin/users.php' . (input('return_status') ? '?status=' . input('return_status') : ''));
}

// --- Filters + pagination ---
$q        = trim(input('q'));
$role     = in_array(input('role'), ['student', 'parent', 'teacher', 'admin', 'creator', 'school_admin'], true) ? input('role') : '';
$status   = in_array(input('status'), ['active', 'pending', 'suspended'], true) ? input('status') : '';
$sort     = in_array(input('sort'), ['newest', 'oldest', 'last_login', 'name'], true) ? input('sort') : 'newest';
$page     = max(1, input_int('page', 1));
$perPage  = max(20, min(200, input_int('per_page', 50)));
$offset   = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($q !== '') {
    $where[]  = '(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($role !== '') {
    $where[]  = 'u.role = ?';
    $params[] = $role;
}
if ($status !== '') {
    $where[]  = 'u.status = ?';
    $params[] = $status;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$orderSql = match ($sort) {
    'oldest'     => 'u.id ASC',
    'last_login' => 'u.last_login_at DESC, u.id DESC',
    'name'       => 'u.name ASC, u.id ASC',
    default      => 'u.id DESC',
};

$total = (int) (db_one("SELECT COUNT(*) c FROM users u $whereSql", $params)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$rows = db_all(
    "SELECT u.*, sp.xp, sp.level
     FROM users u
     LEFT JOIN student_profiles sp ON sp.user_id = u.id
     $whereSql
     ORDER BY $orderSql
     LIMIT $perPage OFFSET $offset",
    $params
);

// Role + status counts.
$roleCounts = [];
foreach (db_all("SELECT role, COUNT(*) c FROM users GROUP BY role") as $r) {
    $roleCounts[(string) $r['role']] = (int) $r['c'];
}
$roleCountsAll = array_sum($roleCounts);

$statusCounts = [];
foreach (db_all("SELECT status, COUNT(*) c FROM users GROUP BY status") as $r) {
    $statusCounts[(string) $r['status']] = (int) $r['c'];
}

// Role label map.
$roleLabels = [
    'student'      => 'Student',
    'parent'       => 'Parent',
    'teacher'      => 'Teacher',
    'school_admin' => 'School Admin',
    'creator'      => 'Creator',
    'admin'        => 'Platform Admin',
];

function users_link(array $overrides = []): string
{
    global $q, $role, $status, $sort, $page, $perPage;
    $args = array_merge([
        'q' => $q, 'role' => $role, 'status' => $status,
        'sort' => $sort, 'page' => $page, 'per_page' => $perPage,
    ], $overrides);
    $defaults = ['q' => '', 'role' => '', 'status' => '', 'sort' => 'newest', 'page' => 1, 'per_page' => 50];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('admin/users.php' . ($args ? '?' . http_build_query($args) : ''));
}

/** Human-friendly relative time: "2 hours ago", "3 days ago", "—". */
function rel_time(?string $when): string
{
    if (!$when) return '—';
    $t = strtotime($when);
    if (!$t) return '—';
    $diff = time() - $t;
    if ($diff < 60)        return $diff . 's ago';
    if ($diff < 3600)      return floor($diff / 60) . 'm ago';
    if ($diff < 86400)     return floor($diff / 3600) . 'h ago';
    if ($diff < 86400 * 7) return floor($diff / 86400) . 'd ago';
    if ($diff < 86400 * 30) return floor($diff / 86400 / 7) . 'w ago';
    if ($diff < 86400 * 365) return floor($diff / 86400 / 30) . 'mo ago';
    return floor($diff / 86400 / 365) . 'y ago';
}

admin_layout_start('Users', $admin, 'users.php');
?>
<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Jump by role</h4>
  <div style="display:flex;flex-wrap:wrap;gap:8px">
    <a class="chip" href="<?= e(users_link(['role' => '', 'page' => 1])) ?>"
       style="<?= $role === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
      All <span class="muted" style="margin-left:4px">· <?= $roleCountsAll ?></span>
    </a>
    <?php foreach ($roleLabels as $key => $label):
      $c = (int) ($roleCounts[$key] ?? 0);
      if ($c === 0) continue;
    ?>
      <a class="chip" href="<?= e(users_link(['role' => $key, 'page' => 1])) ?>"
         style="<?= $role === $key ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
        <?= e($label) ?>
        <span class="muted" style="margin-left:4px">· <?= $c ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">All users <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $status === '' ? '' : 'btn--ghost' ?>" href="<?= e(users_link(['status' => '', 'page' => 1])) ?>">
        All <span class="muted" style="font-size:11px;margin-left:4px">· <?= $roleCountsAll ?></span>
      </a>
      <a class="btn btn--sm <?= $status === 'active' ? '' : 'btn--ghost' ?>" href="<?= e(users_link(['status' => 'active', 'page' => 1])) ?>">
        Active <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int) ($statusCounts['active'] ?? 0) ?></span>
      </a>
      <a class="btn btn--sm <?= $status === 'pending' ? '' : 'btn--ghost' ?>" href="<?= e(users_link(['status' => 'pending', 'page' => 1])) ?>">
        Pending
        <?php $pendCount = (int) ($statusCounts['pending'] ?? 0); ?>
        <?php if ($pendCount > 0): ?>
          <span class="badge badge--warn" style="margin-left:4px"><?= $pendCount ?></span>
        <?php else: ?>
          <span class="muted" style="font-size:11px;margin-left:4px">· 0</span>
        <?php endif; ?>
      </a>
      <a class="btn btn--sm <?= $status === 'suspended' ? '' : 'btn--ghost' ?>" href="<?= e(users_link(['status' => 'suspended', 'page' => 1])) ?>">
        Suspended <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int) ($statusCounts['suspended'] ?? 0) ?></span>
      </a>
    </div>
  </div>

  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input class="input" name="q" placeholder="Search name, email or phone" value="<?= e($q) ?>" style="flex:1;min-width:240px">
    <?php if ($role !== ''): ?><input type="hidden" name="role" value="<?= e($role) ?>"><?php endif; ?>
    <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
    <select name="sort" class="input" style="max-width:200px" onchange="this.form.submit()">
      <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Newest first</option>
      <option value="oldest"     <?= $sort === 'oldest'     ? 'selected' : '' ?>>Oldest first</option>
      <option value="last_login" <?= $sort === 'last_login' ? 'selected' : '' ?>>Most recently active</option>
      <option value="name"       <?= $sort === 'name'       ? 'selected' : '' ?>>Name A→Z</option>
    </select>
    <select name="per_page" class="input" style="max-width:130px" onchange="this.form.submit()">
      <?php foreach ([20, 50, 100, 200] as $pp): ?>
        <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?> / page</option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $role !== '' || $status !== '' || $sort !== 'newest'): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(users_link(['q' => '', 'role' => '', 'status' => '', 'sort' => 'newest', 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php if (!$rows): ?>
    <p class="muted">No users match these filters.</p>
  <?php else: ?>
    <?php if ($status === 'pending'): ?>
      <form method="post" id="bulkApproveForm" style="margin-bottom:10px">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="approve_bulk">
        <button class="btn btn--sm" onclick="return confirm('Approve all checked users?')">Approve checked</button>
        <a href="#" class="btn btn--sm btn--ghost" onclick="document.querySelectorAll('.usr-check').forEach(c=>c.checked=true);return false;">Check all</a>
        <a href="#" class="btn btn--sm btn--ghost" onclick="document.querySelectorAll('.usr-check').forEach(c=>c.checked=false);return false;">Clear</a>
      </form>
    <?php endif; ?>

    <div class="usr-list">
      <?php foreach ($rows as $u):
        $statusClass = $u['status'] === 'active' ? 'badge--good' : ($u['status'] === 'pending' ? 'badge--warn' : 'badge--bad');
        $roleLabel = $roleLabels[$u['role']] ?? $u['role'];
        $isSelf = (int) $u['id'] === (int) $admin['id'];
      ?>
        <details class="usr-row usr-row--<?= e($u['status']) ?>">
          <summary>
            <?php if ($status === 'pending' && !$isSelf): ?>
              <input type="checkbox" form="bulkApproveForm" name="ids[]" value="<?= (int)$u['id'] ?>" class="usr-check" onclick="event.stopPropagation()">
            <?php endif; ?>
            <div class="usr-row__main">
              <div class="usr-row__name">
                <?= e($u['name']) ?>
                <?php if ($isSelf): ?><span class="badge" style="margin-left:6px;font-size:10px;color:var(--accent);border-color:var(--accent)">you</span><?php endif; ?>
                <span class="badge" style="margin-left:6px;font-size:10px"><?= e($roleLabel) ?></span>
                <span class="badge <?= $statusClass ?>" style="margin-left:4px;font-size:10px"><?= e($u['status']) ?></span>
              </div>
              <div class="usr-row__meta muted">
                <?= e($u['email']) ?>
                <?php if ($u['phone']): ?> · <?= e($u['phone']) ?><?php endif; ?>
                <?php if ($u['role'] === 'student' && $u['xp'] !== null): ?> · <span style="color:var(--primary)">Lv.<?= (int) $u['level'] ?> · <?= (int) $u['xp'] ?> XP</span><?php endif; ?>
              </div>
            </div>
            <div class="usr-row__times muted">
              <div title="Last login"><?= rel_time($u['last_login_at']) ?></div>
              <div style="font-size:11px" title="Joined">joined <?= rel_time($u['created_at']) ?></div>
            </div>
            <span class="usr-row__expand muted" aria-hidden="true">▾</span>
          </summary>

          <?php if (!$isSelf): ?>
            <div class="usr-row__edit">
              <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin:0">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                <?php if ($status !== ''): ?><input type="hidden" name="return_status" value="<?= e($status) ?>"><?php endif; ?>
                <label class="muted" style="font-size:12px">Role</label>
                <select name="role" class="input" style="max-width:160px">
                  <?php foreach ($roleLabels as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $u['role'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
                <label class="muted" style="font-size:12px">Status</label>
                <select name="status" class="input" style="max-width:140px">
                  <option value="active"    <?= $u['status'] === 'active'    ? 'selected' : '' ?>>Active</option>
                  <option value="pending"   <?= $u['status'] === 'pending'   ? 'selected' : '' ?>>Pending</option>
                  <option value="suspended" <?= $u['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
                <button class="btn btn--sm">Save</button>
              </form>
              <p class="muted" style="font-size:12px;margin:6px 0 0">
                ID <?= (int) $u['id'] ?> · last login: <?= $u['last_login_at'] ? e($u['last_login_at']) : 'never' ?> · joined <?= e($u['created_at']) ?>
              </p>
            </div>
          <?php else: ?>
            <div class="usr-row__edit muted" style="font-size:13px">You can't change your own role / status here.</div>
          <?php endif; ?>
        </details>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(users_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(users_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
          <a class="btn btn--sm <?= $p === $page ? '' : 'btn--ghost' ?>" href="<?= e(users_link(['page' => $p])) ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(users_link(['page' => $page + 1])) ?>">Next ›</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(users_link(['page' => $pages])) ?>">Last »</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
.usr-list { display:flex; flex-direction:column; gap:6px; }
.usr-row {
  border:1px solid var(--border);
  border-radius:10px;
  background:var(--card-2);
}
.usr-row:hover { border-color:var(--primary); }
.usr-row--pending { border-left: 3px solid var(--warn); }
.usr-row--suspended { opacity:.7; }
.usr-row > summary {
  display:flex; align-items:center; gap:14px;
  padding:12px 14px;
  cursor:pointer;
  list-style: none;
}
.usr-row > summary::-webkit-details-marker { display:none; }
.usr-row__main { flex:1; min-width:220px; }
.usr-row__name { font-weight:600; font-size:14px; display:flex; align-items:center; flex-wrap:wrap; }
.usr-row__meta { font-size:12px; margin-top:4px; line-height:1.5; }
.usr-row__times { font-size:12px; text-align:right; min-width:110px; }
.usr-row__expand { font-size:14px; transition: transform .15s; }
.usr-row[open] .usr-row__expand { transform: rotate(180deg); }
.usr-row__edit {
  padding:14px;
  border-top:1px solid var(--border);
  background: var(--bg-2);
  border-bottom-left-radius:10px;
  border-bottom-right-radius:10px;
}
.usr-check { margin: 0 6px 0 0; }
@media (max-width: 720px) {
  .usr-row > summary { flex-wrap:wrap; }
  .usr-row__times { text-align:left; min-width:auto; width:100%; margin-top:4px; }
}
</style>
<?php
admin_layout_end();
