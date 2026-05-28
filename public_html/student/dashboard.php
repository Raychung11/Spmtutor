<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/progress.php';
require_once __DIR__ . '/../inc/schools.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

$summary = db_one('SELECT * FROM student_progress_summary WHERE user_id = ?', [$uid]) ?? ['total_questions' => 0, 'total_correct' => 0, 'avg_score' => 0];
$streak  = db_one('SELECT * FROM student_streaks WHERE user_id = ?', [$uid]) ?? ['current_streak' => 0, 'longest_streak' => 0];
$profile = db_one('SELECT xp, level FROM student_profiles WHERE user_id = ?', [$uid]) ?? ['xp' => 0, 'level' => 1];
$mission = ensure_daily_mission($uid);
$sub     = db_one('SELECT us.*, p.name AS plan_name FROM user_subscriptions us JOIN subscription_plans p ON p.id = us.plan_id WHERE us.user_id = ? ORDER BY us.id DESC LIMIT 1', [$uid]);

$weak = db_all(
    'SELECT t.name, ts.mastery FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id
     WHERE ts.user_id = ? ORDER BY ts.mastery ASC LIMIT 4',
    [$uid]
);
$badges       = db_all('SELECT b.name, b.description FROM student_badges sb JOIN badges b ON b.id = sb.badge_id WHERE sb.user_id = ? ORDER BY sb.earned_at DESC', [$uid]);
$schoolLinks  = user_schools($uid, 'student');

student_layout_start('Dashboard', $user, 'dashboard.php');
?>
<div class="grid grid--4">
  <div class="card stat"><div class="stat__value"><?= (int)$summary['total_questions'] ?></div><div class="stat__label">Questions answered</div></div>
  <div class="card stat"><div class="stat__value"><?= e(number_format((float)$summary['avg_score'], 0)) ?>%</div><div class="stat__label">Average score</div></div>
  <div class="card stat"><div class="stat__value"><?= (int)$streak['current_streak'] ?> 🔥</div><div class="stat__label">Day streak</div></div>
  <div class="card stat"><div class="stat__value">Lv <?= (int)$profile['level'] ?></div><div class="stat__label"><?= (int)$profile['xp'] ?> XP</div></div>
</div>

<?php if ($schoolLinks): ?>
  <div class="card" style="margin-top:18px">
    <h3 style="margin-top:0">My school</h3>
    <?php foreach ($schoolLinks as $sl): ?>
      <p style="margin:6px 0"><strong><?= e($sl['name']) ?></strong>
        <span class="badge <?= $sl['status'] === 'active' ? 'badge--good' : 'badge--warn' ?>"><?= $sl['status'] === 'active' ? 'joined' : 'pending approval' ?></span>
        <span class="muted" style="font-size:13px">&middot; <?= e($sl['type'] === 'school' ? 'School' : 'Learning centre') ?></span>
      </p>
      <?php if ($sl['status'] !== 'active'): ?>
        <p class="muted" style="font-size:13px;margin:0">Your join request is waiting for the school admin to approve it.</p>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="grid grid--2" style="margin-top:18px">
  <div class="card">
    <h3>Today's mission</h3>
    <p class="muted"><?= e($mission['description']) ?></p>
    <div class="progress-bar"><span style="width: <?= min(100, (int)round($mission['progress'] / max(1,(int)$mission['target']) * 100)) ?>%"></span></div>
    <p class="muted" style="margin-top:8px"><?= (int)$mission['progress'] ?> / <?= (int)$mission['target'] ?> <?= $mission['completed'] ? '&mdash; done! 🎉' : '' ?></p>
    <a class="btn btn--sm" href="<?= url('student/practice.php') ?>">Start practising</a>
  </div>
  <div class="card">
    <h3>Quick actions</h3>
    <p><a class="btn btn--sm" href="<?= url('student/tutor.php') ?>">Ask the AI Tutor</a></p>
    <p><a class="btn btn--sm btn--ghost" href="<?= url('student/practice.php') ?>">Practice questions</a></p>
    <p><a class="btn btn--sm btn--ghost" href="<?= url('student/progress.php') ?>">View my progress</a></p>
    <?php if ($sub): ?>
      <p class="muted">Plan: <strong><?= e($sub['plan_name']) ?></strong> (<?= e($sub['status']) ?><?= $sub['ends_at'] ? ', ends ' . e(date('d M Y', strtotime($sub['ends_at']))) : '' ?>)</p>
    <?php endif; ?>
  </div>
</div>

<div class="grid grid--2" style="margin-top:18px">
  <div class="card">
    <h3>Focus areas (weakest topics)</h3>
    <?php if ($weak): ?>
      <table class="table"><tbody>
      <?php foreach ($weak as $w): ?>
        <tr><td><?= e($w['name']) ?></td><td style="width:50%"><div class="progress-bar"><span style="width: <?= (int)round((float)$w['mastery']) ?>%"></span></div></td><td><?= e(number_format((float)$w['mastery'],0)) ?>%</td></tr>
      <?php endforeach; ?>
      </tbody></table>
    <?php else: ?>
      <p class="muted">Answer some practice questions to reveal your weak topics.</p>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>Badges earned</h3>
    <?php if ($badges): ?>
      <?php foreach ($badges as $b): ?>
        <span class="badge badge--good" title="<?= e($b['description']) ?>"><?= e($b['name']) ?></span>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="muted">Keep a streak and answer questions to earn your first badge.</p>
    <?php endif; ?>
  </div>
</div>
<?php
student_layout_end();
