<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin    = require_role('admin', 'creator');
$subjects = db_all('SELECT id, name FROM subjects ORDER BY sort_order, name');
$topics   = db_all('SELECT t.id, t.name, t.subject_id, t.form_level FROM topics t ORDER BY t.subject_id, t.form_level, t.sort_order, t.name');
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
    } elseif ($action === 'approve') {
        db_exec("UPDATE questions SET status = 'active' WHERE id = ?", [input_int('id')]);
        flash('success', 'Question approved and is now live.');
    } elseif ($action === 'approve_bulk') {
        $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
        if ($ids) {
            $place = implode(',', array_fill(0, count($ids), '?'));
            db_exec("UPDATE questions SET status = 'active' WHERE id IN ($place)", $ids);
            flash('success', count($ids) . ' question(s) approved.');
        }
    }
    redirect('admin/questions.php');
}

$qSearch     = trim(input('q'));
$qSubjectId  = input_int('subject');
$qTopicId    = input_int('topic');
$qFormLevel  = input_int('form');
$qStatus     = in_array(input('status'), ['active', 'pending', 'draft'], true) ? input('status') : '';
$qType       = in_array(input('type_filter'), ['mcq','short','essay','calculation','structured','image'], true) ? input('type_filter') : '';
$qPage       = max(1, input_int('page', 1));
$qPerPage    = max(20, min(200, input_int('per_page', 50)));
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
if ($qFormLevel === 4 || $qFormLevel === 5) {
    $qWhere[]  = 't.form_level = ?';
    $qParams[] = $qFormLevel;
}
if ($qStatus !== '') {
    $qWhere[]  = 'q.status = ?';
    $qParams[] = $qStatus;
}
if ($qType !== '') {
    $qWhere[]  = 'q.type = ?';
    $qParams[] = $qType;
}
$qJoin     = "FROM questions q
              JOIN subjects s ON s.id = q.subject_id
              LEFT JOIN topics t ON t.id = q.topic_id";
$qWhereSql = $qWhere ? 'WHERE ' . implode(' AND ', $qWhere) : '';

$qTotal = (int) (db_one("SELECT COUNT(*) c $qJoin $qWhereSql", $qParams)['c'] ?? 0);
$qPages = max(1, (int) ceil($qTotal / $qPerPage));
if ($qPage > $qPages) { $qPage = $qPages; $qOffset = ($qPage - 1) * $qPerPage; }

$rows = db_all(
    "SELECT q.*, s.name AS subject, t.name AS topic, t.form_level
     $qJoin
     $qWhereSql
     ORDER BY q.id DESC
     LIMIT $qPerPage OFFSET $qOffset",
    $qParams
);

// Status counts for the pill buttons.
$statusCounts = [];
foreach (db_all("SELECT status, COUNT(*) c FROM questions GROUP BY status") as $row) {
    $statusCounts[$row['status']] = (int) $row['c'];
}
$totalAll = array_sum($statusCounts);

// Per-subject jump chips (respect status/form filters but not subject/topic).
$scWhere = [];
$scParams = [];
if ($qStatus !== '') { $scWhere[] = 'q.status = ?'; $scParams[] = $qStatus; }
if ($qFormLevel === 4 || $qFormLevel === 5) { $scWhere[] = 't.form_level = ?'; $scParams[] = $qFormLevel; }
if ($qType !== '') { $scWhere[] = 'q.type = ?'; $scParams[] = $qType; }
$scWhereSql = $scWhere ? ' AND ' . implode(' AND ', $scWhere) : '';
$subjectCounts = db_all(
    "SELECT s.id, s.name, COUNT(q.id) AS qcount
     FROM subjects s
     LEFT JOIN questions q ON q.subject_id = s.id
     LEFT JOIN topics t ON t.id = q.topic_id
     WHERE 1=1 $scWhereSql
     GROUP BY s.id, s.name, s.sort_order
     HAVING qcount > 0
     ORDER BY s.sort_order, s.name",
    $scParams
);

