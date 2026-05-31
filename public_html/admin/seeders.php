<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin  = require_role('admin');
$output = null;

/** List migration files in numeric order (phase4.sql, phase5.sql, …). */
function list_migration_files(): array
{
    $dir = __DIR__ . '/../sql/migrations';
    $files = glob($dir . '/*.sql') ?: [];
    sort($files, SORT_NATURAL);
    return $files;
}

/** Track applied migrations in a tiny table; create it on demand. */
function ensure_migrations_table(): void
{
    db_exec('CREATE TABLE IF NOT EXISTS applied_migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(190) NOT NULL UNIQUE,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB');
}

function applied_migration_set(): array
{
    ensure_migrations_table();
    $rows = db_all('SELECT filename FROM applied_migrations');
    return array_column($rows, 'filename');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $which = input('which');
    $force = input('force') === '1';

    @set_time_limit(180);

    try {
        switch ($which) {
            case 'courses':
                require_once __DIR__ . '/../cron/seed_courses.php';
                $r = seed_courses();
                $output = [
                    'title'   => 'Courses seeded',
                    'lines'   => [
                        'Topics added: ' . $r['topics'],
                        'Skills added: ' . $r['skills'],
                        'Questions added: ' . $r['questions'],
                        'Questions skipped (already present): ' . $r['skipped'],
                    ],
                    'kind'    => 'success',
                ];
                break;

            case 'demo_logins':
                require_once __DIR__ . '/../cron/seed_demo_logins.php';
                $r = seed_demo_logins_run();
                $lines = ['Password for all demo accounts: ' . $r['password']];
                foreach ($r['accounts'] as $a) {
                    $lines[] = '[' . $a['action'] . '] ' . str_pad($a['role'], 13) . ' ' . $a['email'];
                }
                $output = ['title' => 'Demo logins ready', 'lines' => $lines, 'kind' => 'success'];
                break;

            case 'demo_data':
                require_once __DIR__ . '/../cron/seed_demo.php';
                $r = seed_demo_data_run($force);
                $output = [
                    'title' => 'Demo data',
                    'lines' => [$r['message']],
                    'kind'  => $r['status'] === 'seeded' ? 'success' : 'info',
                ];
                break;

            case 'weekly_reports':
                require_once __DIR__ . '/../cron/weekly_reports.php';
                $r = run_weekly_reports();
                $output = [
                    'title' => 'Weekly AI reports',
                    'lines' => ['Generated: ' . $r['generated'] . ' / ' . $r['total'], 'Failed: ' . $r['failed']],
                    'kind'  => 'success',
                ];
                break;

            case 'reminders':
                require_once __DIR__ . '/../cron/reminders.php';
                $r = run_reminders();
                $output = [
                    'title' => 'Daily reminders',
                    'lines' => ['Notified: ' . $r['sent'] . ' / ' . $r['total'], 'WhatsApp delivered: ' . $r['whatsapp']],
                    'kind'  => 'success',
                ];
                break;

            case 'schools':
                require_once __DIR__ . '/../cron/seed_schools.php';
                $r = seed_schools_run();
                $output = [
                    'title' => 'Klang Valley schools seeded',
                    'lines' => [
                        'Created: ' . $r['created'],
                        'Already present (kept): ' . $r['kept'],
                        'Total in catalog: ' . $r['total'],
                        'Priority targets in catalog: ' . $r['priority'] . ' (of which ' . $r['priority_created'] . ' were just created)',
                    ],
                    'kind'  => 'success',
                ];
                break;

            case 'subjects':
                require_once __DIR__ . '/../cron/seed_subjects.php';
                $r = seed_subjects_run();
                if (($r['status'] ?? '') === 'no_level') {
                    $output = ['title' => 'SPM subjects', 'lines' => [$r['message']], 'kind' => 'error'];
                } else {
                    $output = [
                        'title' => 'SPM subjects seeded',
                        'lines' => [
                            'Created: ' . $r['created'],
                            'Already present (kept): ' . $r['kept'],
                            'Total in catalog: ' . $r['total'],
                        ],
                        'kind'  => 'success',
                    ];
                }
                break;

            case 'math_kssm':
                require_once __DIR__ . '/../cron/seed_math_kssm.php';
                $r = math_kssm_run();
                if (($r['status'] ?? '') !== 'ok') {
                    $output = ['title' => 'KSSM Mathematics', 'lines' => [$r['message'] ?? 'Failed.'], 'kind' => 'error'];
                } else {
                    $output = [
                        'title' => 'KSSM Mathematics seeded',
                        'lines' => [
                            'Topics created: ' . $r['topics_created'] . ' (kept ' . $r['topics_kept'] . ', catalog ' . $r['catalog_topics'] . ')',
                            'Subtopics created: ' . $r['subtopics_created'] . ' (kept ' . $r['subtopics_kept'] . ')',
                            'Starter skills created: ' . $r['skills_created'] . ' (kept ' . $r['skills_kept'] . ')',
                            'Next: open Admin → Topics, filter by Mathematics, and use 🤖 Skills / 🤖 Qs on each row.',
                        ],
                        'kind'  => 'success',
                    ];
                }
                break;

            case 'migrations':
                $applied = applied_migration_set();
                $cfg = require __DIR__ . '/../config/db_config.php';
                $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['name'], $cfg['charset']);
                $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                    PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => true,
                ]);
                $lines = [];
                foreach (list_migration_files() as $path) {
                    $name = basename($path);
                    if (!$force && in_array($name, $applied, true)) {
                        $lines[] = '[skip] ' . $name . ' — already applied';
                        continue;
                    }
                    try {
                        $pdo->exec(file_get_contents($path));
                        db_exec(
                            'INSERT INTO applied_migrations (filename) VALUES (?)
                             ON DUPLICATE KEY UPDATE applied_at = NOW()',
                            [$name]
                        );
                        $lines[] = '[ok]   ' . $name;
                    } catch (Throwable $e) {
                        $lines[] = '[FAIL] ' . $name . ' — ' . $e->getMessage();
                    }
                }
                $output = ['title' => 'Migrations', 'lines' => $lines ?: ['No migration files found.'], 'kind' => 'success'];
                break;

            default:
                flash('error', 'Unknown seeder.');
                redirect('admin/seeders.php');
        }
    } catch (Throwable $e) {
        $output = ['title' => 'Failed', 'lines' => [$e->getMessage()], 'kind' => 'error'];
    }
}

