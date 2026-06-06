<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/progress.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

$subjects  = db_all('SELECT id, name FROM subjects WHERE status = "active" ORDER BY sort_order');
$subjectId = input_int('subject_id', (int) ($subjects[0]['id'] ?? 0));
$feedback  = null;

// Handle an answer submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $qid = input_int('question_id');
    $question = db_one('SELECT * FROM questions WHERE id = ? AND status = "active"', [$qid]);
    if ($question) {
        $subjectId = (int) $question['subject_id'];
        if ($question['type'] === 'mcq') {
            $optId   = input_int('option_id');
            $correct = db_one('SELECT id, is_correct, option_text FROM question_options WHERE id = ? AND question_id = ?', [$optId, $qid]);
            $isCorrect = $correct ? (bool) $correct['is_correct'] : false;
            record_attempt($uid, $question, $optId ?: null, null, $isCorrect);
            $right = db_one('SELECT label, option_text FROM question_options WHERE question_id = ? AND is_correct = 1', [$qid]);
            $feedback = [
                'correct'     => $isCorrect,
                'your'        => $correct['option_text'] ?? '(none)',
                'right'       => $right ? ($right['label'] . '. ' . $right['option_text']) : '',
                'explanation' => $question['explanation'],
            ];
        } else {
            $text = input('answer_text');
            // Open answers are stored ungraded for AI/teacher marking (Phase 3).
            record_attempt($uid, $question, null, $text, null);
            $feedback = [
                'correct'     => null,
                'your'        => $text,
                'right'       => '',
                'explanation' => $question['explanation'] ?: 'Saved. This answer type is marked by AI / teacher (Snap & Check, Phase 3).',
            ];
        }
    }
}

// Pick the next question for the subject (prefer ones not yet attempted).
$next = db_one(
    'SELECT q.* FROM questions q
     WHERE q.subject_id = ? AND q.status = "active"
       AND q.id NOT IN (SELECT question_id FROM question_attempts WHERE user_id = ?)
     ORDER BY RAND() LIMIT 1',
    [$subjectId, $uid]
);
if (!$next) {
    $next = db_one('SELECT * FROM questions WHERE subject_id = ? AND status = "active" ORDER BY RAND() LIMIT 1', [$subjectId]);
}
$options = $next ? db_all('SELECT * FROM question_options WHERE question_id = ? ORDER BY sort_order', [$next['id']]) : [];

student_layout_start('Practice', $user, 'practice.php');
?>
<div class="card">
  <form method="get" action="<?= url('student/practice.php') ?>" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <label class="muted">Subject:</label>
    <select name="subject_id" class="input" style="max-width:260px" onchange="this.form.submit()">
      <?php foreach ($subjects as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $s['id'] == $subjectId ? 'selected' : '' ?>><?= e($s['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<?php if ($feedback !== null): ?>
  <div class="card" style="margin-top:18px">
    <?php if ($feedback['correct'] === true): ?>
      <h3 style="color:var(--good)">Correct! 🎉 +10 XP</h3>
    <?php elseif ($feedback['correct'] === false): ?>
      <h3 style="color:var(--bad)">Not quite.</h3>
      <p class="muted">Your answer: <?= e($feedback['your']) ?></p>
      <p>Correct answer: <strong><?= e($feedback['right']) ?></strong></p>
    <?php else: ?>
      <h3>Answer saved ✅ +3 XP</h3>
    <?php endif; ?>
    <?php if ($feedback['explanation']): ?>
      <p class="muted"><strong>Explanation:</strong> <?= e($feedback['explanation']) ?></p>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="card" style="margin-top:18px">
  <?php if ($next): ?>
    <p class="muted">
      <span class="badge"><?= e(ucfirst($next['type'])) ?></span>
      <span class="badge badge--warn"><?= e(ucfirst($next['difficulty'])) ?></span>
      <span class="badge"><?= (int)$next['marks'] ?> mark(s)</span>
    </p>
    <h3><?= e($next['question_text']) ?></h3>
    <?php if (!empty($next['image_path'])): ?><img src="<?= url($next['image_path']) ?>" alt="question image"><?php endif; ?>
    <form method="post" action="<?= url('student/practice.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="question_id" value="<?= (int)$next['id'] ?>">
      <?php if ($next['type'] === 'mcq' && $options): ?>
        <?php foreach ($options as $o): ?>
          <label class="field" style="display:flex;gap:10px;align-items:center;cursor:pointer">
            <input type="radio" name="option_id" value="<?= (int)$o['id'] ?>" required>
            <span><strong><?= e($o['label']) ?>.</strong> <?= e($o['option_text']) ?></span>
          </label>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="field"><label>Your answer</label><textarea name="answer_text" required></textarea></div>
      <?php endif; ?>
      <button class="btn" type="submit">Submit answer</button>
    </form>
  <?php else: ?>
    <p class="muted">No questions available for this subject yet. Check back soon!</p>
  <?php endif; ?>
</div>
<?php
student_layout_end();
