<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin    = require_role('admin');
$subjects = db_all('SELECT id, name FROM subjects ORDER BY sort_order, name');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');
    if ($action === 'create') {
        $name    = input('name');
        $subject = input_int('subject_id');
        $slug    = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        if ($name && $subject) {
            db_exec('INSERT INTO topics (subject_id, name, slug, description) VALUES (?,?,?,?)', [$subject, $name, $slug, input('description')]);
            flash('success', 'Topic created.');
        } else {
            flash('error', 'Subject and name are required.');
        }
    } elseif ($action === 'delete') {
        db_exec('DELETE FROM topics WHERE id = ?', [input_int('id')]);
        flash('success', 'Topic deleted.');
    }
    redirect('admin/topics.php');
}

$rows = db_all('SELECT t.*, s.name AS subject FROM topics t JOIN subjects s ON s.id = t.subject_id ORDER BY s.name, t.sort_order');

admin_layout_start('Topics', $admin, 'topics.php');
?>
<div class="grid grid--2">
  <div class="card">
    <h3>Add topic</h3>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create">
      <div class="field"><label>Subject</label>
        <select name="subject_id" class="input" required>
          <option value="">-- select --</option>
          <?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Name</label><input class="input" name="name" required></div>
      <div class="field"><label>Description</label><textarea name="description"></textarea></div>
      <button class="btn">Create</button>
    </form>
  </div>
  <div class="card">
    <h3>All topics</h3>
    <table class="table"><thead><tr><th>Topic</th><th>Subject</th><th></th></tr></thead><tbody>
    <?php foreach ($rows as $t): ?>
      <tr>
        <td><?= e($t['name']) ?></td>
        <td class="muted"><?= e($t['subject']) ?></td>
        <td>
          <form method="post" onsubmit="return confirm('Delete this topic?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
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
