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

$hero     = $sections['hero']     ?? ['title' => 'Not just an app. Your personal AI tutor.', 'subtitle' => 'Learn, practise, diagnose weaknesses and improve with AI guidance.', 'body' => ''];
$features = $sections['features'] ?? null;
$parents  = $sections['parents']  ?? null;
$cta      = $sections['cta']      ?? null;
$user     = current_user();

// Sensible fallback feature list if the CMS section is empty.
$featureItems = $features ? pipe_list($features['body']) : [];
if (!$featureItems) {
    $featureItems = ['AI Tutor Chat', 'Skill Diagnostic', 'Personalised Learning Path', 'Practice Questions', 'Snap & Check marking', 'Progress Analytics'];
}

$steps = [
    ['1', 'Diagnose', 'Take a quick diagnostic to reveal your strong and weak topics.'],
    ['2', 'Learn', 'Follow a personalised learning path and ask the AI tutor anything.'],
    ['3', 'Practise', 'Answer questions and snap a photo of your work for instant AI marking.'],
    ['4', 'Track', 'Watch your streak, mastery and scores climb — parents see it too.'],
];

render_head('Your personal AI tutor');
?>
<header class="lnav">
  <div class="container lnav__inner">
    <a class="brand" href="<?= url('') ?>"><?= e(APP_NAME) ?></a>
    <nav class="lnav__links">
      <a href="#how">How it works</a>
      <a href="#features">Features</a>
      <a href="#subjects">Subjects</a>
      <a href="#parents">For parents</a>
      <a href="<?= url('pricing.php') ?>">Pricing</a>
      <?php if ($user): ?>
        <a class="btn btn--sm" href="<?= url(dashboard_for($user['role'])) ?>">Go to dashboard</a>
      <?php else: ?>
        <a href="<?= url('register-school.php') ?>">For schools</a>
        <a href="<?= url('login.php') ?>">Log in</a>
        <a class="btn btn--sm" href="<?= url('register.php') ?>">Start free</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<section class="lhero">
  <div class="container lhero__grid">
    <div class="lhero__copy">
      <span class="pill">SPM &middot; PT3 &middot; UPSR &middot; STPM</span>
      <h1><span class="grad"><?= e($hero['title']) ?></span></h1>
      <p><?= e($hero['subtitle'] ?? '') ?></p>
      <div class="lhero__cta">
        <a class="btn" href="<?= url('register.php') ?>">Start your 14-day free trial</a>
        <a class="btn btn--ghost" href="<?= url('register-school.php') ?>">I'm a school / centre</a>
      </div>
      <p class="muted" style="font-size:13px">No credit card needed. Cancel anytime.</p>
    </div>
    <div class="lhero__art" aria-hidden="true">
      <div class="chatcard">
        <div class="chatcard__head"><span class="dot"></span><span class="dot"></span><span class="dot"></span> AI Tutor</div>
        <div class="msg msg--user">How do I solve 2x + 3 = 11?</div>
        <div class="msg msg--assistant">Great question! First subtract 3 from both sides → 2x = 8. Now divide by 2. What do you get? 😊</div>
        <div class="msg msg--user">x = 4!</div>
        <div class="msg msg--assistant">Exactly — well done! +10 XP 🔥</div>
      </div>
    </div>
  </div>
</section>

<section class="section" id="how">
  <div class="container">
    <h2>How it works</h2>
    <p class="lead">Four simple steps from "stuck" to "I've got this."</p>
    <div class="grid grid--4">
      <?php foreach ($steps as $s): ?>
        <div class="card feature"><div class="stepnum"><?= e($s[0]) ?></div><h3><?= e($s[1]) ?></h3><p class="muted"><?= e($s[2]) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--alt" id="features">
  <div class="container">
    <h2><?= e($features['title'] ?? 'Everything your child needs to improve') ?></h2>
    <p class="lead"><?= e($features['subtitle'] ?? 'An AI Skill Education Operating System') ?></p>
    <div class="grid grid--3">
      <?php foreach ($featureItems as $f): ?>
        <div class="card feature"><h3><?= e($f) ?></h3><p class="muted">Built into SkillTutor AI.</p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="subjects">
  <div class="container">
    <h2>Subjects we cover</h2>
    <p class="lead">Mapped to the Malaysian syllabus and broken down into skills.</p>
    <div class="grid grid--4">
      <?php foreach ($subjects as $s): ?>
        <div class="card feature"><h3><?= e($s['name']) ?></h3><p class="muted"><?= e($s['description'] ?? '') ?></p></div>
      <?php endforeach; ?>
      <?php if (!$subjects): ?><p class="muted">Subjects coming soon.</p><?php endif; ?>
    </div>
  </div>
