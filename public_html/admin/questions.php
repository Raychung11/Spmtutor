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

$qSearch     = trim(input('q'));
$qSubjectId  = input_int('subject');
$qTopicId    = input_int('topic');
$qStatus     = in_array(input('status'), ['active', 'pending', 'draft'], true) ? input('status') : '';
$qPage       = max(1, input_int('page', 1));
$qPerPage    = 20;
$qOffset     = ($qPage - 1) * $qPerPage;

$qWhere  = [];
$qParams = [];
if ($qSearch !== '') {
    $qWhere[]  = 'q.question_text LIKE ?';
    $qParams[] = '%' . $qSearch . '%';
}
if ($qSubjectId > 0) {
    $qWhere[]  = 'q.subject_id = ?';
    $qParams[] = $qSubjectId;
}
if ($qTopicId > 0) {
    $qWhere[]  = 'q.topic_id = ?';
    $qParams[] = $qTopicId;
}
if ($qStatus !== '') {
    $qWhere[]  = 'q.status = ?';
    $qParams[] = $qStatus;
}
$qWhereSql = $qWhere ? 'WHERE ' . implode(' AND ', $qWhere) : '';

$qTotal = (int) (db_one("SELECT COUNT(*) c FROM questions q $qWhereSql", $qParams)['c'] ?? 0);
$qPages = max(1, (int) ceil($qTotal / $qPerPage));
if ($qPage > $qPages) { $qPage = $qPages; $qOffset = ($qPage - 1) * $qPerPage; }

$rows = db_all(
    "SELECT q.*, s.name AS subject, t.name AS topic FROM questions q
     JOIN subjects s ON s.id = q.subject_id LEFT JOIN topics t ON t.id = q.topic_id
     $qWhereSql
     ORDER BY q.id DESC
     LIMIT $qPerPage OFFSET $qOffset",
    $qParams
);

function questions_link(array $overrides = []): string
{
    global $qSearch, $qSubjectId, $qTopicId, $qStatus, $qPage;
    $args = array_merge(['q' => $qSearch, 'subject' => $qSubjectId, 'topic' => $qTopicId, 'status' => $qStatus, 'page' => $qPage], $overrides);
    $args = array_filter($args, fn($v) => $v !== '' && $v !== null && $v !== 0);
    return url('admin/questions.php' . ($args ? '?' . http_build_query($args) : ''));
}

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
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">Questions <span class="muted" style="font-size:14px">(<?= $qTotal ?>)</span></h3>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $qStatus === '' ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['status' => '', 'page' => 1])) ?>">All</a>
      <a class="btn btn--sm <?= $qStatus === 'active' ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['status' => 'active', 'page' => 1])) ?>">Active</a>
      <a class="btn btn--sm <?= $qStatus === 'pending' ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['status' => 'pending', 'page' => 1])) ?>">Pending</a>
      <a class="btn btn--sm <?= $qStatus === 'draft' ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['status' => 'draft', 'page' => 1])) ?>">Draft</a>
    </div>
  </div>
  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <select name="subject" class="input" style="max-width:200px" onchange="this.form.submit()">
      <option value="">All subjects</option>
      <?php foreach ($subjects as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $qSubjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="topic" class="input" style="max-width:220px" onchange="this.form.submit()">
      <option value="">All topics</option>
      <?php foreach ($topics as $t): if ($qSubjectId > 0 && (int)$t['subject_id'] !== $qSubjectId) continue; ?>
        <option value="<?= (int)$t['id'] ?>" <?= $qTopicId === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <?php if ($qStatus !== ''): ?><input type="hidden" name="status" value="<?= e($qStatus) ?>"><?php endif; ?>
    <input class="input" name="q" placeholder="Search question text" value="<?= e($qSearch) ?>" style="flex:1;min-width:180px">
    <button class="btn btn--sm">Search</button>
    <?php if ($qSearch !== '' || $qSubjectId > 0 || $qTopicId > 0 || $qStatus !== ''): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(questions_link(['q' => '', 'subject' => 0, 'topic' => 0, 'status' => '', 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>
  <table class="table"><thead><tr><th>#</th><th>Question</th><th>Subject / Topic</th><th>Type</th><th>Status</th><th></th></tr></thead><tbody>
  <?php foreach ($rows as $q): ?>
    <tr>
      <td><?= (int)$q['id'] ?></td>
      <td><?= e(mb_substr($q['question_text'], 0, 80)) ?></td>
      <td class="muted"><?= e($q['subject']) ?><?= $q['topic'] ? '<br><span style="font-size:12px">' . e($q['topic']) . '</span>' : '' ?></td>
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
  <?php if (!$rows): ?><tr><td colspan="6" class="muted">No questions match these filters.</td></tr><?php endif; ?>
  </tbody></table>

  <?php if ($qPages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $qPage ?> of <?= $qPages ?> · <?= $qTotal ?> total</span>
      <div style="display:flex;gap:6px">
        <?php if ($qPage > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(questions_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(questions_link(['page' => $qPage - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($p = max(1, $qPage - 2); $p <= min($qPages, $qPage + 2); $p++): ?>
          <a class="btn btn--sm <?= $p === $qPage ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['page' => $p])) ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($qPage < $qPages): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(questions_link(['page' => $qPage + 1])) ?>">Next ›</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(questions_link(['page' => $qPages])) ?>">Last »</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
<script>
  var typeSel = document.getElementById('typeSel');
  var mcq = document.getElementById('mcqOptions');
  typeSel.addEventListener('change', function () { mcq.style.display = typeSel.value === 'mcq' ? '' : 'none'; });
</script>
<?php
admin_layout_end();
