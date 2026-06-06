<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

$summary = db_one('SELECT * FROM student_progress_summary WHERE user_id = ?', [$uid]) ?? ['total_questions' => 0, 'total_correct' => 0, 'avg_score' => 0];
$streak  = db_one('SELECT * FROM student_streaks WHERE user_id = ?', [$uid]) ?? ['current_streak' => 0, 'longest_streak' => 0];
$subjects = db_all(
    'SELECT s.name, ss.total_attempts, ss.total_correct, ss.avg_score
     FROM student_subject_stats ss JOIN subjects s ON s.id = ss.subject_id
     WHERE ss.user_id = ? ORDER BY ss.avg_score DESC',
    [$uid]
);
$topics = db_all(
    'SELECT t.name, ts.total_attempts, ts.mastery
     FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id
     WHERE ts.user_id = ? ORDER BY ts.mastery ASC',
    [$uid]
);
$recent = db_all(
    'SELECT q.question_text, qa.is_correct, qa.created_at
     FROM question_attempts qa JOIN questions q ON q.id = qa.question_id
     WHERE qa.user_id = ? ORDER BY qa.id DESC LIMIT 10',
    [$uid]
);

student_layout_start('Progress', $user, 'progress.php');
?>
<div class="grid grid--4">
  <div class="card stat"><div class="stat__value"><?= (int)$summary['total_questions'] ?></div><div class="stat__label">Total answered</div></div>
  <div class="card stat"><div class="stat__value"><?= (int)$summary['total_correct'] ?></div><div class="stat__label">Correct</div></div>
  <div class="card stat"><div class="stat__value"><?= e(number_format((float)$summary['avg_score'],0)) ?>%</div><div class="stat__label">Average</div></div>
  <div class="card stat"><div class="stat__value"><?= (int)$streak['longest_streak'] ?></div><div class="stat__label">Longest streak</div></div>
</div>

<div class="grid grid--2" style="margin-top:18px">
  <div class="card">
    <h3>By subject</h3>
    <?php if ($subjects): ?>
      <table class="table"><thead><tr><th>Subject</th><th>Attempts</th><th>Avg</th></tr></thead><tbody>
      <?php foreach ($subjects as $s): ?>
        <tr><td><?= e($s['name']) ?></td><td><?= (int)$s['total_attempts'] ?></td><td><?= e(number_format((float)$s['avg_score'],0)) ?>%</td></tr>
      <?php endforeach; ?>
      </tbody></table>
    <?php else: ?><p class="muted">No data yet. Start practising!</p><?php endif; ?>
  </div>
  <div class="card">
    <h3>Topic mastery</h3>
    <?php if ($topics): ?>
      <?php foreach ($topics as $t): ?>
        <p style="margin:6px 0"><?= e($t['name']) ?> <span class="muted">(<?= e(number_format((float)$t['mastery'],0)) ?>%)</span></p>
        <div class="progress-bar"><span style="width: <?= (int)round((float)$t['mastery']) ?>%"></span></div>
      <?php endforeach; ?>
    <?php else: ?><p class="muted">No topic data yet.</p><?php endif; ?>
  </div>
</div>

<div class="card" style="margin-top:18px">
  <h3>Recent activity</h3>
  <?php if ($recent): ?>
    <table class="table"><tbody>
    <?php foreach ($recent as $r): ?>
      <tr>
        <td><?= e(mb_substr($r['question_text'], 0, 70)) ?></td>
        <td><?php if ($r['is_correct'] === null): ?><span class="badge">Saved</span><?php elseif ($r['is_correct']): ?><span class="badge badge--good">Correct</span><?php else: ?><span class="badge badge--bad">Wrong</span><?php endif; ?></td>
        <td class="muted"><?= e(date('d M, H:i', strtotime($r['created_at']))) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  <?php else: ?><p class="muted">No activity yet.</p><?php endif; ?>
</div>
<?php
student_layout_end();
