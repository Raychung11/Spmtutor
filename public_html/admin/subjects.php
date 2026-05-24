<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin  = require_role('admin');
$levels = db_all('SELECT id, name FROM education_levels ORDER BY sort_order');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');
    if ($action === 'create' || $action === 'update') {
        $name  = input('name');
        $level = input_int('education_level_id') ?: null;
        $desc  = input('description');
        $slug  = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        if ($name === '') {
            flash('error', 'Name is required.');
        } elseif ($action === 'create') {
            db_exec('INSERT INTO subjects (education_level_id, name, slug, description) VALUES (?,?,?,?)', [$level, $name, $slug, $desc]);
            flash('success', 'Subject created.');
        } else {
            db_exec('UPDATE subjects SET education_level_id = ?, name = ?, description = ? WHERE id = ?', [$level, $name, $desc, input_int('id')]);
            flash('success', 'Subject updated.');
        }
    } elseif ($action === 'delete') {
        db_exec('DELETE FROM subjects WHERE id = ?', [input_int('id')]);
        flash('success', 'Subject deleted.');
    }
    redirect('admin/subjects.php');
}

$rows = db_all('SELECT s.*, l.name AS level FROM subjects s LEFT JOIN education_levels l ON l.id = s.education_level_id ORDER BY s.sort_order, s.id');

admin_layout_start('Subjects', $admin, 'subjects.php');
?>
<div class="grid grid--2">
  <div class="card">
    <h3>Add subject</h3>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create">
      <div class="field"><label>Name</label><input class="input" name="name" required></div>
      <div class="field"><label>Education level</label>
        <select name="education_level_id" class="input">
          <option value="">-- none --</option>
          <?php foreach ($levels as $l): ?><option value="<?= (int)$l['id'] ?>"><?= e($l['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Description</label><textarea name="description"></textarea></div>
      <button class="btn">Create</button>
    </form>
  </div>
  <div class="card">
    <h3>All subjects</h3>
    <table class="table"><thead><tr><th>Name</th><th>Level</th><th></th></tr></thead><tbody>
    <?php foreach ($rows as $s): ?>
      <tr>
        <td><?= e($s['name']) ?></td>
        <td class="muted"><?= e($s['level'] ?? '-') ?></td>
        <td>
          <form method="post" onsubmit="return confirm('Delete this subject and its topics?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
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
