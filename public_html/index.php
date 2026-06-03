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
    $subjects     = db_all('SELECT * FROM subjects WHERE status = "active" ORDER BY sort_order LIMIT 24');
    $pioneer      = db_one('SELECT * FROM subjects WHERE slug = "kepintaran-buatan" AND status = "active" LIMIT 1');
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
    $featureItems = [
        'AI Tutor Chat (BM + EN)',
        'AI Writing Marker (Karangan, Rumusan, Prompt Engineering)',
        '40,000+ KSSM-aligned questions',
        'Library + Flashcards with spaced repetition',
        'AI Sandbox — compare LLMs side-by-side',
        'Snap & Check photo marking',
        'Adaptive diagnostic + learning path',
        'Streaks, mastery badges, XP',
        'Parent + teacher dashboards',
    ];
}

$steps = [
    ['1', 'Diagnose', 'Take a quick diagnostic to reveal your strong and weak topics.'],
    ['2', 'Learn',    'Follow a personalised learning path and ask the AI tutor anything.'],
    ['3', 'Practise', 'Answer questions and snap a photo of your work for instant AI marking.'],
    ['4', 'Track',    'Watch your streak, mastery and scores climb — parents see it too.'],
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
      <a href="#contact">Contact</a>
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
    <div class="lhero__art">
      <?php if (!empty($hero['image_path'])): ?>
        <img class="lhero__img" src="<?= url($hero['image_path']) ?>" alt="<?= e(APP_NAME) ?> app screenshot">
      <?php else: ?>
        <div class="chatcard" aria-hidden="true">
          <div class="chatcard__head"><span class="dot"></span><span class="dot"></span><span class="dot"></span> AI Writing Marker</div>
          <div class="msg msg--user">Tajuk: Amalan gaya hidup sihat dalam kalangan remaja… [karangan 350 patah perkataan]</div>
          <div class="msg msg--assistant"><strong>78 / 100 · Baik</strong><br>Isi 24/30 · Bahasa 22/30 · Pengolahan 16/20 · Gaya 16/20<br>+ 3 cadangan peribahasa untuk naikkan markah Gaya Bahasa.</div>
          <div class="msg msg--user">Mana satu peribahasa yang sesuai?</div>
          <div class="msg msg--assistant">Untuk perenggan 2 cuba "bagai aur dengan tebing" — gambarkan hubungan murid &amp; ibu bapa. 🔥</div>
        </div>
      <?php endif; ?>
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
        <div class="card feature"><h3><?= e($f) ?></h3><p class="muted">Built into LulusAI.</p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="subjects">
  <div class="container">
    <h2>Subjects we cover</h2>
    <p class="lead">All <?= count($subjects) ?> SPM subjects mapped to the KSSM syllabus and broken down into skills.</p>
    <div class="grid grid--4">
      <?php foreach ($subjects as $s): ?>
        <div class="card feature">
          <h3><?= e($s['name']) ?></h3>
          <?php if (!empty($s['description'])): ?>
            <p class="muted"><?= e($s['description']) ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <?php if (!$subjects): ?><p class="muted">Subjects coming soon.</p><?php endif; ?>
    </div>
  </div>
</section>

<section class="section section--alt" id="pioneer">
  <div class="container">
    <span class="pill" style="display:inline-block;margin-bottom:14px">🚀 Pioneer Track · Malaysia's first SPM-aligned AI elective</span>
    <h2>Asas Kepintaran Buatan</h2>
    <p class="lead">
      A next-generation elective covering AI fundamentals, machine learning, prompt engineering,
      ethics &amp; the future of work — taught with the same hands-on tools that real AI engineers use.
    </p>
    <div class="grid grid--3">
      <div class="card feature">
        <h3>📚 20 bab, 80+ subtopik</h3>
        <p class="muted">
          Tingkatan 4: Pengenalan AI, Data, Pemikiran Komputasi, ML, Neural Networks, NLP, Computer Vision,
          Generative AI, Prompt Engineering. Tingkatan 5: Etika, PDPA, Deepfakes, AI &amp; Pekerjaan,
          Ekonomi Malaysia, Sustainability, Capstone Project.
        </p>
      </div>
      <div class="card feature">
        <h3>✍️ Prompt Engineering Marker</h3>
        <p class="muted">
          Students write a prompt and the AI grades it on 7 criteria (Clarity, Role, Context, Constraints,
          Output Format, Examples, Robustness) and rewrites it side-by-side — so they learn by comparing
          their draft to a production-grade version.
        </p>
      </div>
      <div class="card feature">
        <h3>🧪 AI Sandbox (LLM Compare)</h3>
        <p class="muted">
          Send the same prompt to Claude Opus, Sonnet, Haiku, or GPT-4o and watch the responses appear
          side-by-side. Vote on which is better, add notes, build intuition for how different models
          think — the way prompt engineers actually do it.
        </p>
      </div>
    </div>
    <div class="center" style="margin-top:22px">
      <a class="btn" href="<?= url('register.php') ?>">Try the AI elective free for 14 days</a>
    </div>
  </div>
</section>

<section class="section" id="parents">
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

<section class="section section--alt" id="contact">
  <div class="container lhero__grid">
    <div>
      <h2 style="text-align:left">Talk to us</h2>
      <p class="muted" style="font-size:18px">Questions about plans, schools, or getting started? Send us a message and we'll get back to you.</p>
      <p class="muted">Or email <a href="mailto:<?= e(db_one("SELECT setting_value FROM site_settings WHERE setting_key='support_email'")['setting_value'] ?? 'hello@lulusai.my') ?>"><?= e(db_one("SELECT setting_value FROM site_settings WHERE setting_key='support_email'")['setting_value'] ?? 'hello@lulusai.my') ?></a>.</p>
    </div>
    <div class="card">
      <?php render_flashes(); ?>
      <form method="post" action="<?= url('contact.php') ?>">
        <?= csrf_field() ?>
        <div class="field"><label>Name</label><input class="input" name="name" required></div>
        <div class="field"><label>Email</label><input class="input" type="email" name="email" required></div>
        <div class="field"><label>Phone (optional)</label><input class="input" name="phone"></div>
        <div class="field"><label>Message</label><textarea name="message" placeholder="How can we help?"></textarea></div>
        <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Website</label><input name="website" tabindex="-1" autocomplete="off"></div>
        <button class="btn btn--block" type="submit">Send message</button>
      </form>
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