$seeders = [
    ['key' => 'migrations',     'name' => 'Run database migrations', 'desc' => 'Apply any pending sql/migrations/*.sql files (e.g. phase4, phase5, phase6 school portal, phase7 invitations, phase8 leads). Each file runs at most once. Tracks state in an applied_migrations table.', 'has_force' => true],
    ['key' => 'courses',        'name' => 'Seed courses',         'desc' => 'Add the topics, skills and MCQs from the course catalog (Math, Add Maths, Physics, Chemistry, Biology, English, BM). Idempotent — already-present items are skipped.'],
    ['key' => 'demo_logins',    'name' => 'Demo logins (5 roles)','desc' => 'Create one demo account per role (student, parent, teacher, school admin, platform admin) with predictable credentials and sensible relationships.'],
    ['key' => 'schools',        'name' => 'Klang Valley schools',  'desc' => 'Seed ~100 SMK secondary schools across KL, PJ, Shah Alam, Subang, Klang, Kajang, Cheras, Puchong, Bangi, Cyberjaya and Putrajaya. Idempotent — schools already present (by name) are skipped.'],
    ['key' => 'subjects',       'name' => 'SPM subjects',          'desc' => 'Add the missing SPM subjects beyond the original 7 (Sejarah, Pendidikan Islam, Pendidikan Moral, Perakaunan, Perniagaan, Ekonomi, Sains Komputer, RBT, Sains, Geografi, PSV, three languages, three Islamic electives). Idempotent — existing slugs/names are kept.'],
    ['key' => 'math_kssm',      'name' => 'KSSM Mathematics (Form 4 + 5)', 'desc' => 'Seed the full KSSM SPM Mathematics syllabus: 20 chapters across Form 4 & Form 5 with all their subtopics, plus a few starter skills. Idempotent — existing topics/subtopics matched by name are kept and just refreshed with form_level.'],
    ['key' => 'demo_data',      'name' => 'Demo data (rich)',     'desc' => 'Populate sample students with attempts, subscriptions, a class, a Snap & Check marking and parent reports so dashboards look alive.', 'has_force' => true],
    ['key' => 'weekly_reports', 'name' => 'Generate weekly reports','desc' => 'Generate this week\'s AI parent report for every active student.'],
    ['key' => 'reminders',      'name' => 'Send study reminders', 'desc' => 'Notify students who have not practised yet today (in-app + WhatsApp if configured).'],
];

admin_layout_start('Seeders', $admin, 'seeders.php');
?>
<p class="muted">Run the maintenance / seed scripts from here. These are the same scripts in <code>cron/</code> — they still work from SSH or a scheduled Hostinger cron job.</p>

<?php if ($output): ?>
  <div class="flash flash--<?= $output['kind'] === 'error' ? 'error' : ($output['kind'] === 'info' ? 'info' : 'success') ?>">
    <strong><?= e($output['title']) ?></strong>
    <div style="margin-top:6px;font-family:monospace;font-size:13px;white-space:pre-wrap"><?= e(implode("\n", $output['lines'])) ?></div>
  </div>
<?php endif; ?>

<?php foreach ($seeders as $s): ?>
  <div class="card" style="margin-bottom:14px">
    <h3 style="margin-top:0"><?= e($s['name']) ?></h3>
    <p class="muted"><?= e($s['desc']) ?></p>
    <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <?= csrf_field() ?>
      <input type="hidden" name="which" value="<?= e($s['key']) ?>">
      <?php if (!empty($s['has_force'])): ?>
        <label class="muted" style="display:inline-flex;gap:6px;align-items:center;font-size:13px">
          <input type="checkbox" name="force" value="1"> Force re-run (ignore "already seeded" marker)
        </label>
      <?php endif; ?>
      <button class="btn" type="submit">Run</button>
    </form>
  </div>
<?php endforeach; ?>

<p class="muted" style="font-size:13px;margin-top:18px">
  CLI usage (e.g. Hostinger SSH or scheduled cron):
  <br><code>php public_html/cron/seed_courses.php</code>
  <br><code>php public_html/cron/seed_demo_logins.php</code>
  <br><code>php public_html/cron/seed_demo.php [--force]</code>
  <br><code>php public_html/cron/weekly_reports.php</code>
  <br><code>php public_html/cron/reminders.php</code>
</p>
<?php
admin_layout_end();
