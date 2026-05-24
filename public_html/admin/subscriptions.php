<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');

    if ($action === 'save_plan') {
        $id = input_int('id');
        $price = max(0, (float) input('price'));
        $trial = max(0, input_int('trial_days'));
        $status = input('status') === 'inactive' ? 'inactive' : 'active';
        db_exec(
            'UPDATE subscription_plans SET name = ?, price = ?, trial_days = ?, features = ?, status = ? WHERE id = ?',
            [input('name'), $price, $trial, input('features'), $status, $id]
        );
        flash('success', 'Plan updated.');

    } elseif ($action === 'create_plan') {
        $code = strtolower(preg_replace('/[^a-z0-9_]+/', '_', input('code')));
        $cycle = in_array(input('billing_cycle'), ['trial', 'monthly', 'annual'], true) ? input('billing_cycle') : 'monthly';
        if ($code === '' || input('name') === '') {
            flash('error', 'Code and name are required.');
        } elseif (db_one('SELECT id FROM subscription_plans WHERE code = ?', [$code])) {
            flash('error', 'A plan with that code already exists.');
        } else {
            db_exec(
                'INSERT INTO subscription_plans (code, name, price, currency, billing_cycle, trial_days, features, sort_order)
                 VALUES (?,?,?,?,?,?,?,?)',
                [$code, input('name'), max(0, (float) input('price')), 'MYR', $cycle, max(0, input_int('trial_days')), input('features'),
                 (int) (db_one('SELECT COALESCE(MAX(sort_order),0)+1 n FROM subscription_plans')['n'] ?? 1)]
            );
            flash('success', 'Plan created.');
        }

    } elseif ($action === 'cancel_sub') {
        db_exec('UPDATE user_subscriptions SET status = "cancelled" WHERE id = ?', [input_int('id')]);
        flash('success', 'Subscription cancelled.');

    } elseif ($action === 'extend_sub') {
        db_exec(
            'UPDATE user_subscriptions SET status = "active", ends_at = DATE_ADD(GREATEST(COALESCE(ends_at, NOW()), NOW()), INTERVAL 1 MONTH) WHERE id = ?',
            [input_int('id')]
        );
        flash('success', 'Subscription extended by 1 month.');
    }
    redirect('admin/subscriptions.php');
}

$totalRevenue = (float) (db_one("SELECT COALESCE(SUM(amount),0) s FROM payment_transactions WHERE status = 'paid'")['s'] ?? 0);
$activeCount  = (int) (db_one("SELECT COUNT(*) c FROM user_subscriptions WHERE status IN ('active','trialing')")['c'] ?? 0);
$mrr = (float) (db_one(
    "SELECT COALESCE(SUM(CASE WHEN p.billing_cycle='annual' THEN p.price/12 ELSE p.price END),0) s
     FROM user_subscriptions us JOIN subscription_plans p ON p.id = us.plan_id
     WHERE us.status = 'active'"
)['s'] ?? 0);

$plans = db_all('SELECT * FROM subscription_plans ORDER BY sort_order, id');
$subs  = db_all(
    "SELECT us.*, u.name AS user_name, u.email, p.name AS plan_name
     FROM user_subscriptions us JOIN users u ON u.id = us.user_id JOIN subscription_plans p ON p.id = us.plan_id
     ORDER BY us.id DESC LIMIT 100"
);

admin_layout_start('Subscriptions', $admin, 'subscriptions.php');
?>
<div class="grid grid--3">
  <div class="card stat"><div class="stat__value">RM<?= e(number_format($totalRevenue, 0)) ?></div><div class="stat__label">Total revenue (paid)</div></div>
  <div class="card stat"><div class="stat__value">RM<?= e(number_format($mrr, 0)) ?></div><div class="stat__label">Est. monthly recurring</div></div>
  <div class="card stat"><div class="stat__value"><?= $activeCount ?></div><div class="stat__label">Active / trialing</div></div>
</div>

<h2 style="margin-top:24px">Plans</h2>
<div class="grid grid--2">
  <?php foreach ($plans as $p): ?>
    <div class="card">
      <h3><?= e($p['name']) ?> <span class="badge"><?= e($p['code']) ?></span> <span class="badge"><?= e($p['billing_cycle']) ?></span></h3>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_plan">
        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
        <div class="field"><label>Name</label><input class="input" name="name" value="<?= e($p['name']) ?>"></div>
        <div class="grid grid--2">
          <div class="field"><label>Price (<?= e($p['currency']) ?>)</label><input class="input" type="number" step="0.01" min="0" name="price" value="<?= e((string)$p['price']) ?>"></div>
          <div class="field"><label>Trial days</label><input class="input" type="number" min="0" name="trial_days" value="<?= (int)$p['trial_days'] ?>"></div>
        </div>
        <div class="field"><label>Features (one per line — use <code>|</code>)</label><textarea name="features"><?= e($p['features'] ?? '') ?></textarea></div>
        <div class="field"><label>Status</label>
          <select name="status" class="input">
            <option value="active" <?= $p['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $p['status'] !== 'active' ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
        <button class="btn btn--sm">Save plan</button>
      </form>
    </div>
  <?php endforeach; ?>
  <div class="card">
    <h3>Add a plan</h3>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create_plan">
      <div class="grid grid--2">
        <div class="field"><label>Code</label><input class="input" name="code" placeholder="e.g. premium" required></div>
        <div class="field"><label>Billing cycle</label>
          <select name="billing_cycle" class="input"><option value="monthly">monthly</option><option value="annual">annual</option><option value="trial">trial</option></select>
        </div>
      </div>
      <div class="field"><label>Name</label><input class="input" name="name" required></div>
      <div class="grid grid--2">
        <div class="field"><label>Price (MYR)</label><input class="input" type="number" step="0.01" min="0" name="price" value="0"></div>
        <div class="field"><label>Trial days</label><input class="input" type="number" min="0" name="trial_days" value="0"></div>
      </div>
      <div class="field"><label>Features (use <code>|</code>)</label><textarea name="features"></textarea></div>
      <button class="btn btn--sm">Create plan</button>
    </form>
  </div>
</div>

<h2 style="margin-top:24px">User subscriptions</h2>
<div class="card">
  <table class="table">
    <thead><tr><th>User</th><th>Plan</th><th>Status</th><th>Ends</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($subs as $s): ?>
      <tr>
        <td><?= e($s['user_name']) ?><br><span class="muted" style="font-size:12px"><?= e($s['email']) ?></span></td>
        <td><?= e($s['plan_name']) ?></td>
        <td><span class="badge <?= in_array($s['status'], ['active','trialing'], true) ? 'badge--good' : 'badge--warn' ?>"><?= e($s['status']) ?></span></td>
        <td class="muted"><?= $s['ends_at'] ? e(date('d M Y', strtotime((string)$s['ends_at']))) : '—' ?></td>
        <td style="white-space:nowrap">
          <form method="post" style="display:inline">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="extend_sub">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="btn btn--sm btn--ghost">+1 month</button>
          </form>
          <?php if (in_array($s['status'], ['active','trialing'], true)): ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Cancel this subscription?')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="cancel_sub">
              <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
              <button class="btn btn--sm btn--danger">Cancel</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$subs): ?><tr><td colspan="5" class="muted">No subscriptions yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
admin_layout_end();
