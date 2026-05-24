<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin    = require_role('admin', 'creator');
$subjects = db_all('SELECT id, name FROM subjects ORDER BY name');
$topics   = db_all('SELECT id, name, subject_id FROM topics ORDER BY name');
$skills   = db_all('SELECT id, name, topic_id FROM skills ORDER BY name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');
    if ($action === 'create') {
        $subject = input_int('subject_id');
        $topic   = input_int('topic_id') ?: null;
        $skill   = input_int('skill_id') ?: null;
        $type    = in_array(input('type'), ['mcq','short','essay','calculation','structured','image'], true) ? input('type') : 'mcq';
        $diff    = in_array(input('difficulty'), ['easy','medium','hard'], true) ? input('difficulty') : 'medium';
        $text    = input('question_text');
        $expl    = input('explanation');
        $marks   = max(1, input_int('marks', 1));

        if ($subject && $text !== '') {
            $qid = db_exec(
                'INSERT INTO questions (subject_id, topic_id, skill_id, type, difficulty, question_text, explanation, marks, created_by, status)
                 VALUES (?,?,?,?,?,?,?,?,?,?)',
                [$subject, $topic, $skill, $type, $diff, $text, $expl, $marks, (int)$admin['id'], $admin['role'] === 'creator' ? 'pending' : 'active']
            );
            if ($type === 'mcq') {
                $opts    = $_POST['option_text'] ?? [];
                $correct = input_int('correct_option');
                foreach (array_values($opts) as $i => $ot) {
                    $ot = trim((string) $ot);
                    if ($ot === '') {
                        continue;
                    }
                    $label = chr(65 + $i); // A, B, C...
                    db_exec(
                        'INSERT INTO question_options (question_id, label, option_text, is_correct, sort_order) VALUES (?,?,?,?,?)',
                        [$qid, $label, $ot, $i === $correct ? 1 : 0, $i + 1]
                    );
                }
            }
            flash('success', 'Question created.');
        } else {
            flash('error', 'Subject and question text are required.');
        }
    } elseif ($action === 'delete') {
        db_exec('DELETE FROM questions WHERE id = ?', [input_int('id')]);
        flash('success', 'Question deleted.');
    }
    redirect('admin/questions.php');
}

$rows = db_all(
    'SELECT q.*, s.name AS subject, t.name AS topic FROM questions q
     JOIN subjects s ON s.id = q.subject_id LEFT JOIN topics t ON t.id = q.topic_id
     ORDER BY q.id DESC LIMIT 100'
);

admin_layout_start('Questions', $admin, 'questions.php');
?>
<div class="card">
  <h3>Add question</h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="grid grid--3">
      <div class="field"><label>Subject</label>
        <select name="subject_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Topic (optional)</label>
        <select name="topic_id" class="input">
          <option value="">-- none --</option>
          <?php foreach ($topics as $t): ?><option value="<?= (int)$t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Skill (optional)</label>
        <select name="skill_id" class="input">
          <option value="">-- none --</option>
          <?php foreach ($skills as $sk): ?><option value="<?= (int)$sk['id'] ?>"><?= e($sk['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="grid grid--3">
      <div class="field"><label>Type</label>
        <select name="type" class="input" id="typeSel">
          <option value="mcq">MCQ</option><option value="short">Short answer</option>
          <option value="essay">Essay</option><option value="calculation">Calculation</option>
          <option value="structured">Structured</option><option value="image">Image-based</option>
        </select>
      </div>
      <div class="field"><label>Difficulty</label>
        <select name="difficulty" class="input"><option>easy</option><option selected>medium</option><option>hard</option></select>
      </div>
      <div class="field"><label>Marks</label><input class="input" type="number" name="marks" value="1" min="1"></div>
    </div>
    <div class="field"><label>Question text</label><textarea name="question_text" required></textarea></div>
    <div id="mcqOptions">
      <label class="muted">MCQ options (select the correct one)</label>
      <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="field" style="display:flex;gap:10px;align-items:center">
          <input type="radio" name="correct_option" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?>>
          <input class="input" name="option_text[]" placeholder="Option <?= chr(65 + $i) ?>">
        </div>
      <?php endfor; ?>
    </div>
    <div class="field"><label>Explanation</label><textarea name="explanation"></textarea></div>
    <button class="btn">Create question</button>
  </form>
</div>

<div class="card" style="margin-top:18px">
  <h3>Recent questions</h3>
  <table class="table"><thead><tr><th>#</th><th>Question</th><th>Subject</th><th>Type</th><th>Status</th><th></th></tr></thead><tbody>
  <?php foreach ($rows as $q): ?>
    <tr>
      <td><?= (int)$q['id'] ?></td>
      <td><?= e(mb_substr($q['question_text'], 0, 60)) ?></td>
      <td class="muted"><?= e($q['subject']) ?></td>
      <td><span class="badge"><?= e($q['type']) ?></span></td>
      <td><span class="badge <?= $q['status'] === 'active' ? 'badge--good' : 'badge--warn' ?>"><?= e($q['status']) ?></span></td>
      <td>
        <form method="post" onsubmit="return confirm('Delete this question?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$q['id'] ?>">
          <button class="btn btn--sm btn--danger">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody></table>
</div>
<script>
  var typeSel = document.getElementById('typeSel');
  var mcq = document.getElementById('mcqOptions');
  typeSel.addEventListener('change', function () { mcq.style.display = typeSel.value === 'mcq' ? '' : 'none'; });
</script>
<?php
admin_layout_end();
