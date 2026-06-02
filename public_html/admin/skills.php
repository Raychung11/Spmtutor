<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin    = require_role('admin');
$subjects = db_all('SELECT id, name FROM subjects ORDER BY sort_order, name');
$topics   = db_all('SELECT t.id, t.name, t.subject_id, t.form_level, s.name AS subject FROM topics t JOIN subjects s ON s.id = t.subject_id ORDER BY s.sort_order, s.name, t.form_level, t.name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');
    if ($action === 'create' || $action === 'update') {
        $name  = input('name');
        $topic = input_int('topic_id');
        $diff  = in_array(input('difficulty'), ['easy', 'medium', 'hard'], true) ? input('difficulty') : 'medium';
        $desc  = input('description');
        if ($name === '' || $topic === 0) {
            flash('error', 'Topic and name are required.');
        } elseif ($action === 'create') {
            db_exec('INSERT INTO skills (topic_id, name, difficulty, description) VALUES (?,?,?,?)', [$topic, $name, $diff, $desc]);
            flash('success', 'Skill created.');
        } else {
            db_exec('UPDATE skills SET topic_id = ?, name = ?, difficulty = ?, description = ? WHERE id = ?', [$topic, $name, $diff, $desc, input_int('id')]);
            flash('success', 'Skill updated.');
        }
    } elseif ($action === 'delete') {
        db_exec('DELETE FROM skills WHERE id = ?', [input_int('id')]);
        flash('success', 'Skill deleted.');
    }
    redirect('admin/skills.php');
}

$editId = input_int('edit');
$edit   = $editId ? db_one('SELECT * FROM skills WHERE id = ?', [$editId]) : null;

