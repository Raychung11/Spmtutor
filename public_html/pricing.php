<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

$plans = db_all('SELECT * FROM subscription_plans WHERE status = "active" ORDER BY sort_order');
$user  = current_user();

render_head('Pricing');
?>
<div class="container">
  <nav class="nav">
    <a class="brand" href="<?= url('') ?>"><?= e(APP_NAME) ?></a>
    <div class="nav__links">
      <a href="<?= url('') ?>">Home</a>
      <?php if ($user): ?>
        <a class="btn btn--sm" href="<?= url(dashboard_for($user['role'])) ?>">Dashboard</a>
      <?php else: ?>
        <a href="<?= url('login.php') ?>">Log in</a>
        <a class="btn btn--sm" href="<?= url('register.php') ?>">Start free</a>
      <?php endif; ?>
    </div>
  </nav>

  <section class="hero">
    <h1><span class="grad">Pricing</span></h1>
    <p>Start with a 14-day free trial. Cancel anytime.</p>
  </section>

  <section class="section">
    <div class="grid grid--3">
      <?php foreach ($plans as $p): ?>
        <div class="card price-card<?= $p['code'] === 'monthly' ? ' price-card--featured' : '' ?>">
          <h3><?= e($p['name']) ?></h3>
          <div class="price">
            <?= $p['price'] > 0 ? 'RM' . e(number_format((float)$p['price'], 0)) : 'Free' ?>
            <small><?= $p['billing_cycle'] === 'annual' ? '/year' : ($p['billing_cycle'] === 'monthly' ? '/month' : '/14 days') ?></small>
          </div>
          <ul>
            <?php foreach (pipe_list($p['features']) as $f): ?><li><?= e($f) ?></li><?php endforeach; ?>
          </ul>
          <?php if ($user && $user['role'] === 'student'): ?>
            <a class="btn btn--block" href="<?= url('student/subscription.php?plan=' . urlencode($p['code'])) ?>">Select <?= e($p['name']) ?></a>
          <?php else: ?>
            <a class="btn btn--block" href="<?= url('register.php') ?>">Get started</a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="center muted" style="margin-top:24px">Payments via Billplz (Stripe coming soon). Phase 2 feature &mdash; checkout is integration-ready.</p>
  </section>
</div>
<footer class="site">
  <div class="container center muted" style="display:flex;gap:18px;justify-content:center;flex-wrap:wrap;font-size:13px">
    <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>.</span>
    <a href="<?= url('privacy.php') ?>">Privacy</a>
    <a href="<?= url('terms.php') ?>">Terms</a>
    <a href="<?= url('refund.php') ?>">Refund</a>
  </div>
</footer>
</body>
</html>