function questions_link(array $overrides = []): string
{
    global $qSearch, $qSubjectId, $qTopicId, $qFormLevel, $qStatus, $qType, $qPage, $qPerPage;
    $args = array_merge([
        'q' => $qSearch, 'subject' => $qSubjectId, 'topic' => $qTopicId,
        'form' => $qFormLevel, 'status' => $qStatus, 'type_filter' => $qType,
        'page' => $qPage, 'per_page' => $qPerPage,
    ], $overrides);
    $defaults = ['q' => '', 'subject' => 0, 'topic' => 0, 'form' => 0, 'status' => '', 'type_filter' => '', 'page' => 1, 'per_page' => 50];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('admin/questions.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Questions', $admin, 'questions.php');
?>
<details class="card" style="margin-bottom:18px">
  <summary style="cursor:pointer;font-weight:600;font-size:15px">+ Add new question</summary>
  <form method="post" style="margin-top:14px">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="grid grid--3">
      <div class="field"><label>Subject *</label>
        <select name="subject_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Topic (optional)</label>
        <select name="topic_id" class="input">
          <option value="">-- none --</option>
          <?php foreach ($topics as $t): $form = $t['form_level'] ? " (T{$t['form_level']})" : ''; ?>
            <option value="<?= (int)$t['id'] ?>"><?= e($t['name'] . $form) ?></option>
          <?php endforeach; ?>
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
    <div class="field"><label>Question text *</label><textarea name="question_text" required rows="3"></textarea></div>
    <div id="mcqOptions">
      <label class="muted">MCQ options (select the correct one)</label>
      <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="field" style="display:flex;gap:10px;align-items:center">
          <input type="radio" name="correct_option" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?>>
          <input class="input" name="option_text[]" placeholder="Option <?= chr(65 + $i) ?>">
        </div>
      <?php endfor; ?>
    </div>
    <div class="field"><label>Explanation</label><textarea name="explanation" rows="2"></textarea></div>
    <button class="btn">Create question</button>
  </form>
</details>

<?php if ($subjectCounts): ?>
<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Jump to subject</h4>
  <div style="display:flex;flex-wrap:wrap;gap:8px">
    <a class="chip" href="<?= e(questions_link(['subject' => 0, 'topic' => 0, 'page' => 1])) ?>"
       style="<?= $qSubjectId === 0 && $qTopicId === 0 ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
       All <span class="muted" style="margin-left:4px">· <?= (int)array_sum(array_column($subjectCounts, 'qcount')) ?></span>
    </a>
    <?php foreach ($subjectCounts as $sc): ?>
      <a class="chip" href="<?= e(questions_link(['subject' => (int)$sc['id'], 'topic' => 0, 'page' => 1])) ?>"
         style="<?= $qSubjectId === (int)$sc['id'] ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
         <?= e($sc['name']) ?>
         <span class="muted" style="margin-left:4px">· <?= (int)$sc['qcount'] ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">Questions <span class="muted" style="font-size:14px">(<?= $qTotal ?>)</span></h3>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $qStatus === '' ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['status' => '', 'page' => 1])) ?>">All <span class="muted" style="font-size:11px;margin-left:4px">· <?= $totalAll ?></span></a>
      <a class="btn btn--sm <?= $qStatus === 'active' ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['status' => 'active', 'page' => 1])) ?>">Active <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int)($statusCounts['active'] ?? 0) ?></span></a>
      <a class="btn btn--sm <?= $qStatus === 'pending' ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['status' => 'pending', 'page' => 1])) ?>">Pending <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int)($statusCounts['pending'] ?? 0) ?></span></a>
      <a class="btn btn--sm <?= $qStatus === 'draft' ? '' : 'btn--ghost' ?>" href="<?= e(questions_link(['status' => 'draft', 'page' => 1])) ?>">Draft <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int)($statusCounts['draft'] ?? 0) ?></span></a>
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
      <?php foreach ($topics as $t):
        if ($qSubjectId > 0 && (int)$t['subject_id'] !== $qSubjectId) continue;
        $form = $t['form_level'] ? " (T{$t['form_level']})" : '';
      ?>
        <option value="<?= (int)$t['id'] ?>" <?= $qTopicId === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['name'] . $form) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="form" class="input" style="max-width:140px" onchange="this.form.submit()">
      <option value="">All forms</option>
      <option value="4" <?= $qFormLevel === 4 ? 'selected' : '' ?>>Form 4</option>
      <option value="5" <?= $qFormLevel === 5 ? 'selected' : '' ?>>Form 5</option>
    </select>
    <select name="type_filter" class="input" style="max-width:160px" onchange="this.form.submit()">
      <option value="">All types</option>
      <?php foreach (['mcq', 'short', 'essay', 'calculation', 'structured', 'image'] as $tp): ?>
        <option value="<?= $tp ?>" <?= $qType === $tp ? 'selected' : '' ?>><?= $tp ?></option>
      <?php endforeach; ?>
    </select>
    <?php if ($qStatus !== ''): ?><input type="hidden" name="status" value="<?= e($qStatus) ?>"><?php endif; ?>
    <input class="input" name="q" placeholder="Search question text" value="<?= e($qSearch) ?>" style="flex:1;min-width:180px">
    <select name="per_page" class="input" style="max-width:130px" onchange="this.form.submit()">
      <?php foreach ([20, 50, 100, 200] as $pp): ?>
        <option value="<?= $pp ?>" <?= $qPerPage === $pp ? 'selected' : '' ?>><?= $pp ?> / page</option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--sm">Search</button>
    <?php if ($qSearch !== '' || $qSubjectId > 0 || $qTopicId > 0 || $qFormLevel > 0 || $qStatus !== '' || $qType !== ''): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(questions_link(['q' => '', 'subject' => 0, 'topic' => 0, 'form' => 0, 'status' => '', 'type_filter' => '', 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php if (!$rows): ?>
    <p class="muted">No questions match these filters.</p>
  <?php else: ?>
    <?php if ($qStatus === 'pending'): ?>
      <form method="post" id="bulkApproveForm" style="margin-bottom:10px">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="approve_bulk">
        <button class="btn btn--sm" onclick="return confirm('Approve all checked questions?')">Approve checked</button>
        <a href="#" class="btn btn--sm btn--ghost" onclick="document.querySelectorAll('.q-check').forEach(c=>c.checked=true);return false;">Check all</a>
        <a href="#" class="btn btn--sm btn--ghost" onclick="document.querySelectorAll('.q-check').forEach(c=>c.checked=false);return false;">Clear</a>
      </form>
    <?php endif; ?>

    <div class="q-list">
      <?php foreach ($rows as $q):
        $statusClass = $q['status'] === 'active' ? 'badge--good' : ($q['status'] === 'pending' ? 'badge--warn' : '');
      ?>
        <div class="q-row q-row--<?= e($q['status']) ?>">
          <?php if ($qStatus === 'pending'): ?>
            <input type="checkbox" form="bulkApproveForm" name="ids[]" value="<?= (int)$q['id'] ?>" class="q-check">
          <?php endif; ?>
          <div class="q-row__main">
            <div class="q-row__id muted">#<?= (int)$q['id'] ?></div>
            <div class="q-row__text"><?= e(mb_substr($q['question_text'], 0, 200)) ?><?= mb_strlen($q['question_text']) > 200 ? '…' : '' ?></div>
            <div class="q-row__meta muted">
              <strong><?= e($q['subject']) ?></strong>
              <?php if ($q['topic']): ?> · <?= e($q['topic']) ?><?php endif; ?>
              <?php if ($q['form_level']): ?> · <span class="badge" style="padding:1px 7px;font-size:10px">T<?= (int)$q['form_level'] ?></span><?php endif; ?>
              · <?= e($q['difficulty']) ?>
              · <?= (int)$q['marks'] ?>m
            </div>
          </div>
          <div class="q-row__badges">
            <span class="badge"><?= e($q['type']) ?></span>
            <span class="badge <?= $statusClass ?>"><?= e($q['status']) ?></span>
          </div>
          <div class="q-row__actions">
            <?php if ($q['status'] === 'pending'): ?>
              <form method="post" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="id" value="<?= (int)$q['id'] ?>">
                <button class="btn btn--sm">Approve</button>
              </form>
            <?php endif; ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete this question?')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$q['id'] ?>">
              <button class="btn btn--sm btn--danger">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($qPages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $qPage ?> of <?= $qPages ?> · <?= $qTotal ?> total</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
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

<style>
.q-list { display:flex; flex-direction:column; gap:6px; }
.q-row {
  display:flex; align-items:flex-start; gap:14px;
  padding:12px 14px;
  border:1px solid var(--border);
  border-radius:10px;
  background:var(--card-2);
}
.q-row:hover { border-color:var(--primary); }
.q-row--pending { border-left: 3px solid var(--warn); }
.q-row__main { flex:1; min-width:200px; }
.q-row__id { font-size:11px; }
.q-row__text { font-size:14px; line-height:1.5; margin-top:3px; }
.q-row__meta { font-size:12px; margin-top:6px; }
.q-row__badges { display:flex; flex-direction:column; gap:4px; min-width:80px; align-items:flex-end; }
.q-row__actions { display:flex; gap:6px; flex-wrap:wrap; align-self:center; }
.q-check { margin-top:6px; }
@media (max-width: 720px) {
  .q-row { flex-wrap:wrap; }
  .q-row__badges { flex-direction:row; align-items:center; min-width:auto; }
  .q-row__actions { width:100%; justify-content:flex-end; }
}
</style>
<script>
  var typeSel = document.getElementById('typeSel');
  var mcq = document.getElementById('mcqOptions');
  if (typeSel && mcq) {
    typeSel.addEventListener('change', function () { mcq.style.display = typeSel.value === 'mcq' ? '' : 'none'; });
  }
</script>
<?php
admin_layout_end();
