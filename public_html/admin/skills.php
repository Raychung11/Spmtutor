<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin    = require_role('admin');
$subjects = db_all('SELECT id, name FROM subjects ORDER BY sort_order, name');
$topics   = db_all('SELECT t.id, t.name, t.subject_id, s.name AS subject FROM topics t JOIN subjects s ON s.id = t.subject_id ORDER BY s.sort_order, s.name, t.name');

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

$q         = trim(input('q'));
$subjectId = input_int('subject');
$topicId   = input_int('topic');
$page      = max(1, input_int('page', 1));
$perPage   = 20;
$offset    = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($q !== '') {
    $where[]  = 'sk.name LIKE ?';
    $params[] = '%' . $q . '%';
}
if ($topicId > 0) {
    $where[]  = 'sk.topic_id = ?';
    $params[] = $topicId;
} elseif ($subjectId > 0) {
    $where[]  = 't.subject_id = ?';
    $params[] = $subjectId;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = (int) (db_one("SELECT COUNT(*) c FROM skills sk JOIN topics t ON t.id = sk.topic_id $whereSql", $params)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$rows = db_all(
    "SELECT sk.*, t.name AS topic, t.subject_id, s.name AS subject
     FROM skills sk JOIN topics t ON t.id = sk.topic_id JOIN subjects s ON s.id = t.subject_id
     $whereSql
     ORDER BY s.sort_order, s.name, t.name, sk.id
     LIMIT $perPage OFFSET $offset",
    $params
);

function skills_link(array $overrides = []): string
{
    global $q, $subjectId, $topicId, $page;
    $args = array_merge(['q' => $q, 'subject' => $subjectId, 'topic' => $topicId, 'page' => $page], $overrides);
    $args = array_filter($args, fn($v) => $v !== '' && $v !== null && $v !== 0);
    return url('admin/skills.php' . ($args ? '?' . http_build_query($args) : ''));
}

admin_layout_start('Skills', $admin, 'skills.php');
?>
<div class="card" style="margin-bottom:18px">
  <h3 style="margin-top:0"><?= $edit ? 'Edit skill' : 'Add skill' ?></h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
    <div class="grid grid--3">
      <div class="field"><label>Topic (subject / topic) *</label>
        <select name="topic_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($topics as $t): ?>
            <option value="<?= (int)$t['id'] ?>" <?= (int)($edit['topic_id'] ?? 0) === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['subject'] . ' / ' . $t['name']) ?></option>
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
    <div class="field"><label>Description</label><textarea name="description"><?= e($edit['description'] ?? '') ?></textarea></div>
    <button class="btn"><?= $edit ? 'Save changes' : 'Create' ?></button>
    <?php if ($edit): ?><a class="btn btn--ghost" href="<?= e(skills_link(['edit' => null])) ?>">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="card">
  <h3 style="margin:0 0 10px">All skills <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <select name="subject" class="input" style="max-width:220px" onchange="this.form.submit()">
      <option value="">All subjects</option>
      <?php foreach ($subjects as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $subjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="topic" class="input" style="max-width:240px" onchange="this.form.submit()">
      <option value="">All topics</option>
      <?php foreach ($topics as $t): if ($subjectId > 0 && (int)$t['subject_id'] !== $subjectId) continue; ?>
        <option value="<?= (int)$t['id'] ?>" <?= $topicId === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['subject'] . ' / ' . $t['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <input class="input" name="q" placeholder="Search skill name" value="<?= e($q) ?>" style="flex:1;min-width:160px">
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $subjectId > 0 || $topicId > 0): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(skills_link(['q' => '', 'subject' => 0, 'topic' => 0, 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php
  $grouped = [];
  foreach ($rows as $sk) { $grouped[$sk['subject'] . ' / ' . $sk['topic']][] = $sk; }
  ?>
  <?php foreach ($grouped as $heading => $items): ?>
    <h4 style="margin:18px 0 6px;color:var(--accent);font-size:14px;text-transform:uppercase;letter-spacing:.08em"><?= e($heading) ?></h4>
    <table class="table">
      <tbody>
      <?php foreach ($items as $sk): ?>
        <tr>
          <td><?= e($sk['name']) ?><?php if ($sk['description']): ?><br><span class="muted" style="font-size:12px"><?= e(mb_substr((string)$sk['description'], 0, 80)) ?></span><?php endif; ?></td>
          <td style="width:100px"><span class="badge badge--warn"><?= e($sk['difficulty']) ?></span></td>
          <td style="white-space:nowrap;width:1%">
            <a class="btn btn--sm btn--ghost" href="<?= e(skills_link(['edit' => (int)$sk['id']])) ?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete this skill?')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$sk['id'] ?>">
              <button class="btn btn--sm btn--danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endforeach; ?>
  <?php if (!$rows): ?><p class="muted">No skills match these filters.</p><?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px">
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
<?php
admin_layout_end();
