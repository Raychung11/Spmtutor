<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = input_int('id');
    if ($id) {
        db_exec(
            'UPDATE landing_sections SET title = ?, subtitle = ?, body = ?, status = ? WHERE id = ?',
            [input('title'), input('subtitle'), input('body'), input('status') === 'active' ? 'active' : 'hidden', $id]
        );
        flash('success', 'Section updated.');
    }
    redirect('admin/landing.php');
}

$rows = db_all('SELECT * FROM landing_sections ORDER BY sort_order');

admin_layout_start('Landing Page CMS', $admin, 'landing.php');
?>
<p class="muted">Edit the public landing page sections. Use <code>|</code> to separate list items in the body (e.g. feature list).</p>
<?php foreach ($rows as $s): ?>
  <div class="card" style="margin-bottom:18px">
    <h3><?= e(ucfirst($s['section_key'])) ?> section</h3>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
      <div class="field"><label>Title</label><input class="input" name="title" value="<?= e($s['title'] ?? '') ?>"></div>
      <div class="field"><label>Subtitle</label><input class="input" name="subtitle" value="<?= e($s['subtitle'] ?? '') ?>"></div>
      <div class="field"><label>Body</label><textarea name="body"><?= e($s['body'] ?? '') ?></textarea></div>
      <div class="field"><label>Status</label>
        <select name="status" class="input">
          <option value="active" <?= $s['status'] === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="hidden" <?= $s['status'] !== 'active' ? 'selected' : '' ?>>Hidden</option>
        </select>
      </div>
      <button class="btn">Save</button>
    </form>
  </div>
<?php endforeach; ?>
<?php
admin_layout_end();
