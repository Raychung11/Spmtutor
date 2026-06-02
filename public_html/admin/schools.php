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
                notify((int) $school['owner_user_id'], 'Your school has been approved', 'You can now invite teachers and enrol students.', 'school');
            }
            flash('success', $school['name'] . ' approved.');
        }
        redirect('admin/schools.php');
    }

    if ($action === 'approve_bulk') {
        $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
        if ($ids) {
            $place = implode(',', array_fill(0, count($ids), '?'));
            $toNotify = db_all("SELECT id, name, owner_user_id FROM schools WHERE id IN ($place) AND status = 'pending'", $ids);
            db_exec("UPDATE schools SET status = 'active' WHERE id IN ($place)", $ids);
            foreach ($toNotify as $sch) {
                if ($sch['owner_user_id']) {
                    notify((int) $sch['owner_user_id'], 'Your school has been approved', 'You can now invite teachers and enrol students.', 'school');
                }
            }
            flash('success', count($toNotify) . ' school(s) approved.');
        }
        redirect('admin/schools.php?status=pending');
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
$q       = trim(input('q'));
$status  = in_array(input('status'), ['active', 'pending', 'inactive'], true) ? input('status') : '';
$type    = in_array(input('type_filter'), ['school', 'learning_center'], true) ? input('type_filter') : '';
$page    = max(1, input_int('page', 1));
$perPage = max(20, min(200, input_int('per_page', 50)));
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
if ($type !== '') {
    $where[]  = 's.type = ?';
    $params[] = $type;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int) (db_one("SELECT COUNT(*) c FROM schools s $whereSql", $params)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$rows = db_all(
    "SELECT s.*, o.name AS owner_name, o.email AS owner_email,
            (SELECT COUNT(*) FROM school_members m WHERE m.school_id = s.id) AS member_count
     FROM schools s LEFT JOIN users o ON o.id = s.owner_user_id
     $whereSql
     ORDER BY (s.status = 'pending') DESC, s.id DESC
     LIMIT $perPage OFFSET $offset",
    $params
);

// Status counts (for pills).
$statusCounts = [];
foreach (db_all("SELECT status, COUNT(*) c FROM schools GROUP BY status") as $r) {
    $statusCounts[(string) $r['status']] = (int) $r['c'];
}
$statusAll = array_sum($statusCounts);

// Type chip counts (respect current status + search filters).
$tcWhere = [];
$tcParams = [];
if ($q !== '') {
    $tcWhere[] = '(s.name LIKE ? OR s.contact_email LIKE ?)';
    $tcParams[] = '%' . $q . '%';
    $tcParams[] = '%' . $q . '%';
}
if ($status !== '') {
    $tcWhere[] = 's.status = ?';
    $tcParams[] = $status;
}
$tcWhereSql = $tcWhere ? ' AND ' . implode(' AND ', $tcWhere) : '';
$typeCounts = ['school' => 0, 'learning_center' => 0];
foreach (db_all("SELECT type, COUNT(*) c FROM schools s WHERE 1=1 $tcWhereSql GROUP BY type", $tcParams) as $r) {
    $typeCounts[(string) $r['type']] = (int) $r['c'];
}

/** Build a filter URL preserving current params. */
function schools_link(array $overrides = []): string
{
    global $q, $status, $type, $page, $perPage;
    $args = array_merge([
        'q' => $q, 'status' => $status, 'type_filter' => $type,
        'page' => $page, 'per_page' => $perPage,
    ], $overrides);
    $defaults = ['q' => '', 'status' => '', 'type_filter' => '', 'page' => 1, 'per_page' => 50];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('admin/schools.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Schools & Centres', $admin, 'schools.php');
?>
<?php if ($edit): ?>
<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0">Edit school / centre</h3>
  <?php render_school_form($edit); ?>
</div>
<?php else: ?>
<details class="card" style="margin-bottom:18px">
  <summary style="cursor:pointer;font-weight:600;font-size:15px">+ Add school / learning centre</summary>
  <div style="margin-top:14px">
    <?php render_school_form(null); ?>
  </div>
</details>
<?php endif; ?>

<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Jump by type</h4>
  <div style="display:flex;flex-wrap:wrap;gap:8px">
    <a class="chip" href="<?= e(schools_link(['type_filter' => '', 'page' => 1])) ?>"
       style="<?= $type === '' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
      All <span class="muted" style="margin-left:4px">· <?= (int) array_sum($typeCounts) ?></span>
    </a>
    <a class="chip" href="<?= e(schools_link(['type_filter' => 'school', 'page' => 1])) ?>"
       style="<?= $type === 'school' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
      Schools <span class="muted" style="margin-left:4px">· <?= (int) $typeCounts['school'] ?></span>
    </a>
    <a class="chip" href="<?= e(schools_link(['type_filter' => 'learning_center', 'page' => 1])) ?>"
       style="<?= $type === 'learning_center' ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
      Learning centres <span class="muted" style="margin-left:4px">· <?= (int) $typeCounts['learning_center'] ?></span>
    </a>
  </div>
</div>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">All schools / centres <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $status === '' ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['status' => '', 'page' => 1])) ?>">
        All <span class="muted" style="font-size:11px;margin-left:4px">· <?= $statusAll ?></span>
      </a>
      <a class="btn btn--sm <?= $status === 'pending' ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['status' => 'pending', 'page' => 1])) ?>">
        Pending
        <?php $p = (int) ($statusCounts['pending'] ?? 0); ?>
        <?php if ($p > 0): ?>
          <span class="badge badge--warn" style="margin-left:4px"><?= $p ?></span>
        <?php else: ?>
          <span class="muted" style="font-size:11px;margin-left:4px">· 0</span>
        <?php endif; ?>
      </a>
      <a class="btn btn--sm <?= $status === 'active' ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['status' => 'active', 'page' => 1])) ?>">
        Active <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int) ($statusCounts['active'] ?? 0) ?></span>
      </a>
      <a class="btn btn--sm <?= $status === 'inactive' ? '' : 'btn--ghost' ?>" href="<?= e(schools_link(['status' => 'inactive', 'page' => 1])) ?>">
        Inactive <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int) ($statusCounts['inactive'] ?? 0) ?></span>
      </a>
    </div>
  </div>

  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input class="input" name="q" placeholder="Search name or contact email" value="<?= e($q) ?>" style="flex:1;min-width:220px">
    <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
    <?php if ($type !== ''): ?><input type="hidden" name="type_filter" value="<?= e($type) ?>"><?php endif; ?>
    <select name="per_page" class="input" style="max-width:130px" onchange="this.form.submit()">
      <?php foreach ([20, 50, 100, 200] as $pp): ?>
        <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?> / page</option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $status !== '' || $type !== ''): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['q' => '', 'status' => '', 'type_filter' => '', 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php if (!$rows): ?>
    <p class="muted">No schools / centres match these filters.</p>
  <?php else: ?>
    <?php if ($status === 'pending'): ?>
      <form method="post" id="bulkApproveForm" style="margin-bottom:10px">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="approve_bulk">
        <button class="btn btn--sm" onclick="return confirm('Approve all checked schools?')">Approve checked</button>
        <a href="#" class="btn btn--sm btn--ghost" onclick="document.querySelectorAll('.sch-check').forEach(c=>c.checked=true);return false;">Check all</a>
        <a href="#" class="btn btn--sm btn--ghost" onclick="document.querySelectorAll('.sch-check').forEach(c=>c.checked=false);return false;">Clear</a>
      </form>
    <?php endif; ?>

    <div class="sch-list">
      <?php foreach ($rows as $s):
        $statusClass = $s['status'] === 'active' ? 'badge--good' : ($s['status'] === 'pending' ? 'badge--warn' : '');
        $typeLabel = $s['type'] === 'school' ? 'School' : 'Centre';
      ?>
        <div class="sch-row sch-row--<?= e($s['status']) ?>">
          <?php if ($status === 'pending'): ?>
            <input type="checkbox" form="bulkApproveForm" name="ids[]" value="<?= (int)$s['id'] ?>" class="sch-check">
          <?php endif; ?>
          <div class="sch-row__main">
            <div class="sch-row__name">
              <?= e($s['name']) ?>
              <span class="badge" style="margin-left:6px;font-size:10px"><?= e($typeLabel) ?></span>
              <span class="badge <?= $statusClass ?>" style="margin-left:4px;font-size:10px"><?= e($s['status']) ?></span>
            </div>
            <div class="sch-row__meta muted">
              <?php if ($s['contact_email']): ?>📧 <?= e($s['contact_email']) ?><?php endif; ?>
              <?php if ($s['phone']): ?> · ☎ <?= e($s['phone']) ?><?php endif; ?>
              <?php if ($s['owner_name']): ?> · 👤 <?= e($s['owner_name']) ?><?php endif; ?>
            </div>
          </div>
          <div class="sch-row__stat">
            <span class="muted">Members</span>
            <strong><?= (int)$s['member_count'] ?></strong>
          </div>
          <div class="sch-row__actions">
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
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(schools_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
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

<style>
.sch-list { display:flex; flex-direction:column; gap:6px; }
.sch-row {
  display:flex; align-items:center; gap:14px;
  padding:12px 14px;
  border:1px solid var(--border);
  border-radius:10px;
  background:var(--card-2);
}
.sch-row:hover { border-color:var(--primary); }
.sch-row--pending { border-left: 3px solid var(--warn); }
.sch-row__main { flex:1; min-width:220px; }
.sch-row__name { font-weight:600; font-size:14px; display:flex; align-items:center; flex-wrap:wrap; }
.sch-row__meta { font-size:12px; margin-top:4px; line-height:1.5; }
.sch-row__stat {
  display:flex; flex-direction:column; align-items:center;
  width:80px; text-align:center; font-size:12px;
}
.sch-row__stat strong { font-size:16px; font-weight:700; margin:2px 0; color:var(--text); }
.sch-row__actions { display:flex; gap:6px; flex-wrap:wrap; }
.sch-check { margin-top:4px; }
@media (max-width: 880px) {
  .sch-row { flex-wrap:wrap; }
  .sch-row__stat { width:auto; min-width:64px; }
  .sch-row__actions { width:100%; justify-content:flex-end; margin-top:8px; }
}
</style>
<?php
admin_layout_end();

// ----------------------------------------------------------------
// School form renderer (used for both create and edit).
// ----------------------------------------------------------------
function render_school_form(?array $edit): void
{
    ?>
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
    <?php
}
