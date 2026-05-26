<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = input_int('id');
    if (input('action') === 'status' && $id) {
        $status = in_array(input('status'), ['new', 'contacted', 'closed'], true) ? input('status') : 'new';
        db_exec('UPDATE leads SET status = ? WHERE id = ?', [$status, $id]);
        flash('success', 'Lead updated.');
    } elseif (input('action') === 'delete' && $id) {
        db_exec('DELETE FROM leads WHERE id = ?', [$id]);
        flash('success', 'Lead deleted.');
    }
    redirect('admin/leads.php');
}

$filter = in_array(input('status'), ['new', 'contacted', 'closed'], true) ? input('status') : '';
$rows = $filter
    ? db_all('SELECT * FROM leads WHERE status = ? ORDER BY id DESC LIMIT 200', [$filter])
    : db_all('SELECT * FROM leads ORDER BY id DESC LIMIT 200');
$newCount = (int) (db_one("SELECT COUNT(*) c FROM leads WHERE status = 'new'")['c'] ?? 0);

admin_layout_start('Leads / Enquiries', $admin, 'leads.php');
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <h3 style="margin:0">Enquiries <span class="badge badge--warn"><?= $newCount ?> new</span></h3>
    <div>
      <a class="btn btn--sm <?= $filter === '' ? '' : 'btn--ghost' ?>" href="<?= url('admin/leads.php') ?>">All</a>
      <a class="btn btn--sm <?= $filter === 'new' ? '' : 'btn--ghost' ?>" href="<?= url('admin/leads.php?status=new') ?>">New</a>
      <a class="btn btn--sm <?= $filter === 'contacted' ? '' : 'btn--ghost' ?>" href="<?= url('admin/leads.php?status=contacted') ?>">Contacted</a>
      <a class="btn btn--sm <?= $filter === 'closed' ? '' : 'btn--ghost' ?>" href="<?= url('admin/leads.php?status=closed') ?>">Closed</a>
    </div>
  </div>
  <table class="table" style="margin-top:14px">
    <thead><tr><th>Name</th><th>Contact</th><th>Message</th><th>Date</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $l): ?>
      <tr>
        <td><?= e($l['name']) ?></td>
        <td class="muted"><a href="mailto:<?= e($l['email']) ?>"><?= e($l['email']) ?></a><?php if ($l['phone']): ?><br><?= e($l['phone']) ?><?php endif; ?></td>
        <td class="muted" style="max-width:280px"><?= nl2br(e(mb_substr((string)$l['message'], 0, 240))) ?></td>
        <td class="muted"><?= e(date('d M, H:i', strtotime((string)$l['created_at']))) ?></td>
        <td>
          <form method="post" style="display:flex;gap:6px">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="status">
            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
            <select name="status" class="input" style="width:auto;padding:6px" onchange="this.form.submit()">
              <?php foreach (['new', 'contacted', 'closed'] as $st): ?>
                <option value="<?= $st ?>" <?= $l['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>
        <td>
          <form method="post" onsubmit="return confirm('Delete this lead?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
            <button class="btn btn--sm btn--danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="6" class="muted">No enquiries yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
admin_layout_end();
