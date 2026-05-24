<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/parent_layout.php';

$user = require_role('parent');
$uid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(input('student_email'));
    $student = db_one("SELECT id, name FROM users WHERE email = ? AND role = 'student'", [$email]);
    if (!$student) {
        flash('error', 'No student account found with that email.');
    } else {
        try {
            db_exec(
                'INSERT INTO parent_student_links (parent_user_id, student_user_id, relationship, status) VALUES (?,?,?,?)',
                [$uid, $student['id'], input('relationship') ?: 'parent', 'active']
            );
            flash('success', 'Linked to ' . $student['name'] . '.');
        } catch (Throwable $e) {
            flash('info', 'That student is already linked.');
        }
    }
    redirect('parent/dashboard.php');
}

$children = db_all(
    'SELECT u.id, u.name, u.email FROM parent_student_links l
     JOIN users u ON u.id = l.student_user_id
     WHERE l.parent_user_id = ? AND l.status = "active"',
    [$uid]
);

parent_layout_start('Parent Dashboard', $user, 'dashboard.php');
?>
<div class="card">
  <h3>Link to your child</h3>
  <p class="muted">Enter the email your child uses on <?= e(APP_NAME) ?>.</p>
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end">
    <?= csrf_field() ?>
    <div class="field" style="margin:0;flex:1;min-width:220px"><label>Student email</label><input class="input" type="email" name="student_email" required></div>
    <div class="field" style="margin:0"><label>Relationship</label><input class="input" name="relationship" placeholder="Mother / Father"></div>
    <button class="btn">Link</button>
  </form>
</div>

<?php foreach ($children as $c):
    $cid = (int) $c['id'];
    $summary = db_one('SELECT * FROM student_progress_summary WHERE user_id = ?', [$cid]) ?? ['total_questions' => 0, 'avg_score' => 0];
    $streak  = db_one('SELECT current_streak FROM student_streaks WHERE user_id = ?', [$cid]) ?? ['current_streak' => 0];
    $weak    = db_all('SELECT t.name, ts.mastery FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id WHERE ts.user_id = ? ORDER BY ts.mastery ASC LIMIT 3', [$cid]);
    $strong  = db_all('SELECT t.name, ts.mastery FROM student_topic_stats ts JOIN topics t ON t.id = ts.topic_id WHERE ts.user_id = ? ORDER BY ts.mastery DESC LIMIT 3', [$cid]);
    $report  = db_one('SELECT summary, recommendation FROM weekly_ai_reports WHERE student_user_id = ? ORDER BY id DESC LIMIT 1', [$cid]);
?>
  <div class="card" style="margin-top:18px">
    <h3><?= e($c['name']) ?> <span class="muted" style="font-size:13px"><?= e($c['email']) ?></span></h3>
    <div class="grid grid--3">
      <div class="card stat"><div class="stat__value"><?= (int)$summary['total_questions'] ?></div><div class="stat__label">Questions answered</div></div>
      <div class="card stat"><div class="stat__value"><?= e(number_format((float)$summary['avg_score'],0)) ?>%</div><div class="stat__label">Average score</div></div>
      <div class="card stat"><div class="stat__value"><?= (int)$streak['current_streak'] ?> 🔥</div><div class="stat__label">Day streak</div></div>
    </div>
    <div class="grid grid--2" style="margin-top:14px">
      <div>
        <strong>Needs attention</strong>
        <?php if ($weak): foreach ($weak as $w): ?>
          <p class="muted" style="margin:4px 0"><?= e($w['name']) ?> &mdash; <?= e(number_format((float)$w['mastery'],0)) ?>%</p>
        <?php endforeach; else: ?><p class="muted">Not enough data yet.</p><?php endif; ?>
      </div>
      <div>
        <strong>Doing well</strong>
        <?php if ($strong): foreach ($strong as $w): ?>
          <p class="muted" style="margin:4px 0"><?= e($w['name']) ?> &mdash; <?= e(number_format((float)$w['mastery'],0)) ?>%</p>
        <?php endforeach; else: ?><p class="muted">Not enough data yet.</p><?php endif; ?>
      </div>
    </div>
    <div class="flash flash--info" style="margin-top:14px">
      <strong>Weekly AI report:</strong>
      <?= $report ? e($report['summary']) . ' ' . e($report['recommendation'] ?? '') : 'Reports generate after a week of activity. (AI Parent Report &mdash; Phase 2/3.)' ?>
    </div>
  </div>
<?php endforeach; ?>

<?php if (!$children): ?>
  <div class="card" style="margin-top:18px"><p class="muted">No children linked yet. Use the form above to link your first child.</p></div>
<?php endif; ?>
<?php
parent_layout_end();
