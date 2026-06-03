<?php
/**
 * Shared footer for the public legal pages (privacy, terms, refund).
 * Mirrors the landing-page site footer but with a Legal column.
 */
declare(strict_types=1);
?>
<footer class="site">
  <div class="container lfooter">
    <div>
      <div class="brand"><?= e(APP_NAME) ?></div>
      <p class="muted">Not just an app. Your personal AI tutor.</p>
    </div>
    <div>
      <strong>Product</strong>
      <a href="<?= url('') ?>#features">Features</a>
      <a href="<?= url('') ?>#subjects">Subjects</a>
      <a href="<?= url('pricing.php') ?>">Pricing</a>
    </div>
    <div>
      <strong>Get started</strong>
      <a href="<?= url('register.php') ?>">Student / parent</a>
      <a href="<?= url('register-school.php') ?>">Schools</a>
      <a href="<?= url('login.php') ?>">Log in</a>
    </div>
    <div>
      <strong>Legal</strong>
      <a href="<?= url('privacy.php') ?>">Privacy Policy</a>
      <a href="<?= url('terms.php') ?>">Terms &amp; Conditions</a>
      <a href="<?= url('refund.php') ?>">Refund Policy</a>
    </div>
  </div>
  <div class="container center muted" style="border-top:1px solid var(--border);padding-top:18px;margin-top:18px">
    &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.
  </div>
</footer>

<style>
.legal { max-width: 800px; }
.legal h1 { font-size: 36px; line-height: 1.1; margin: 0 0 8px; }
.legal h2 { font-size: 20px; margin: 28px 0 8px; }
.legal p, .legal li { font-size: 15px; line-height: 1.65; color: var(--text); }
.legal ul, .legal ol { padding-left: 22px; margin: 8px 0 16px; }
.legal li { margin-bottom: 6px; }
.legal a { color: var(--primary); }
</style>