$q          = trim(input('q'));
$subjectId  = input_int('subject');
$topicId    = input_int('topic');
$formLevel  = input_int('form');
$difficulty = input('difficulty_filter');
$difficulty = in_array($difficulty, ['easy', 'medium', 'hard'], true) ? $difficulty : '';
$page       = max(1, input_int('page', 1));
$perPage    = max(20, min(200, input_int('per_page', 50)));
$offset     = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($q !== '') {
    $where[]  = '(sk.name LIKE ? OR sk.description LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($topicId > 0) {
    $where[]  = 'sk.topic_id = ?';
    $params[] = $topicId;
} elseif ($subjectId > 0) {
    $where[]  = 't.subject_id = ?';
    $params[] = $subjectId;
}
if ($formLevel === 4 || $formLevel === 5) {
    $where[]  = 't.form_level = ?';
    $params[] = $formLevel;
}
if ($difficulty !== '') {
    $where[]  = 'sk.difficulty = ?';
    $params[] = $difficulty;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int) (db_one("SELECT COUNT(*) c FROM skills sk JOIN topics t ON t.id = sk.topic_id $whereSql", $params)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$rows = db_all(
    "SELECT sk.*, t.name AS topic, t.form_level, t.subject_id, s.name AS subject
     FROM skills sk JOIN topics t ON t.id = sk.topic_id JOIN subjects s ON s.id = t.subject_id
     $whereSql
     ORDER BY s.sort_order, s.name, t.form_level, t.sort_order, sk.id
     LIMIT $perPage OFFSET $offset",
    $params
);

// Per-subject counts for jump chips (respects current form/difficulty/search filters).
$scWhere = [];
$scParams = [];
if ($q !== '') {
    $scWhere[] = '(sk.name LIKE ? OR sk.description LIKE ?)';
    $scParams[] = '%' . $q . '%';
    $scParams[] = '%' . $q . '%';
}
if ($formLevel === 4 || $formLevel === 5) {
    $scWhere[] = 't.form_level = ?';
    $scParams[] = $formLevel;
}
if ($difficulty !== '') {
    $scWhere[] = 'sk.difficulty = ?';
    $scParams[] = $difficulty;
}
$scWhereSql = $scWhere ? ' AND ' . implode(' AND ', $scWhere) : '';
$subjectCounts = db_all(
    "SELECT s.id, s.name, COUNT(sk.id) AS skill_count
     FROM subjects s
     LEFT JOIN topics t ON t.subject_id = s.id
     LEFT JOIN skills sk ON sk.topic_id = t.id
     WHERE 1=1 $scWhereSql
     GROUP BY s.id, s.name, s.sort_order
     HAVING skill_count > 0
     ORDER BY s.sort_order, s.name",
    $scParams
);

function skills_link(array $overrides = []): string
{
    global $q, $subjectId, $topicId, $formLevel, $difficulty, $page, $perPage;
    $args = array_merge([
        'q' => $q, 'subject' => $subjectId, 'topic' => $topicId,
        'form' => $formLevel, 'difficulty_filter' => $difficulty,
        'page' => $page, 'per_page' => $perPage,
    ], $overrides);
    $defaults = ['q' => '', 'subject' => 0, 'topic' => 0, 'form' => 0, 'difficulty_filter' => '', 'page' => 1, 'per_page' => 50];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('admin/skills.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Skills', $admin, 'skills.php');
?>
<?php if ($edit): ?>
<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0">Edit skill</h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    <div class="grid grid--3">
      <div class="field"><label>Topic (subject / topic) *</label>
        <select name="topic_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($topics as $t): $form = $t['form_level'] ? " (T{$t['form_level']})" : ''; ?>
            <option value="<?= (int)$t['id'] ?>" <?= (int)($edit['topic_id'] ?? 0) === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['subject'] . ' / ' . $t['name'] . $form) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Skill name *</label><input class="input" name="name" value="<?= e($edit['name'] ?? '') ?>" required></div>
      <div class="field"><label>Difficulty</label>
        <select name="difficulty" class="input">
          <option value="easy"   <?= ($edit['difficulty'] ?? '') === 'easy'   ? 'selected' : '' ?>>easy</option>
          <option value="medium" <?= ($edit['difficulty'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>medium</option>
          <option value="hard"   <?= ($edit['difficulty'] ?? '') === 'hard'   ? 'selected' : '' ?>>hard</option>
        </select>
      </div>
    </div>
    <div class="field"><label>Description</label><textarea name="description" rows="2"><?= e($edit['description'] ?? '') ?></textarea></div>
    <button class="btn">Save changes</button>
    <a class="btn btn--ghost" href="<?= e(skills_link(['edit' => null])) ?>">Cancel</a>
  </form>
</div>
<?php else: ?>
<details class="card" style="margin-bottom:18px">
  <summary style="cursor:pointer;font-weight:600;font-size:15px">+ Add new skill</summary>
  <form method="post" style="margin-top:14px">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="grid grid--3">
      <div class="field"><label>Topic (subject / topic) *</label>
        <select name="topic_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($topics as $t): $form = $t['form_level'] ? " (T{$t['form_level']})" : ''; ?>
            <option value="<?= (int)$t['id'] ?>" <?= $topicId === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['subject'] . ' / ' . $t['name'] . $form) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Skill name *</label><input class="input" name="name" required></div>
      <div class="field"><label>Difficulty</label>
        <select name="difficulty" class="input">
          <option value="easy">easy</option><option value="medium" selected>medium</option><option value="hard">hard</option>
        </select>
      </div>
    </div>
    <div class="field"><label>Description</label><textarea name="description" rows="2"></textarea></div>
    <button class="btn">Create skill</button>
  </form>
</details>
<?php endif; ?>

<?php if (!$edit && $subjectCounts): ?>
<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Jump to subject</h4>
  <div style="display:flex;flex-wrap:wrap;gap:8px">
    <a class="chip" href="<?= e(skills_link(['subject' => 0, 'topic' => 0, 'page' => 1])) ?>"
       style="<?= $subjectId === 0 && $topicId === 0 ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
       All <span class="muted" style="margin-left:4px">· <?= (int)array_sum(array_column($subjectCounts, 'skill_count')) ?></span>
    </a>
    <?php foreach ($subjectCounts as $sc): ?>
      <a class="chip" href="<?= e(skills_link(['subject' => (int)$sc['id'], 'topic' => 0, 'page' => 1])) ?>"
         style="<?= $subjectId === (int)$sc['id'] ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
         <?= e($sc['name']) ?>
         <span class="muted" style="margin-left:4px">· <?= (int)$sc['skill_count'] ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">Skills <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?></span>
  </div>

  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <select name="subject" class="input" style="max-width:220px" onchange="this.form.submit()">
      <option value="">All subjects</option>
      <?php foreach ($subjects as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $subjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="topic" class="input" style="max-width:240px" onchange="this.form.submit()">
      <option value="">All topics</option>
      <?php foreach ($topics as $t):
        if ($subjectId > 0 && (int)$t['subject_id'] !== $subjectId) continue;
        $form = $t['form_level'] ? " (T{$t['form_level']})" : '';
      ?>
        <option value="<?= (int)$t['id'] ?>" <?= $topicId === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['subject'] . ' / ' . $t['name'] . $form) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="form" class="input" style="max-width:140px" onchange="this.form.submit()">
      <option value="">All forms</option>
      <option value="4" <?= $formLevel === 4 ? 'selected' : '' ?>>Form 4 only</option>
      <option value="5" <?= $formLevel === 5 ? 'selected' : '' ?>>Form 5 only</option>
    </select>
    <select name="difficulty_filter" class="input" style="max-width:140px" onchange="this.form.submit()">
      <option value="">All difficulty</option>
      <option value="easy"   <?= $difficulty === 'easy'   ? 'selected' : '' ?>>Easy</option>
      <option value="medium" <?= $difficulty === 'medium' ? 'selected' : '' ?>>Medium</option>
      <option value="hard"   <?= $difficulty === 'hard'   ? 'selected' : '' ?>>Hard</option>
    </select>
    <input class="input" name="q" placeholder="Search name or description" value="<?= e($q) ?>" style="flex:1;min-width:200px">
    <select name="per_page" class="input" style="max-width:130px" onchange="this.form.submit()">
      <?php foreach ([20, 50, 100, 200] as $pp): ?>
        <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?> / page</option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $subjectId > 0 || $topicId > 0 || $formLevel > 0 || $difficulty !== ''): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(skills_link(['q' => '', 'subject' => 0, 'topic' => 0, 'form' => 0, 'difficulty_filter' => '', 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php
  // Group by subject + topic within the current page.
  $grouped = [];
  foreach ($rows as $sk) {
      $heading = $sk['subject'] . ($sk['form_level'] ? " (T{$sk['form_level']})" : '') . ' / ' . $sk['topic'];
      $grouped[$heading][] = $sk;
  }
  ?>
  <?php foreach ($grouped as $heading => $items): ?>
    <div style="display:flex;align-items:baseline;gap:10px;margin:18px 0 8px">
      <h4 style="margin:0;color:var(--accent);font-size:13px;text-transform:uppercase;letter-spacing:.08em"><?= e($heading) ?></h4>
      <span class="muted" style="font-size:12px">· <?= count($items) ?> on this page</span>
    </div>
    <div class="skills-list">
      <?php foreach ($items as $sk):
        $diff = $sk['difficulty'];
        $diffClass = $diff === 'easy' ? 'badge--good' : ($diff === 'hard' ? 'badge--bad' : 'badge--warn');
      ?>
        <div class="skill-row">
          <div class="skill-row__main">
            <div class="skill-row__name"><?= e($sk['name']) ?></div>
            <?php if (!empty($sk['description'])): ?>
              <div class="skill-row__desc muted"><?= e(mb_substr((string)$sk['description'], 0, 160)) ?></div>
            <?php endif; ?>
          </div>
          <div class="skill-row__diff">
            <span class="badge <?= $diffClass ?>"><?= e($diff) ?></span>
          </div>
          <div class="skill-row__actions">
            <a class="btn btn--sm btn--ghost" href="<?= e(skills_link(['edit' => (int)$sk['id']])) ?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete this skill?')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$sk['id'] ?>">
              <button class="btn btn--sm btn--danger">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
  <?php if (!$rows): ?><p class="muted">No skills match these filters.</p><?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(skills_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(skills_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
          <a class="btn btn--sm <?= $p === $page ? '' : 'btn--ghost' ?>" href="<?= e(skills_link(['page' => $p])) ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(skills_link(['page' => $page + 1])) ?>">Next ›</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(skills_link(['page' => $pages])) ?>">Last »</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
.skills-list { display:flex; flex-direction:column; gap:6px; }
.skill-row {
  display:flex; align-items:center; gap:14px;
  padding:10px 14px;
  border:1px solid var(--border);
  border-radius:10px;
  background:var(--card-2);
}
.skill-row:hover { border-color:var(--primary); }
.skill-row__main { flex:1; min-width:180px; }
.skill-row__name { font-weight:600; font-size:14px; }
.skill-row__desc { font-size:12px; margin-top:3px; line-height:1.4; }
.skill-row__diff { width:80px; text-align:center; }
.skill-row__actions { display:flex; gap:6px; flex-wrap:wrap; }
@media (max-width: 720px) {
  .skill-row { flex-wrap:wrap; gap:10px; }
  .skill-row__diff { width:auto; }
  .skill-row__actions { width:100%; justify-content:flex-end; }
}
</style>
<?php
admin_layout_end();