</section>

<section class="section section--alt" id="parents">
  <div class="container lhero__grid">
    <div>
      <h2 style="text-align:left"><?= e($parents['title'] ?? 'Built for parents too') ?></h2>
      <p class="muted" style="font-size:18px"><?= e($parents['subtitle'] ?? 'See real progress, not just screen time.') ?></p>
      <ul class="ticks">
        <?php
        $parentPoints = $parents ? pipe_list($parents['body']) : [];
        if (!$parentPoints) {
            $parentPoints = ['Weekly AI progress reports', 'Weak-area alerts and recommended actions', 'Track learning streaks and scores', 'One parent account, multiple children'];
        }
        foreach ($parentPoints as $p): ?>
          <li><?= e($p) ?></li>
        <?php endforeach; ?>
      </ul>
      <a class="btn" href="<?= url('register.php') ?>">Create a parent account</a>
    </div>
    <div class="card">
      <h3 style="margin-top:0">Weekly report</h3>
      <p class="muted">"Ar Ryan is improving in Algebra but still weak in Geometry. Recommended: 15 minutes daily practice on Geometry this week."</p>
      <div class="grid grid--3">
        <div class="stat"><div class="stat__value">86%</div><div class="stat__label">Avg score</div></div>
        <div class="stat"><div class="stat__value">7🔥</div><div class="stat__label">Day streak</div></div>
        <div class="stat"><div class="stat__value">124</div><div class="stat__label">Questions</div></div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2>Simple, honest pricing</h2>
    <p class="lead">Start free. Upgrade when you see the results.</p>
    <div class="grid grid--3">
      <?php foreach ($plans as $p): ?>
        <div class="card price-card<?= $p['code'] === 'monthly' ? ' price-card--featured' : '' ?>">
          <h3><?= e($p['name']) ?></h3>
          <div class="price">
            <?= $p['price'] > 0 ? 'RM' . e(number_format((float)$p['price'], 0)) : 'Free' ?>
            <small><?= $p['billing_cycle'] === 'annual' ? '/year' : ($p['billing_cycle'] === 'monthly' ? '/month' : '/14 days') ?></small>
          </div>
          <ul><?php foreach (pipe_list($p['features']) as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul>
          <a class="btn btn--block" href="<?= url('register.php') ?>">Choose <?= e($p['name']) ?></a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($testimonials): ?>
<section class="section section--alt">
  <div class="container">
    <h2>Loved by students and parents</h2>
    <div class="grid grid--2">
      <?php foreach ($testimonials as $t): ?>
        <div class="card"><p style="font-size:18px">&ldquo;<?= e($t['quote']) ?>&rdquo;</p><p class="muted"><strong><?= e($t['name']) ?></strong> &middot; <?= e($t['role'] ?? '') ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section band">
  <div class="container center">
    <h2>Are you a school or learning centre?</h2>
    <p class="lead">Onboard your teachers and students, set assignments, and track school-wide progress.</p>
    <a class="btn" href="<?= url('register-school.php') ?>">Register your school</a>
  </div>
</section>

<?php if ($faqs): ?>
<section class="section" id="faq">
  <div class="container">
    <h2>Frequently asked questions</h2>
    <div class="card">
      <?php foreach ($faqs as $f): ?>
        <details class="faq"><summary><?= e($f['question']) ?></summary><p class="muted"><?= e($f['answer']) ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section">
  <div class="container center">
    <div class="card">
      <h2><?= e($cta['title'] ?? 'Ready to start learning smarter?') ?></h2>
      <p class="lead"><?= e($cta['subtitle'] ?? 'Create your free account in under a minute.') ?></p>
      <a class="btn" href="<?= url('register.php') ?>">Create your free account</a>
    </div>
  </div>
</section>

<footer class="site">
  <div class="container lfooter">
    <div>
      <div class="brand"><?= e(APP_NAME) ?></div>
      <p class="muted">Not just an app. Your personal AI tutor.</p>
    </div>
    <div>
      <strong>Product</strong>
      <a href="#features">Features</a>
      <a href="#subjects">Subjects</a>
      <a href="<?= url('pricing.php') ?>">Pricing</a>
    </div>
    <div>
      <strong>Get started</strong>
      <a href="<?= url('register.php') ?>">Student / parent</a>
      <a href="<?= url('register-school.php') ?>">Schools</a>
      <a href="<?= url('login.php') ?>">Log in</a>
    </div>
  </div>
  <div class="container center muted" style="border-top:1px solid var(--border);padding-top:18px;margin-top:18px">
    &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.
  </div>
</footer>
</body>
</html>
