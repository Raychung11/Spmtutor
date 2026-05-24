<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$user = require_role('admin', 'creator');

$counts = [
    'users'    => (int) (db_one('SELECT COUNT(*) c FROM users')['c'] ?? 0),
    'students' => (int) (db_one("SELECT COUNT(*) c FROM users WHERE role = 'student'")['c'] ?? 0),
    'active_subs' => (int) (db_one("SELECT COUNT(*) c FROM user_subscriptions WHERE status IN ('active','trialing')")['c'] ?? 0),
    'questions' => (int) (db_one('SELECT COUNT(*) c FROM questions')['c'] ?? 0),
    'attempts' => (int) (db_one('SELECT COUNT(*) c FROM question_attempts')['c'] ?? 0),
    'chats'    => (int) (db_one('SELECT COUNT(*) c FROM ai_chat_messages')['c'] ?? 0),
];
$revenue = (float) (db_one("SELECT COALESCE(SUM(amount),0) s FROM payment_transactions WHERE status = 'paid'")['s'] ?? 0);

$topSubjects = db_all(
    'SELECT s.name, COUNT(qa.id) c FROM question_attempts qa
     JOIN questions q ON q.id = qa.question_id JOIN subjects s ON s.id = q.subject_id
     GROUP BY s.id ORDER BY c DESC LIMIT 5'
);
$weakTopics = db_all(
    'SELECT t.name, ROUND(AVG(ts.mastery),0) m FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id
     GROUP BY t.id ORDER BY m ASC LIMIT 5'
);

admin_layout_start('Admin Dashboard', $user, 'dashboard.php');
?>
<div class="grid grid--4">
  <div class="card stat"><div class="stat__value"><?= $counts['users'] ?></div><div class="stat__label">Total users</div></div>
  <div class="card stat"><div class="stat__value"><?= $counts['students'] ?></div><div class="stat__label">Students</div></div>
  <div class="card stat"><div class="stat__value"><?= $counts['active_subs'] ?></div><div class="stat__label">Active subscriptions</div></div>
  <div class="card stat"><div class="stat__value">RM<?= e(number_format($revenue, 0)) ?></div><div class="stat__label">Revenue (paid)</div></div>
  <div class="card stat"><div class="stat__value"><?= $counts['questions'] ?></div><div class="stat__label">Questions</div></div>
  <div class="card stat"><div class="stat__value"><?= $counts['attempts'] ?></div><div class="stat__label">Question attempts</div></div>
  <div class="card stat"><div class="stat__value"><?= $counts['chats'] ?></div><div class="stat__label">AI messages</div></div>
</div>

<div class="grid grid--2" style="margin-top:18px">
  <div class="card">
    <h3>Top subjects by activity</h3>
    <?php if ($topSubjects): ?>
      <table class="table"><tbody>
      <?php foreach ($topSubjects as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= (int)$r['c'] ?> attempts</td></tr><?php endforeach; ?>
      </tbody></table>
    <?php else: ?><p class="muted">No activity yet.</p><?php endif; ?>
  </div>
  <div class="card">
    <h3>Weakest topics (all students)</h3>
    <?php if ($weakTopics): ?>
      <table class="table"><tbody>
      <?php foreach ($weakTopics as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= (int)$r['m'] ?>% mastery</td></tr><?php endforeach; ?>
      </tbody></table>
    <?php else: ?><p class="muted">No data yet.</p><?php endif; ?>
  </div>
</div>
<?php
admin_layout_end();
