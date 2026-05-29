<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin  = require_role('admin');
$output = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $which = input('which');
    $force = input('force') === '1';

    @set_time_limit(120); // some seeders do a lot of inserts

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

            default:
                flash('error', 'Unknown seeder.');
                redirect('admin/seeders.php');
        }
    } catch (Throwable $e) {
        $output = ['title' => 'Failed', 'lines' => [$e->getMessage()], 'kind' => 'error'];
    }
}

$seeders = [
    ['key' => 'courses',        'name' => 'Seed courses',         'desc' => 'Add the topics, skills and MCQs from the course catalog (Math, Add Maths, Physics, Chemistry, Biology, English, BM). Idempotent — already-present items are skipped.'],
    ['key' => 'demo_logins',    'name' => 'Demo logins (5 roles)','desc' => 'Create one demo account per role (student, parent, teacher, school admin, platform admin) with predictable credentials and sensible relationships.'],
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
