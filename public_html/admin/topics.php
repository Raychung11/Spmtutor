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
        $desc    = input('description');
        $slug    = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        if ($name === '' || $subject === 0) {
            flash('error', 'Subject and name are required.');
        } elseif ($action === 'create') {
            db_exec('INSERT INTO topics (subject_id, name, slug, description) VALUES (?,?,?,?)', [$subject, $name, $slug, $desc]);
            flash('success', 'Topic created.');
        } else {
            db_exec('UPDATE topics SET subject_id = ?, name = ?, description = ? WHERE id = ?', [$subject, $name, $desc, input_int('id')]);
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

$q         = trim(input('q'));
$subjectId = input_int('subject');
$page      = max(1, input_int('page', 1));
$perPage   = 20;
$offset    = ($page - 1) * $perPage;

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
     ORDER BY s.sort_order, s.name, t.sort_order, t.id
     LIMIT $perPage OFFSET $offset",
    $params
);

function topics_link(array $overrides = []): string
{
    global $q, $subjectId, $page;
    $args = array_merge(['q' => $q, 'subject' => $subjectId, 'page' => $page], $overrides);
    $args = array_filter($args, fn($v) => $v !== '' && $v !== null && $v !== 0);
    return url('admin/topics.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Topics', $admin, 'topics.php');
?>
<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0"><?= $edit ? 'Edit topic' : 'Add topic' ?></h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
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
    <div class="field"><label>Description</label><textarea name="description"><?= e($edit['description'] ?? '') ?></textarea></div>
    <button class="btn"><?= $edit ? 'Save changes' : 'Create' ?></button>
    <?php if ($edit): ?><a class="btn btn--ghost" href="<?= e(topics_link(['edit' => null])) ?>">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">All topics <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
  </div>
  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <select name="subject" class="input" style="max-width:260px" onchange="this.form.submit()">
      <option value="">All subjects</option>
      <?php foreach ($subjects as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $subjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <input class="input" name="q" placeholder="Search topic name or slug" value="<?= e($q) ?>" style="flex:1;min-width:200px">
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $subjectId > 0): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(topics_link(['q' => '', 'subject' => 0, 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php
  // Group by subject within the current page.
  $grouped = [];
  foreach ($rows as $t) { $grouped[$t['subject']][] = $t; }
  ?>
  <?php foreach ($grouped as $subjectName => $tops): ?>
    <h4 style="margin:18px 0 8px;color:var(--accent);font-size:14px;text-transform:uppercase;letter-spacing:.08em"><?= e($subjectName) ?></h4>
    <table class="table">
      <tbody>
      <?php foreach ($tops as $t): ?>
        <tr>
          <td><?= e($t['name']) ?><br><span class="muted" style="font-size:12px"><?= e($t['slug']) ?></span></td>
          <td style="width:100px"><span class="muted">Skills</span><br><?= (int)$t['skill_count'] ?></td>
          <td style="width:100px"><span class="muted">Questions</span><br><?= (int)$t['question_count'] ?></td>
          <td style="white-space:nowrap;width:1%">
            <a class="btn btn--sm" href="<?= url('admin/ai_generate.php?topic_id=' . (int)$t['id']) ?>" title="Generate questions with AI">🤖 Qs</a>
            <a class="btn btn--sm btn--ghost" href="<?= url('admin/ai_generate_skills.php?topic_id=' . (int)$t['id']) ?>" title="Generate skills with AI">🤖 Skills</a>
            <a class="btn btn--sm btn--ghost" href="<?= e(topics_link(['edit' => (int)$t['id']])) ?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete this topic? Its skills and questions will lose this link.')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
              <button class="btn btn--sm btn--danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endforeach; ?>
  <?php if (!$rows): ?><p class="muted">No topics match these filters.</p><?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px">
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
<?php
admin_layout_end();
