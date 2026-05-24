<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id     = input_int('id');
    $prompt = input('system_prompt');
    $temp   = (float) input('temperature');
    $model  = input('model');
    $temp   = max(0, min(1, $temp));
    if ($id && $prompt !== '') {
        db_exec('UPDATE ai_prompt_templates SET system_prompt = ?, temperature = ?, model = ? WHERE id = ?', [$prompt, $temp, $model ?: null, $id]);
        flash('success', 'Prompt updated.');
    } else {
        flash('error', 'Prompt text is required.');
    }
    redirect('admin/ai_prompts.php');
}

$rows = db_all('SELECT * FROM ai_prompt_templates ORDER BY id');

admin_layout_start('AI Prompts', $admin, 'ai_prompts.php');
?>
<p class="muted">Edit the system prompts that drive each AI feature. Changes take effect immediately.</p>
<?php foreach ($rows as $p): ?>
  <div class="card" style="margin-bottom:18px">
    <h3><?= e($p['name']) ?> <span class="badge"><?= e($p['code']) ?></span></h3>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
      <div class="field"><label>System prompt</label><textarea name="system_prompt" style="min-height:140px"><?= e($p['system_prompt']) ?></textarea></div>
      <div class="grid grid--2">
        <div class="field"><label>Temperature (0&ndash;1)</label><input class="input" type="number" step="0.05" min="0" max="1" name="temperature" value="<?= e((string)$p['temperature']) ?>"></div>
        <div class="field"><label>Model override (optional)</label><input class="input" name="model" value="<?= e($p['model'] ?? '') ?>" placeholder="default"></div>
      </div>
      <button class="btn">Save</button>
    </form>
  </div>
<?php endforeach; ?>
<?php
admin_layout_end();
