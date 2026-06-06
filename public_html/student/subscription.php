<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/billing.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $plan = get_plan(input('plan'));
    if (!$plan) {
        flash('error', 'Plan not found.');
        redirect('student/subscription.php');
    }
    try {
        $result = start_checkout($user, $plan);
        if (!empty($result['redirect'])) {
            header('Location: ' . $result['redirect']);
            exit;
        }
        flash('success', $result['message'] ?? 'Subscription updated.');
    } catch (Throwable $e) {
        flash('error', 'Checkout failed: ' . (APP_DEBUG ? $e->getMessage() : 'please try again.'));
    }
    redirect('student/subscription.php');
}

if (input_int('paid') === 1) {
    flash('info', 'Thanks! If your payment succeeded, your plan will activate once confirmed.');
}

$plans = db_all('SELECT * FROM subscription_plans WHERE status = "active" ORDER BY sort_order');
$sub   = current_subscription($uid);
$invoices = db_all('SELECT invoice_no, amount, status, created_at FROM invoices WHERE user_id = ? ORDER BY id DESC LIMIT 10', [$uid]);

student_layout_start('Subscription', $user, 'subscription.php');
?>
<div class="card">
  <h2>Your subscription</h2>
  <?php if ($sub): ?>
    <p>Plan: <strong><?= e($sub['plan_name']) ?></strong>
      <span class="badge <?= in_array($sub['status'], ['active','trialing'], true) ? 'badge--good' : 'badge--warn' ?>"><?= e($sub['status']) ?></span>
    </p>
    <?php if ($sub['ends_at']): ?><p class="muted"><?= $sub['status'] === 'trialing' ? 'Trial ends' : 'Renews/ends' ?>: <?= e(date('d M Y', strtotime((string) $sub['ends_at']))) ?></p><?php endif; ?>
  <?php else: ?>
    <p class="muted">No active subscription.</p>
  <?php endif; ?>
</div>

<div class="grid grid--3" style="margin-top:18px">
  <?php foreach ($plans as $p): $isCurrent = $sub && (int) $sub['plan_id'] === (int) $p['id']; ?>
    <div class="card price-card<?= $p['code'] === 'monthly' ? ' price-card--featured' : '' ?>">
      <h3><?= e($p['name']) ?></h3>
      <div class="price">
        <?= $p['price'] > 0 ? 'RM' . e(number_format((float) $p['price'], 0)) : 'Free' ?>
        <small><?= $p['billing_cycle'] === 'annual' ? '/year' : ($p['billing_cycle'] === 'monthly' ? '/month' : '/14 days') ?></small>
      </div>
      <ul><?php foreach (pipe_list($p['features']) as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul>
      <?php if ($isCurrent): ?>
        <button class="btn btn--block btn--ghost" disabled>Current plan</button>
      <?php else: ?>
        <form method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="plan" value="<?= e($p['code']) ?>">
          <button class="btn btn--block" type="submit"><?= $p['price'] > 0 ? 'Subscribe' : 'Activate' ?></button>
        </form>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<?php if ($invoices): ?>
  <div class="card" style="margin-top:18px">
    <h3>Invoices</h3>
    <table class="table"><thead><tr><th>Invoice</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody>
    <?php foreach ($invoices as $inv): ?>
      <tr>
        <td><?= e($inv['invoice_no']) ?></td>
        <td>RM<?= e(number_format((float) $inv['amount'], 2)) ?></td>
        <td><span class="badge"><?= e($inv['status']) ?></span></td>
        <td class="muted"><?= e(date('d M Y', strtotime((string) $inv['created_at']))) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  </div>
<?php endif; ?>

<p class="muted" style="margin-top:14px">Payments are processed by Billplz. <?= BILLPLZ_API_KEY === '' ? 'Currently in demo mode (no gateway key configured) — plans activate instantly for testing.' : '' ?></p>
<p class="muted" style="margin-top:6px;font-size:13px">
  Subscriptions auto-renew until cancelled. We offer a <strong>7-day money-back guarantee</strong> on your first paid subscription —
  see the <a href="<?= url('refund.php') ?>" target="_blank">Refund Policy</a> for details.
  By subscribing you agree to our <a href="<?= url('terms.php') ?>" target="_blank">Terms</a>
  and <a href="<?= url('privacy.php') ?>" target="_blank">Privacy Policy</a>.
</p>
<?php
student_layout_end();
