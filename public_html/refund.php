<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

$user = current_user();
$supportEmail = db_one("SELECT setting_value FROM site_settings WHERE setting_key='support_email'")['setting_value'] ?? 'hello@lulusai.my';
$lastUpdated = '3 June 2026';

render_head('Refund Policy');
?>
<header class="lnav">
  <div class="container lnav__inner">
    <a class="brand" href="<?= url('') ?>"><?= e(APP_NAME) ?></a>
    <nav class="lnav__links">
      <a href="<?= url('') ?>">Home</a>
      <a href="<?= url('pricing.php') ?>">Pricing</a>
      <?php if ($user): ?>
        <a class="btn btn--sm" href="<?= url(dashboard_for($user['role'])) ?>">Dashboard</a>
      <?php else: ?>
        <a href="<?= url('login.php') ?>">Log in</a>
        <a class="btn btn--sm" href="<?= url('register.php') ?>">Start free</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<section class="section">
  <div class="container legal">
    <h1>Refund Policy</h1>
    <p class="muted">Last updated: <?= e($lastUpdated) ?></p>

    <p>
      We want every student and parent to feel confident trying <?= e(APP_NAME) ?>. This Refund Policy
      explains when and how you can request a refund for a paid subscription. It forms part of our
      <a href="<?= url('terms.php') ?>">Terms and Conditions</a>.
    </p>

    <h2>1. Free trial</h2>
    <p>
      Every new student account includes a 14-day free trial of a single subject. No payment is taken
      during the trial, so no refund is required. The trial ends automatically unless you choose to
      subscribe.
    </p>

    <h2>2. 7-day money-back guarantee (monthly &amp; annual plans)</h2>
    <p>
      We offer a 7-day money-back guarantee on your first paid subscription. If you are not satisfied
      with the Service within 7 days of your first successful payment, email us at
      <a href="mailto:<?= e($supportEmail) ?>"><?= e($supportEmail) ?></a> and we will refund that
      payment in full.
    </p>
    <p>The 7-day guarantee applies only to:</p>
    <ul>
      <li>Your first paid subscription (one per customer / household).</li>
      <li>Payments made directly through our website via Billplz.</li>
      <li>Requests received within 7 calendar days of the payment date.</li>
    </ul>

    <h2>3. Renewals (after the guarantee window)</h2>
    <p>
      Subscriptions renew automatically unless cancelled before the renewal date. Renewals after the
      initial 7-day window are generally non-refundable. As a goodwill gesture, we may pro-rate a
      refund for an annual plan within 14 days of an unintended renewal if no significant usage has
      occurred — decisions on goodwill refunds are at our discretion.
    </p>

    <h2>4. How to cancel auto-renewal</h2>
    <p>
      You can cancel auto-renewal at any time from <em>Account &rarr; Subscription</em>. Cancelling
      stops the next billing cycle; you keep access to paid features until the end of the current
      cycle. Cancellation alone does not refund a past payment.
    </p>

    <h2>5. How to request a refund</h2>
    <ol>
      <li>Email <a href="mailto:<?= e($supportEmail) ?>"><?= e($supportEmail) ?></a> from the email
        address registered on your account.</li>
      <li>Include your full name, the transaction reference shown on your subscription page, and a
        brief reason for the refund request.</li>
      <li>We will acknowledge your request within 2 working days and complete eligible refunds within
        7–14 working days, depending on your bank or card issuer.</li>
    </ol>

    <h2>6. Non-refundable situations</h2>
    <p>Refunds will not normally be granted for:</p>
    <ul>
      <li>Requests made more than 7 days after the original payment (subject to the goodwill clause in
        section 3).</li>
      <li>Accounts terminated for breach of our <a href="<?= url('terms.php') ?>">Terms and
        Conditions</a> — for example, sharing accounts, cheating, abuse of AI features, or scraping
        question banks.</li>
      <li>Partial use of a billing cycle after the guarantee window — pro-ration is not provided for
        monthly plans.</li>
      <li>Add-on charges, school plan seats, or institutional invoices once services have been
        provisioned, unless otherwise agreed in writing.</li>
      <li>Payments made through third parties (school billing, in-app stores or future channels) which
        are governed by that party's refund policy.</li>
    </ul>

    <h2>7. School and institutional plans</h2>
    <p>
      Refunds for school plans, learning centres, and bulk seat purchases are handled under the written
      agreement between us and the school. Where no specific clause applies, the school may contact us
      to discuss pro-rated reductions for unused seats.</p>

    <h2>8. Currency and processing</h2>
    <p>
      Refunds are issued in Malaysian Ringgit (RM) to the original payment method. We are not
      responsible for currency conversion fees, foreign-bank charges, or processing delays beyond our
      control.
    </p>

    <h2>9. Changes to this policy</h2>
    <p>
      We may revise this Refund Policy from time to time. The version in effect at the time of your
      payment applies to that payment.
    </p>

    <h2>10. Contact</h2>
    <p>
      Refund questions and requests:
      <a href="mailto:<?= e($supportEmail) ?>"><?= e($supportEmail) ?></a>.
    </p>
  </div>
</section>

<?php require __DIR__ . '/inc/legal_footer.php'; ?>
</body>
</html>
