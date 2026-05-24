<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin  = require_role('admin');
$topics = db_all('SELECT t.id, t.name, s.name AS subject FROM topics t JOIN subjects s ON s.id = t.subject_id ORDER BY s.name, t.name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');
    if ($action === 'create') {
        $name  = input('name');
        $topic = input_int('topic_id');
        $diff  = in_array(input('difficulty'), ['easy','medium','hard'], true) ? input('difficulty') : 'medium';
        if ($name && $topic) {
            db_exec('INSERT INTO skills (topic_id, name, difficulty, description) VALUES (?,?,?,?)', [$topic, $name, $diff, input('description')]);
            flash('success', 'Skill created.');
        } else {
            flash('error', 'Topic and name are required.');
        }
    } elseif ($action === 'delete') {
        db_exec('DELETE FROM skills WHERE id = ?', [input_int('id')]);
        flash('success', 'Skill deleted.');
    }
    redirect('admin/skills.php');
}

$rows = db_all('SELECT sk.*, t.name AS topic FROM skills sk JOIN topics t ON t.id = sk.topic_id ORDER BY t.name, sk.id');

admin_layout_start('Skills', $admin, 'skills.php');
?>
<div class="grid grid--2">
  <div class="card">
    <h3>Add skill</h3>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create">
      <div class="field"><label>Topic</label>
        <select name="topic_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($topics as $t): ?><option value="<?= (int)$t['id'] ?>"><?= e($t['subject'] . ' / ' . $t['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Skill name</label><input class="input" name="name" required></div>
      <div class="field"><label>Difficulty</label>
        <select name="difficulty" class="input"><option>easy</option><option selected>medium</option><option>hard</option></select>
      </div>
      <div class="field"><label>Description</label><textarea name="description"></textarea></div>
      <button class="btn">Create</button>
    </form>
  </div>
  <div class="card">
    <h3>All skills</h3>
    <table class="table"><thead><tr><th>Skill</th><th>Topic</th><th>Diff</th><th></th></tr></thead><tbody>
    <?php foreach ($rows as $sk): ?>
      <tr>
        <td><?= e($sk['name']) ?></td>
        <td class="muted"><?= e($sk['topic']) ?></td>
        <td><span class="badge badge--warn"><?= e($sk['difficulty']) ?></span></td>
        <td>
          <form method="post" onsubmit="return confirm('Delete this skill?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$sk['id'] ?>">
            <button class="btn btn--sm btn--danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  </div>
</div>
<?php
admin_layout_end();
