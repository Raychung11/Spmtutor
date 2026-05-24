<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

// If the DB isn't installed yet, guide the user to the installer.
try {
    $sections = [];
    foreach (db_all('SELECT * FROM landing_sections WHERE status = "active" ORDER BY sort_order') as $s) {
        $sections[$s['section_key']] = $s;
    }
    $subjects     = db_all('SELECT * FROM subjects WHERE status = "active" ORDER BY sort_order LIMIT 8');
    $plans        = db_all('SELECT * FROM subscription_plans WHERE status = "active" ORDER BY sort_order');
    $testimonials = db_all('SELECT * FROM testimonials WHERE status = "active" ORDER BY sort_order');
    $faqs         = db_all('SELECT * FROM faqs WHERE status = "active" ORDER BY sort_order');
} catch (Throwable $e) {
    header('Location: ' . url('install.php'));
    exit;
}

$hero     = $sections['hero']     ?? ['title' => APP_NAME, 'subtitle' => '', 'body' => ''];
$features = $sections['features'] ?? null;
$cta      = $sections['cta']      ?? null;
$user     = current_user();

render_head('Your personal AI tutor');
?>
<div class="container">
  <nav class="nav">
    <a class="brand" href="<?= url('') ?>"><?= e(APP_NAME) ?></a>
    <div class="nav__links">
      <a href="#features">Features</a>
      <a href="#subjects">Subjects</a>
      <a href="<?= url('pricing.php') ?>">Pricing</a>
      <a href="#faq">FAQ</a>
      <?php if ($user): ?>
        <a class="btn btn--sm" href="<?= url(dashboard_for($user['role'])) ?>">Dashboard</a>
      <?php else: ?>
        <a href="<?= url('login.php') ?>">Log in</a>
        <a class="btn btn--sm" href="<?= url('register.php') ?>">Start free</a>
      <?php endif; ?>
    </div>
  </nav>

  <section class="hero">
    <h1><span class="grad"><?= e($hero['title']) ?></span></h1>
    <p><?= e($hero['subtitle'] ?? '') ?></p>
    <a class="btn" href="<?= url('register.php') ?>">Start your 14-day free trial</a>
  </section>

  <?php if ($features): ?>
  <section class="section" id="features">
    <h2><?= e($features['title']) ?></h2>
    <p class="lead"><?= e($features['subtitle'] ?? '') ?></p>
    <div class="grid grid--3">
      <?php foreach (pipe_list($features['body']) as $f): ?>
        <div class="card feature"><h3><?= e($f) ?></h3><p class="muted">Powered by SkillTutor AI.</p></div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="section" id="subjects">
    <h2>Subjects we cover</h2>
    <p class="lead">Mapped to the Malaysian syllabus and broken down into skills.</p>
    <div class="grid grid--4">
      <?php foreach ($subjects as $s): ?>
        <div class="card feature"><h3><?= e($s['name']) ?></h3><p class="muted"><?= e($s['description'] ?? '') ?></p></div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section" id="pricing">
    <h2>Simple, honest pricing</h2>
    <p class="lead">Start free. Upgrade when you see the results.</p>
    <div class="grid grid--3">
      <?php foreach ($plans as $i => $p): ?>
        <div class="card price-card<?= $p['code'] === 'monthly' ? ' price-card--featured' : '' ?>">
          <h3><?= e($p['name']) ?></h3>
          <div class="price">
            <?= $p['price'] > 0 ? 'RM' . e(number_format((float)$p['price'], 0)) : 'Free' ?>
            <small><?= $p['billing_cycle'] === 'annual' ? '/year' : ($p['billing_cycle'] === 'monthly' ? '/month' : '/14 days') ?></small>
          </div>
          <ul>
            <?php foreach (pipe_list($p['features']) as $f): ?><li><?= e($f) ?></li><?php endforeach; ?>
          </ul>
          <a class="btn btn--block" href="<?= url('register.php') ?>">Choose <?= e($p['name']) ?></a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php if ($testimonials): ?>
  <section class="section">
    <h2>Loved by students and parents</h2>
    <div class="grid grid--2">
      <?php foreach ($testimonials as $t): ?>
        <div class="card"><p>&ldquo;<?= e($t['quote']) ?>&rdquo;</p><p class="muted"><strong><?= e($t['name']) ?></strong> &middot; <?= e($t['role'] ?? '') ?></p></div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($faqs): ?>
  <section class="section" id="faq">
    <h2>Frequently asked questions</h2>
    <div class="card">
      <?php foreach ($faqs as $f): ?>
        <details class="faq"><summary><?= e($f['question']) ?></summary><p class="muted"><?= e($f['answer']) ?></p></details>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($cta): ?>
  <section class="section center">
    <div class="card">
      <h2><?= e($cta['title']) ?></h2>
      <p class="lead"><?= e($cta['subtitle'] ?? '') ?></p>
      <a class="btn" href="<?= url('register.php') ?>">Create your free account</a>
    </div>
  </section>
  <?php endif; ?>
</div>

<footer class="site">
  <div class="container">&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Not just an app. Your personal AI tutor.</div>
</footer>
</body>
</html>
