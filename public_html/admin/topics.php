<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin    = require_role('admin');
$subjects = db_all('SELECT id, name FROM subjects ORDER BY sort_order, name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');
    if ($action === 'create' || $action === 'update') {
        $name    = input('name');
        $subject = input_int('subject_id');
        $form    = input_int('form_level');
        $form    = in_array($form, [4, 5], true) ? $form : null;
        $desc    = input('description');
        $slug    = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        if ($name === '' || $subject === 0) {
            flash('error', 'Subject and name are required.');
        } elseif ($action === 'create') {
            db_exec('INSERT INTO topics (subject_id, form_level, name, slug, description) VALUES (?,?,?,?,?)', [$subject, $form, $name, $slug, $desc]);
            flash('success', 'Topic created.');
        } else {
            db_exec('UPDATE topics SET subject_id = ?, form_level = ?, name = ?, description = ? WHERE id = ?', [$subject, $form, $name, $desc, input_int('id')]);
            flash('success', 'Topic updated.');
        }
    } elseif ($action === 'delete') {
        db_exec('DELETE FROM topics WHERE id = ?', [input_int('id')]);
        flash('success', 'Topic deleted.');
    }
    redirect('admin/topics.php');
}

$editId = input_int('edit');
$edit   = $editId ? db_one('SELECT * FROM topics WHERE id = ?', [$editId]) : null;

