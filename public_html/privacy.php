<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

$user = current_user();
$supportEmail = db_one("SELECT setting_value FROM site_settings WHERE setting_key='support_email'")['setting_value'] ?? 'hello@lulusai.my';
$lastUpdated = '3 June 2026';

render_head('Privacy Policy');
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
      <?php theme_toggle_button(); ?>
    </nav>
  </div>
</header>

<section class="section">
  <div class="container legal">
    <h1>Privacy Policy</h1>
    <p class="muted">Last updated: <?= e($lastUpdated) ?></p>

    <p>
      <?= e(APP_NAME) ?> ("we", "us", "our") operates the lulusai.my platform and related mobile applications
      (the "Service"). This Privacy Policy explains how we collect, use, share, and protect personal
      information when you use the Service. We comply with the Personal Data Protection Act 2010 of
      Malaysia ("PDPA") and its 2024 amendments.
    </p>

    <h2>1. Who this policy applies to</h2>
    <p>
      This policy applies to students, parents, teachers, school administrators and visitors who use
      the Service. The Service is designed for secondary-school learners (typically aged 13–19) preparing
      for Malaysian SPM and equivalent examinations.
    </p>

    <h2>2. Information we collect</h2>
    <ul>
      <li><strong>Account information</strong> — name, email address, phone number, role (student / parent /
        teacher / school admin), school name, form level, and chosen subjects.</li>
      <li><strong>Authentication data</strong> — hashed passwords, login timestamps, IP address, and
        device/browser information used for security and rate-limiting.</li>
      <li><strong>Learning data</strong> — diagnostic and practice attempts, scores, time spent, streak and
        mastery progress, AI tutor conversations, written submissions (essays, summaries, prompt-engineering
        practice), uploaded photos for AI marking ("Snap &amp; Check"), and flashcard progress.</li>
      <li><strong>Parental / school linkage</strong> — when a parent links a student account, or a school
        admin enrols students, the linkage and basic identifiers are stored.</li>
      <li><strong>Payment data</strong> — handled by our payment processor (Billplz). We store only the
        transaction reference, plan, status and last payment date. We do not store full card or bank account
        details on our servers.</li>
      <li><strong>Communication data</strong> — contact-form messages, support requests, and (if opted in)
        WhatsApp / email reminder delivery logs.</li>
      <li><strong>Cookies and analytics</strong> — session cookies for login, and minimal usage analytics to
        improve the Service.</li>
    </ul>

    <h2>3. How we use your information</h2>
    <ul>
      <li>To provide and personalise the Service — including AI tutoring, learning paths, diagnostic
        feedback, writing-marker grading, library and flashcard recommendations.</li>
      <li>To allow parents and teachers to monitor a linked student's progress.</li>
      <li>To process payments, free trials, refunds and subscription renewals.</li>
      <li>To send reminders, weekly AI progress reports and important account or security notifications.</li>
      <li>To protect the Service from abuse, fraud, and security incidents.</li>
      <li>To comply with legal obligations and respond to lawful requests by authorities.</li>
      <li>To improve our Service through aggregate, de-identified analytics.</li>
    </ul>

    <h2>4. AI processing &amp; third-party providers</h2>
    <p>
      The Service uses large language model providers (currently Anthropic and OpenAI) to power AI tutoring,
      writing marking, and the AI Sandbox. When you submit a question, an essay, a photo of handwritten
      work, or a prompt for comparison, the relevant content is sent to the provider's API for processing.
      We require these providers to handle the content according to their published privacy and data-use
      policies and we do not authorise them to train their models on student submissions.
    </p>
    <p>
      We also use the following categories of third-party processors:
    </p>
    <ul>
      <li><strong>Hosting</strong> — Hostinger (servers and database).</li>
      <li><strong>Payments</strong> — Billplz (and, in future, Stripe).</li>
      <li><strong>Messaging</strong> — Meta WhatsApp Cloud API (for opt-in reminders) and SMTP email
        delivery.</li>
    </ul>

    <h2>5. Children and parental consent</h2>
    <p>
      The Service is intended for learners aged 13 and above. If you are under 18, you should obtain
      consent from a parent or legal guardian before creating an account and using paid features. Parents
      may at any time review, correct, or request deletion of their child's data by contacting us at
      <a href="mailto:<?= e($supportEmail) ?>"><?= e($supportEmail) ?></a>.
    </p>

    <h2>6. Sharing and disclosure</h2>
    <p>We share personal information only in the following situations:</p>
    <ul>
      <li><strong>With your consent</strong> — for example, when you choose to link a parent or teacher
        account.</li>
      <li><strong>With your school</strong> — if your account is provisioned by a school, your registered
        teachers and school administrators may view your learning data within that school.</li>
      <li><strong>With service providers</strong> listed above, under contractual data-protection
        obligations.</li>
      <li><strong>Legal compliance</strong> — to comply with a court order, regulatory request or applicable
        law.</li>
      <li><strong>Corporate transactions</strong> — in the event of a merger, acquisition or asset sale,
        subject to continued protection of your data.</li>
    </ul>
    <p>We do not sell personal information to advertisers or data brokers.</p>

    <h2>7. Data retention</h2>
    <p>
      We retain account and learning data while your account is active and for a reasonable period
      afterwards to support audit, dispute resolution, and reactivation. You may request deletion of
      your account and associated data at any time. Aggregate, de-identified data may be retained
      indefinitely for analytics.
    </p>

    <h2>8. Security</h2>
    <p>
      We use industry-standard safeguards including HTTPS encryption in transit, hashed passwords, CSRF
      protections, prepared SQL statements, role-based access controls, and rate limits. No system is
      perfectly secure; if you suspect a security issue, please notify us immediately.
    </p>

    <h2>9. Your rights under the PDPA</h2>
    <p>You have the right to:</p>
    <ul>
      <li>Access the personal data we hold about you.</li>
      <li>Request correction of inaccurate or incomplete data.</li>
      <li>Withdraw consent for processing (which may affect your ability to use the Service).</li>
      <li>Request deletion of your account and associated personal data.</li>
      <li>Lodge a complaint with the Personal Data Protection Department, Malaysia.</li>
    </ul>
    <p>
      To exercise these rights, email <a href="mailto:<?= e($supportEmail) ?>"><?= e($supportEmail) ?></a>.
      We will respond within 21 days of receiving a valid request.
    </p>

    <h2>10. International data transfers</h2>
    <p>
      Some processors (AI providers, payment gateways, messaging providers) operate outside Malaysia. By
      using the Service you consent to your personal data being transferred to those jurisdictions,
      subject to safeguards equivalent to those required by Malaysian law.
    </p>

    <h2>11. Cookies</h2>
    <p>
      We use session cookies to keep you logged in and CSRF tokens to protect your session. We do not
      use advertising cookies. You can disable cookies in your browser, but parts of the Service may
      stop working as a result.
    </p>

    <h2>12. Changes to this policy</h2>
    <p>
      We may update this Privacy Policy from time to time. Material changes will be notified by email
      or by a prominent notice on the Service. Continued use of the Service after an update constitutes
      acceptance of the revised policy.
    </p>

    <h2>13. Contact us</h2>
    <p>
      Questions about this policy, or to exercise your data-protection rights, contact:
      <br>
      <strong><?= e(APP_NAME) ?></strong> &middot;
      <a href="mailto:<?= e($supportEmail) ?>"><?= e($supportEmail) ?></a>
    </p>

    <p class="muted" style="margin-top:32px;font-size:13px">
      This page describes our standard practices. For school-provisioned accounts, your school's data
      processing agreement with us governs any conflict with this policy.
    </p>
  </div>
</section>

<?php require __DIR__ . '/inc/legal_footer.php'; ?>
</body>
</html>
