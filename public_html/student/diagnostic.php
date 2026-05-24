<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/diagnostic.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

// Handle submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $subjectId = input_int('subject_id');
    $answers   = $_POST['answer'] ?? []; // question_id => option_id
    if ($subjectId && is_array($answers) && $answers) {
        $attemptId = grade_diagnostic($uid, $subjectId, array_map('intval', $answers));
        flash('success', 'Diagnostic complete! Here are your results.');
        redirect('student/diagnostic.php?attempt=' . $attemptId);
    }
    flash('error', 'Please answer at least one question.');
    redirect('student/diagnostic.php');
}

$attemptId = input_int('attempt');
$subjectId = input_int('subject_id');

// ---- Result view ----
if ($attemptId) {
    $attempt = db_one('SELECT * FROM diagnostic_attempts WHERE id = ? AND user_id = ?', [$attemptId, $uid]);
    $result  = $attempt ? db_one('SELECT * FROM diagnostic_results WHERE attempt_id = ?', [$attemptId]) : null;

    student_layout_start('Diagnostic Results', $user, 'diagnostic.php');
    if (!$attempt) {
        echo '<div class="card"><p class="muted">Result not found.</p></div>';
    } else {
        $strong = array_filter(array_map('trim', explode(',', (string) ($result['strong_topics'] ?? ''))));
        $weak   = array_filter(array_map('trim', explode(',', (string) ($result['weak_topics'] ?? ''))));
        ?>
        <div class="card">
          <h2>Your score: <?= e(number_format((float) $attempt['score'], 0)) ?>%</h2>
          <div class="progress-bar"><span style="width: <?= (int) round((float) $attempt['score']) ?>%"></span></div>
        </div>
        <div class="grid grid--2" style="margin-top:18px">
          <div class="card">
            <h3 style="color:var(--good)">Strong topics</h3>
            <?php if ($strong): foreach ($strong as $t): ?><span class="badge badge--good"><?= e($t) ?></span> <?php endforeach; else: ?><p class="muted">Keep practising to build strengths.</p><?php endif; ?>
          </div>
          <div class="card">
            <h3 style="color:var(--bad)">Focus areas</h3>
            <?php if ($weak): foreach ($weak as $t): ?><span class="badge badge--bad"><?= e($t) ?></span> <?php endforeach; else: ?><p class="muted">No weak topics detected. 🎉</p><?php endif; ?>
          </div>
        </div>
        <div class="card" style="margin-top:18px">
          <h3>Recommended plan</h3>
          <p><?= nl2br(e((string) ($result['recommendation'] ?? ''))) ?></p>
          <a class="btn" href="<?= url('student/learning_path.php') ?>">View my learning path</a>
          <a class="btn btn--ghost" href="<?= url('student/practice.php?subject_id=' . (int) ($attempt['test_id'] ? (db_one('SELECT subject_id FROM diagnostic_tests WHERE id=?', [$attempt['test_id']])['subject_id'] ?? 0) : 0)) ?>">Start practising</a>
        </div>
        <?php
    }
    student_layout_end();
    return;
}

// ---- Quiz view ----
if ($subjectId) {
    $subject   = db_one('SELECT id, name FROM subjects WHERE id = ? AND status = "active"', [$subjectId]);
    $questions = $subject ? diagnostic_questions($subjectId) : [];

    student_layout_start('Diagnostic Quiz', $user, 'diagnostic.php');
    if (!$subject || !$questions) {
        echo '<div class="card"><p class="muted">No diagnostic questions available for this subject yet.</p>'
           . '<a class="btn btn--ghost" href="' . url('student/diagnostic.php') . '">Back</a></div>';
        student_layout_end();
        return;
    }
    ?>
    <div class="card">
      <h2><?= e($subject['name']) ?> diagnostic</h2>
      <p class="muted">Answer the questions below. We'll identify your strong and weak topics and build a learning path.</p>
      <form method="post" action="<?= url('student/diagnostic.php') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="subject_id" value="<?= (int) $subject['id'] ?>">
        <?php foreach ($questions as $i => $q): $opts = diagnostic_options((int) $q['id']); ?>
          <div class="card" style="margin:14px 0;background:var(--bg-2)">
            <p><strong><?= $i + 1 ?>.</strong> <?= e($q['question_text']) ?></p>
            <?php foreach ($opts as $o): ?>
              <label class="field" style="display:flex;gap:10px;align-items:center;margin:6px 0;cursor:pointer">
                <input type="radio" name="answer[<?= (int) $q['id'] ?>]" value="<?= (int) $o['id'] ?>">
                <span><strong><?= e($o['label']) ?>.</strong> <?= e($o['option_text']) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
        <button class="btn" type="submit">Submit diagnostic</button>
      </form>
    </div>
    <?php
    student_layout_end();
    return;
}

// ---- Subject picker ----
$subjects = db_all('SELECT id, name, description FROM subjects WHERE status = "active" ORDER BY sort_order');
$past = db_all(
    'SELECT da.id, da.score, da.completed_at, s.name AS subject
     FROM diagnostic_attempts da JOIN diagnostic_tests dt ON dt.id = da.test_id JOIN subjects s ON s.id = dt.subject_id
     WHERE da.user_id = ? AND da.status = "completed" ORDER BY da.id DESC LIMIT 10',
    [$uid]
);

student_layout_start('Diagnostic', $user, 'diagnostic.php');
?>
<div class="card">
  <h2>Find out where you stand</h2>
  <p class="muted">Pick a subject to take a quick diagnostic. We'll map your strengths and weaknesses and build a personalised plan.</p>
</div>
<div class="grid grid--3" style="margin-top:18px">
  <?php foreach ($subjects as $s): ?>
    <div class="card feature">
      <h3><?= e($s['name']) ?></h3>
      <p class="muted"><?= e($s['description'] ?? '') ?></p>
      <a class="btn btn--sm" href="<?= url('student/diagnostic.php?subject_id=' . (int) $s['id']) ?>">Start diagnostic</a>
    </div>
  <?php endforeach; ?>
</div>
<?php if ($past): ?>
  <div class="card" style="margin-top:18px">
    <h3>Past diagnostics</h3>
    <table class="table"><thead><tr><th>Subject</th><th>Score</th><th>Date</th><th></th></tr></thead><tbody>
    <?php foreach ($past as $p): ?>
      <tr>
        <td><?= e($p['subject']) ?></td>
        <td><?= e(number_format((float) $p['score'], 0)) ?>%</td>
        <td class="muted"><?= e(date('d M Y', strtotime((string) $p['completed_at']))) ?></td>
        <td><a href="<?= url('student/diagnostic.php?attempt=' . (int) $p['id']) ?>">View</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  </div>
<?php endif; ?>
<?php
student_layout_end();
