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

    <?php
    // Tag each subject with a category icon for the carousel. Pioneer AI
    // elective lives in its own section below — exclude it here.
    $catalogIcons = [
        'mathematics' => '🧪', 'add-maths' => '🧪', 'physics' => '🧪', 'chemistry' => '🧪', 'biology' => '🧪', 'sains' => '🧪',
        'bahasa-melayu' => '🗣️', 'english' => '🗣️', 'bahasa-cina' => '🗣️', 'bahasa-tamil' => '🗣️', 'bahasa-arab' => '🗣️',
        'sejarah' => '📜', 'geografi' => '📜', 'pendidikan-islam' => '📜', 'pendidikan-moral' => '📜',
        'perakaunan' => '💼', 'perniagaan' => '💼', 'ekonomi' => '💼',
        'sains-komputer' => '💻', 'rbt' => '💻', 'psv' => '🎨',
        'tasawwur-islam' => '🕌', 'pqs' => '🕌', 'psi' => '🕌',
    ];
    $marqueeSubjects = array_filter($subjects, fn($s) => $s['slug'] !== 'kepintaran-buatan');
    $marqueeSubjects = array_values($marqueeSubjects);
    // Split into two rows; row B reverses for the opposite-direction track.
    $half = (int) ceil(count($marqueeSubjects) / 2);
    $rowA = array_slice($marqueeSubjects, 0, $half);
    $rowB = array_reverse(array_slice($marqueeSubjects, $half));
    ?>

    <div class="subj-marquee" aria-label="All SPM subjects">
      <div class="subj-marquee__row">
        <div class="subj-marquee__track">
          <?php for ($pass = 0; $pass < 2; $pass++): ?>
            <?php foreach ($rowA as $s): $icon = $catalogIcons[$s['slug']] ?? '📚'; ?>
              <span class="subj-pill"><span class="subj-pill__icon"><?= e($icon) ?></span><?= e($s['name']) ?></span>
            <?php endforeach; ?>
          <?php endfor; ?>
        </div>
      </div>
      <div class="subj-marquee__row">
        <div class="subj-marquee__track subj-marquee__track--reverse">
          <?php for ($pass = 0; $pass < 2; $pass++): ?>
            <?php foreach ($rowB as $s): $icon = $catalogIcons[$s['slug']] ?? '📚'; ?>
              <span class="subj-pill"><span class="subj-pill__icon"><?= e($icon) ?></span><?= e($s['name']) ?></span>
            <?php endforeach; ?>
          <?php endfor; ?>
        </div>
      </div>
    </div>

    <p class="muted center" style="margin-top:18px;font-size:13px">
      🧪 Sciences · 🗣️ Languages · 📜 Humanities · 💼 Commerce · 💻 Technology · 🎨 Arts · 🕌 Religious electives
      <br>Plus a Pioneer Track AI elective — see below ↓
    </p>
  </div>
</section>

<style>
.subj-marquee {
  margin-top: 24px;
  position: relative;
  overflow: hidden;
  -webkit-mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
          mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
}
.subj-marquee__row { display: flex; overflow: hidden; padding: 6px 0; }
.subj-marquee__track {
  display: flex;
  flex-shrink: 0;
  gap: 12px;
  padding-right: 12px;
  animation: subj-marquee 48s linear infinite;
  will-change: transform;
}
.subj-marquee__track--reverse { animation-direction: reverse; animation-duration: 56s; }
.subj-marquee:hover .subj-marquee__track { animation-play-state: paused; }
@keyframes subj-marquee {
  from { transform: translate3d(0, 0, 0); }
  to   { transform: translate3d(-50%, 0, 0); }
}
.subj-pill {
  display: inline-flex; align-items: center; gap: 8px;
  flex-shrink: 0;
  padding: 9px 16px;
  background: var(--card-2, rgba(255,255,255,0.04));
  border: 1px solid var(--border);
  border-radius: 999px;
  font-size: 14px;
  color: var(--text);
  white-space: nowrap;
  transition: border-color .15s, background .15s, transform .15s;
}
.subj-pill:hover { border-color: var(--primary); background: rgba(139,92,246,.08); transform: translateY(-1px); }
.subj-pill__icon { font-size: 15px; line-height: 1; }
@media (prefers-reduced-motion: reduce) {
  .subj-marquee__track { animation: none; transform: translateX(0); }
}
</style>

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
    <p class="lead">Real feedback from families using LulusAI today.</p>
    <div class="testi-carousel" role="region" aria-label="Testimonials, swipe to see more">
      <?php foreach ($testimonials as $t):
        $rawName = trim((string) $t['name']);
        // Strip trailing ", Parent" etc. from name to get a cleaner display + initial.
        $cleanName = preg_replace('/,.*$/', '', $rawName) ?: $rawName;
        $initial = mb_strtoupper(mb_substr($cleanName, 0, 1));
      ?>
        <article class="testi">
          <div class="testi__stars" aria-label="5 out of 5 stars">★★★★★</div>
          <p class="testi__quote">&ldquo;<?= e((string) $t['quote']) ?>&rdquo;</p>
          <div class="testi__attrib">
            <div class="testi__avatar" aria-hidden="true"><?= e($initial) ?></div>
            <div>
              <strong><?= e($cleanName) ?></strong>
              <?php if (!empty($t['role'])): ?>
                <div class="muted" style="font-size:13px"><?= e((string) $t['role']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="muted center testi-hint">← swipe →</p>
  </div>
</section>
<style>
.testi-carousel {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
  margin-top: 18px;
}
.testi {
  background: var(--card, rgba(255,255,255,0.03));
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 22px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.testi__stars { color: #fbbf24; font-size: 14px; letter-spacing: 2px; }
.testi__quote { font-size: 17px; line-height: 1.55; margin: 0; flex: 1; }
.testi__attrib { display: flex; align-items: center; gap: 12px; margin-top: 6px; }
.testi__avatar {
  width: 42px; height: 42px;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--primary, #8b5cf6), var(--accent, #ec4899));
  color: #fff; font-weight: 700; font-size: 17px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.testi-hint { display: none; font-size: 12px; margin-top: 10px; letter-spacing: 4px; }

@media (max-width: 880px) {
  .testi-carousel {
    display: flex;
    grid-template-columns: none;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    gap: 14px;
    padding: 4px 20px 14px;
    margin: 18px -20px 0;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }
  .testi-carousel::-webkit-scrollbar { display: none; }
  .testi {
    flex: 0 0 86%;
    scroll-snap-align: center;
    padding: 20px;
  }
  .testi__quote { font-size: 16px; }
  .testi-hint { display: block; }
}
</style>
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
</body>
</html>
