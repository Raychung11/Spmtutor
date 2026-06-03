<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

$user = current_user();
$supportEmail = db_one("SELECT setting_value FROM site_settings WHERE setting_key='support_email'")['setting_value'] ?? 'hello@lulusai.my';
$lastUpdated = '3 June 2026';

render_head('Terms and Conditions');
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
    <h1>Terms and Conditions</h1>
    <p class="muted">Last updated: <?= e($lastUpdated) ?></p>

    <p>
      These Terms and Conditions ("Terms") govern your access to and use of <?= e(APP_NAME) ?>
      (the "Service"), operated from Malaysia. By creating an account or using the Service you agree
      to these Terms. If you do not agree, please do not use the Service.
    </p>

    <h2>1. Eligibility</h2>
    <p>
      The Service is intended for learners aged 13 years or older. If you are below 18 years of age,
      you may only use the Service with the consent and supervision of a parent or legal guardian, who
      agrees to be bound by these Terms on your behalf.
    </p>

    <h2>2. Your account</h2>
    <ul>
      <li>You must provide accurate, current and complete information when registering.</li>
      <li>You are responsible for keeping your password secure and for all activity on your account.</li>
      <li>You must notify us promptly of any unauthorised use of your account.</li>
      <li>You may not share, transfer, or resell access to your account.</li>
      <li>We may suspend or terminate accounts that violate these Terms, are inactive for extended periods,
        or are used in a manner harmful to the Service or other users.</li>
    </ul>

    <h2>3. Subscriptions, free trial and payment</h2>
    <ul>
      <li>The Service offers a 14-day free trial for new students. No payment information is required to
        start a trial unless explicitly stated.</li>
      <li>Paid plans renew automatically at the end of each billing cycle (monthly or annual) unless
        cancelled before the renewal date.</li>
      <li>Prices are listed in Malaysian Ringgit (RM) and may be revised on at least 14 days' notice. The
        revised price applies to the next renewal cycle.</li>
      <li>Payments are processed by our payment partner (Billplz). By submitting payment, you authorise
        the relevant charge and agree to the payment partner's terms.</li>
      <li>You may cancel auto-renewal at any time from your account settings. Cancellation stops future
        billing; refunds for past payments are governed by our <a href="<?= url('refund.php') ?>">Refund
        Policy</a>.</li>
    </ul>

    <h2>4. Acceptable use</h2>
    <p>When using the Service, you agree NOT to:</p>
    <ul>
      <li>Use the AI tutor, writing marker, or any AI feature to submit work as your own in examinations
        or assessments where AI use is not permitted by your school or examination authority.</li>
      <li>Upload content that is unlawful, defamatory, obscene, harassing, discriminatory, or violates
        another person's rights.</li>
      <li>Attempt to reverse-engineer, scrape, copy, mirror, or otherwise extract bulk data from the
        Service or its question bank.</li>
      <li>Use the Service to send spam, malware, or to attack other systems.</li>
      <li>Interfere with rate limits, security controls, billing systems, or the integrity of any
        feature.</li>
      <li>Impersonate another person or misrepresent your affiliation with any school or organisation.</li>
    </ul>

    <h2>5. AI features — important disclosures</h2>
    <p>
      The Service uses AI models (including Claude and GPT) to provide tutoring, marking, and other
      learning aids. AI outputs are generated probabilistically and may sometimes be inaccurate,
      incomplete, biased, or out of date. You agree that:
    </p>
    <ul>
      <li>AI responses are educational aids, not a substitute for verified study materials, teacher
        guidance or official examination answers.</li>
      <li>AI marking is approximate and should not be treated as a final examination grade.</li>
      <li>You will use independent judgement and check important facts and answers.</li>
      <li>We do not guarantee the accuracy, reliability or fitness-for-purpose of any AI output.</li>
    </ul>

    <h2>6. Student work and intellectual property</h2>
    <p>
      You retain ownership of the original written work, photos and submissions you upload. By
      submitting content, you grant <?= e(APP_NAME) ?> a non-exclusive, royalty-free, worldwide
      licence to host, process, display and analyse that content for the purpose of providing the
      Service to you (and, where applicable, to your linked parent, teacher, or school).
    </p>
    <p>
      All Service materials including the platform code, question bank, reference libraries, designs,
      logos, prompts and AI workflows are owned by <?= e(APP_NAME) ?> or its licensors and are
      protected by Malaysian and international intellectual property law. You may not reproduce,
      distribute, or create derivative works without our prior written consent.
    </p>

    <h2>7. School and institutional accounts</h2>
    <p>
      Where a school provisions an account for a student or teacher, the school is the data controller
      of that user's records for educational purposes. Use of the Service through a school account is
      additionally subject to any agreement between us and the school.
    </p>

    <h2>8. Third-party services</h2>
    <p>
      The Service integrates with third-party providers (AI APIs, payment, messaging, hosting). We are
      not responsible for the availability or content of third-party services, and your use of them is
      governed by their respective terms.
    </p>

    <h2>9. Suspension and termination</h2>
    <p>
      We may suspend or terminate your access to the Service immediately and without prior notice if
      you breach these Terms, abuse the Service, or if we are required to do so by law. You may close
      your account at any time. Some provisions of these Terms (including those on intellectual
      property, disclaimers, liability, and dispute resolution) survive termination.
    </p>

    <h2>10. Disclaimers</h2>
    <p>
      The Service is provided on an "as-is" and "as-available" basis. To the maximum extent permitted
      by law, we disclaim all warranties, express or implied, including warranties of merchantability,
      fitness for a particular purpose, and non-infringement.
    </p>

    <h2>11. Limitation of liability</h2>
    <p>
      To the maximum extent permitted by Malaysian law, our total aggregate liability arising out of
      or in connection with the Service is limited to the fees you paid to us in the 12 months
      preceding the event giving rise to the claim. We will not be liable for any indirect, incidental,
      special, consequential or punitive damages, including loss of profits, examination outcomes,
      data, goodwill or other intangible losses.
    </p>

    <h2>12. Indemnity</h2>
    <p>
      You agree to indemnify and hold harmless <?= e(APP_NAME) ?>, its officers and employees, against
      any claims arising from your breach of these Terms or your unlawful use of the Service.
    </p>

    <h2>13. Governing law and jurisdiction</h2>
    <p>
      These Terms are governed by the laws of Malaysia. Any dispute arising from or related to these
      Terms or the Service shall be subject to the exclusive jurisdiction of the courts of Malaysia.
    </p>

    <h2>14. Changes to these Terms</h2>
    <p>
      We may update these Terms from time to time. Material changes will be notified by email or by a
      prominent in-product notice. Your continued use of the Service after the effective date of the
      revised Terms constitutes acceptance.
    </p>

    <h2>15. Contact</h2>
    <p>
      Questions about these Terms can be sent to
      <a href="mailto:<?= e($supportEmail) ?>"><?= e($supportEmail) ?></a>.
    </p>
  </div>
</section>

<?php require __DIR__ . '/inc/legal_footer.php'; ?>
</body>
</html>