$q          = trim(input('q'));
$subjectId  = input_int('subject');
$formLevel  = input_int('form');
$page       = max(1, input_int('page', 1));
$perPage    = max(20, min(200, input_int('per_page', 50)));
$offset     = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($q !== '') {
    $where[]  = '(t.name LIKE ? OR t.slug LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($subjectId > 0) {
    $where[]  = 't.subject_id = ?';
    $params[] = $subjectId;
}
if ($formLevel === 4 || $formLevel === 5) {
    $where[]  = 't.form_level = ?';
    $params[] = $formLevel;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int) (db_one("SELECT COUNT(*) c FROM topics t $whereSql", $params)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$rows = db_all(
    "SELECT t.*, s.name AS subject,
            (SELECT COUNT(*) FROM skills sk WHERE sk.topic_id = t.id) AS skill_count,
            (SELECT COUNT(*) FROM questions q WHERE q.topic_id = t.id) AS question_count
     FROM topics t JOIN subjects s ON s.id = t.subject_id
     $whereSql
     ORDER BY s.sort_order, s.name, t.form_level, t.sort_order, t.id
     LIMIT $perPage OFFSET $offset",
    $params
);

// Per-subject counts for the overview row (uses current form-level + search filters).
$subjectCountWhere = [];
$subjectCountParams = [];
if ($q !== '') {
    $subjectCountWhere[] = '(t.name LIKE ? OR t.slug LIKE ?)';
    $subjectCountParams[] = '%' . $q . '%';
    $subjectCountParams[] = '%' . $q . '%';
}
if ($formLevel === 4 || $formLevel === 5) {
    $subjectCountWhere[] = 't.form_level = ?';
    $subjectCountParams[] = $formLevel;
}
$scWhereSql = $subjectCountWhere ? 'WHERE ' . implode(' AND ', $subjectCountWhere) : '';
$subjectCounts = db_all(
    "SELECT s.id, s.name, COUNT(t.id) AS topic_count
     FROM subjects s LEFT JOIN topics t ON t.subject_id = s.id
     $scWhereSql
     GROUP BY s.id, s.name, s.sort_order
     HAVING topic_count > 0
     ORDER BY s.sort_order, s.name",
    $subjectCountParams
);

function topics_link(array $overrides = []): string
{
    global $q, $subjectId, $formLevel, $page, $perPage;
    $args = array_merge([
        'q'        => $q,
        'subject'  => $subjectId,
        'form'     => $formLevel,
        'page'     => $page,
        'per_page' => $perPage,
    ], $overrides);
    // Drop empty/default values from the URL.
    $defaults = ['q' => '', 'subject' => 0, 'form' => 0, 'page' => 1, 'per_page' => 50];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('admin/topics.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Topics', $admin, 'topics.php');
?>
<?php if ($edit): ?>
<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0">Edit topic</h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    <div class="grid grid--2">
      <div class="field"><label>Subject *</label>
        <select name="subject_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($subjects as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= (int)($edit['subject_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Name *</label><input class="input" name="name" value="<?= e($edit['name'] ?? '') ?>" required></div>
    </div>
    <div class="grid grid--2">
      <div class="field"><label>Form level</label>
        <select name="form_level" class="input">
          <option value="">— (none) —</option>
          <option value="4" <?= (int)($edit['form_level'] ?? 0) === 4 ? 'selected' : '' ?>>Tingkatan / Form 4</option>
          <option value="5" <?= (int)($edit['form_level'] ?? 0) === 5 ? 'selected' : '' ?>>Tingkatan / Form 5</option>
        </select>
      </div>
      <div class="field"><label>Description</label><textarea name="description" rows="2"><?= e($edit['description'] ?? '') ?></textarea></div>
    </div>
    <button class="btn">Save changes</button>
    <a class="btn btn--ghost" href="<?= e(topics_link(['edit' => null])) ?>">Cancel</a>
  </form>
</div>
<?php else: ?>
<details class="card" style="margin-bottom:18px">
  <summary style="cursor:pointer;font-weight:600;font-size:15px">+ Add new topic</summary>
  <form method="post" style="margin-top:14px">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="grid grid--2">
      <div class="field"><label>Subject *</label>
        <select name="subject_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($subjects as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= $subjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Name *</label><input class="input" name="name" required></div>
    </div>
    <div class="grid grid--2">
      <div class="field"><label>Form level</label>
        <select name="form_level" class="input">
          <option value="">— (none) —</option>
          <option value="4" <?= $formLevel === 4 ? 'selected' : '' ?>>Tingkatan / Form 4</option>
          <option value="5" <?= $formLevel === 5 ? 'selected' : '' ?>>Tingkatan / Form 5</option>
        </select>
      </div>
      <div class="field"><label>Description</label><textarea name="description" rows="2"></textarea></div>
    </div>
    <button class="btn">Create topic</button>
  </form>
</details>
<?php endif; ?>

<?php if (!$edit && $subjectCounts): ?>
<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Jump to subject</h4>
  <div style="display:flex;flex-wrap:wrap;gap:8px">
    <a class="chip" href="<?= e(topics_link(['subject' => 0, 'page' => 1])) ?>"
       style="<?= $subjectId === 0 ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
       All <span class="muted" style="margin-left:4px">· <?= (int)array_sum(array_column($subjectCounts, 'topic_count')) ?></span>
    </a>
    <?php foreach ($subjectCounts as $sc): ?>
      <a class="chip" href="<?= e(topics_link(['subject' => (int)$sc['id'], 'page' => 1])) ?>"
         style="<?= $subjectId === (int)$sc['id'] ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
         <?= e($sc['name']) ?>
         <span class="muted" style="margin-left:4px">· <?= (int)$sc['topic_count'] ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">Topics <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?></span>
  </div>

  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <select name="subject" class="input" style="max-width:240px" onchange="this.form.submit()">
      <option value="">All subjects</option>
      <?php foreach ($subjects as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $subjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="form" class="input" style="max-width:160px" onchange="this.form.submit()">
      <option value="">All forms</option>
      <option value="4" <?= $formLevel === 4 ? 'selected' : '' ?>>Form 4 only</option>
      <option value="5" <?= $formLevel === 5 ? 'selected' : '' ?>>Form 5 only</option>
    </select>
    <input class="input" name="q" placeholder="Search topic name or slug" value="<?= e($q) ?>" style="flex:1;min-width:200px">
    <select name="per_page" class="input" style="max-width:130px" onchange="this.form.submit()">
      <?php foreach ([20, 50, 100, 200] as $pp): ?>
        <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?> / page</option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $subjectId > 0 || $formLevel > 0): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(topics_link(['q' => '', 'subject' => 0, 'form' => 0, 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php
  // Group by subject within the current page so headings only show once.
  $grouped = [];
  foreach ($rows as $t) { $grouped[$t['subject']][] = $t; }
  ?>
  <?php foreach ($grouped as $subjectName => $tops): ?>
    <div style="display:flex;align-items:baseline;gap:10px;margin:18px 0 8px">
      <h4 style="margin:0;color:var(--accent);font-size:13px;text-transform:uppercase;letter-spacing:.08em"><?= e($subjectName) ?></h4>
      <span class="muted" style="font-size:12px">· <?= count($tops) ?> on this page</span>
    </div>
    <div class="topics-list">
      <?php foreach ($tops as $t): ?>
        <div class="topic-row">
          <div class="topic-row__main">
            <div class="topic-row__name">
              <?= e($t['name']) ?>
              <?php if ($t['form_level']): ?>
                <span class="badge" style="margin-left:6px;font-size:10px"><?= $t['form_level'] === 4 ? 'T4' : 'T5' ?></span>
              <?php endif; ?>
            </div>
            <div class="topic-row__slug muted"><?= e($t['slug']) ?></div>
          </div>
          <div class="topic-row__stat">
            <span class="muted">Skills</span>
            <strong><?= (int)$t['skill_count'] ?></strong>
          </div>
          <div class="topic-row__stat">
            <span class="muted">Qs</span>
            <strong><?= (int)$t['question_count'] ?></strong>
          </div>
          <div class="topic-row__actions">
            <a class="btn btn--sm" href="<?= url('admin/ai_generate.php?topic_id=' . (int)$t['id']) ?>" title="Generate questions with AI">🤖 Qs</a>
            <a class="btn btn--sm btn--ghost" href="<?= url('admin/ai_generate_skills.php?topic_id=' . (int)$t['id']) ?>" title="Generate skills with AI">🤖 Skills</a>
            <a class="btn btn--sm btn--ghost" href="<?= e(topics_link(['edit' => (int)$t['id']])) ?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete this topic? Its skills and questions will lose this link.')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
              <button class="btn btn--sm btn--danger">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
  <?php if (!$rows): ?><p class="muted">No topics match these filters.</p><?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(topics_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(topics_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
          <a class="btn btn--sm <?= $p === $page ? '' : 'btn--ghost' ?>" href="<?= e(topics_link(['page' => $p])) ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(topics_link(['page' => $page + 1])) ?>">Next ›</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(topics_link(['page' => $pages])) ?>">Last »</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
.topics-list { display:flex; flex-direction:column; gap:6px; }
.topic-row {
  display:flex; align-items:center; gap:14px;
  padding:10px 14px;
  border:1px solid var(--border);
  border-radius:10px;
  background:var(--card-2);
}
.topic-row:hover { border-color:var(--primary); }
.topic-row__main { flex:1; min-width:180px; }
.topic-row__name { font-weight:600; font-size:14px; }
.topic-row__slug { font-size:11px; margin-top:2px; }
.topic-row__stat { width:64px; text-align:center; font-size:12px; }
.topic-row__stat strong { display:block; font-size:14px; margin-top:2px; }
.topic-row__actions { display:flex; gap:6px; flex-wrap:wrap; }
@media (max-width: 720px) {
  .topic-row { flex-wrap:wrap; gap:10px; }
  .topic-row__actions { width:100%; justify-content:flex-end; }
}
</style>
<?php
admin_layout_end();
