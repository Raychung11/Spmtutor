<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

/** Render a horizontal bar list from [label => value]. */
function bar_list(array $data, string $suffix = ''): void
{
    if (!$data) {
        echo '<p class="muted">No data yet.</p>';
        return;
    }
    $max = max(1, ...array_map('floatval', $data));
    foreach ($data as $label => $value) {
        $pct = (int) round((float) $value / $max * 100);
        echo '<div style="margin:6px 0">';
        echo '<div style="display:flex;justify-content:space-between;font-size:13px"><span>' . e((string) $label) . '</span><span class="muted">' . e((string) $value) . e($suffix) . '</span></div>';
        echo '<div class="progress-bar"><span style="width:' . $pct . '%"></span></div>';
        echo '</div>';
    }
}

// Signups per day (last 14 days).
$signupRows = db_all(
    "SELECT DATE(created_at) d, COUNT(*) c FROM users
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY) GROUP BY DATE(created_at)"
);
$attemptRows = db_all(
    "SELECT DATE(created_at) d, COUNT(*) c FROM question_attempts
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY) GROUP BY DATE(created_at)"
);
$signups = $attempts = [];
for ($i = 13; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i day"));
    $label = date('d/m', strtotime($day));
    $signups[$label]  = 0;
    $attempts[$label] = 0;
}
foreach ($signupRows as $r) { $signups[date('d/m', strtotime($r['d']))] = (int) $r['c']; }
foreach ($attemptRows as $r) { $attempts[date('d/m', strtotime($r['d']))] = (int) $r['c']; }

$revenueByPlan = [];
foreach (db_all(
    "SELECT p.name, COALESCE(SUM(pt.amount),0) total
     FROM payment_transactions pt JOIN user_subscriptions us ON us.id = pt.subscription_id
     JOIN subscription_plans p ON p.id = us.plan_id
     WHERE pt.status = 'paid' GROUP BY p.id"
) as $r) { $revenueByPlan[$r['name']] = (float) $r['total']; }

$subsByPlan = [];
foreach (db_all(
    "SELECT p.name, COUNT(*) c FROM user_subscriptions us JOIN subscription_plans p ON p.id = us.plan_id
     WHERE us.status IN ('active','trialing') GROUP BY p.id"
) as $r) { $subsByPlan[$r['name']] = (int) $r['c']; }

$topSubjects = [];
foreach (db_all(
    "SELECT s.name, COUNT(qa.id) c FROM question_attempts qa
     JOIN questions q ON q.id = qa.question_id JOIN subjects s ON s.id = q.subject_id
     GROUP BY s.id ORDER BY c DESC LIMIT 8"
) as $r) { $topSubjects[$r['name']] = (int) $r['c']; }

$weakTopics = [];
foreach (db_all(
    "SELECT t.name, ROUND(AVG(ts.mastery),0) m FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id
     GROUP BY t.id ORDER BY m ASC LIMIT 8"
) as $r) { $weakTopics[$r['name']] = (int) $r['m']; }

$aiMessages = (int) (db_one("SELECT COUNT(*) c FROM ai_chat_messages WHERE role = 'assistant'")['c'] ?? 0);
$aiMarkings = (int) (db_one('SELECT COUNT(*) c FROM ai_marking_results')['c'] ?? 0);
$totalRevenue = (float) (db_one("SELECT COALESCE(SUM(amount),0) s FROM payment_transactions WHERE status = 'paid'")['s'] ?? 0);
$channels = [];
foreach (db_all('SELECT channel, status, COUNT(*) c FROM notification_logs GROUP BY channel, status') as $r) {
    $channels[$r['channel'] . ' (' . $r['status'] . ')'] = (int) $r['c'];
}

admin_layout_start('Advanced Analytics', $admin, 'analytics.php');
?>
<div class="grid grid--4">
  <div class="card stat"><div class="stat__value">RM<?= e(number_format($totalRevenue, 0)) ?></div><div class="stat__label">Total revenue</div></div>
  <div class="card stat"><div class="stat__value"><?= $aiMessages ?></div><div class="stat__label">AI tutor replies</div></div>
  <div class="card stat"><div class="stat__value"><?= $aiMarkings ?></div><div class="stat__label">AI markings</div></div>
  <div class="card stat"><div class="stat__value"><?= array_sum($subsByPlan) ?></div><div class="stat__label">Active subscriptions</div></div>
</div>

<div class="grid grid--2" style="margin-top:18px">
  <div class="card"><h3>New signups (14 days)</h3><?php bar_list($signups); ?></div>
  <div class="card"><h3>Question attempts (14 days)</h3><?php bar_list($attempts); ?></div>
</div>

<div class="grid grid--2" style="margin-top:18px">
  <div class="card"><h3>Revenue by plan</h3><?php bar_list($revenueByPlan, ' RM'); ?></div>
  <div class="card"><h3>Active subscriptions by plan</h3><?php bar_list($subsByPlan); ?></div>
</div>

<div class="grid grid--2" style="margin-top:18px">
  <div class="card"><h3>Top subjects by activity</h3><?php bar_list($topSubjects); ?></div>
  <div class="card"><h3>Weakest topics (avg mastery)</h3><?php bar_list($weakTopics, '%'); ?></div>
</div>

<div class="card" style="margin-top:18px">
  <h3>Notification delivery</h3>
  <?php bar_list($channels); ?>
</div>
<?php
admin_layout_end();
