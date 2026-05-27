<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/ai.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');
$testReply = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');

    if ($action === 'save') {
        $provider = input('provider') === 'openai' ? 'openai' : 'anthropic';
        setting_set('ai_provider', $provider);
        setting_set('ai_model', input('model'));
        setting_set('ai_api_base', input('api_base'));

        if (input('clear_key') === '1') {
            setting_set('ai_api_key', '');
        } elseif (input('api_key') !== '') {
            setting_set('ai_api_key', input('api_key'));
        }
        flash('success', 'AI settings saved.');
        redirect('admin/ai_settings.php');
    }

    if ($action === 'test') {
        if (!ai_enabled()) {
            flash('info', 'No API key configured — the tutor runs in offline demo mode.');
            redirect('admin/ai_settings.php');
        }
        $reply = ai_chat('You are a connection test. Reply briefly.', [['role' => 'user', 'content' => 'Reply with exactly: LulusAI connection OK']], 0.0);
        flash(str_contains($reply, 'OK') ? 'success' : 'info', 'AI responded: ' . mb_substr($reply, 0, 200));
        redirect('admin/ai_settings.php');
    }
}

$cfg     = ai_cfg();
$hasKey  = $cfg['key'] !== '';
$fromEnv = AI_API_KEY !== '' && (setting_get('ai_api_key', '') ?? '') === '';

admin_layout_start('AI Settings', $admin, 'ai_settings.php');
?>
<div class="card">
  <h2>AI provider</h2>
  <p class="muted">
    Status:
    <?php if ($hasKey): ?>
      <span class="badge badge--good">Live (<?= e($cfg['provider']) ?>, <?= e($cfg['model']) ?>)</span>
      <?= $fromEnv ? '<span class="muted">key from server env</span>' : '' ?>
    <?php else: ?>
      <span class="badge badge--warn">Demo mode — no API key set</span>
    <?php endif; ?>
  </p>
  <p class="muted">Configure the LLM that powers the tutor, diagnostic and Snap &amp; Check marking. Settings here override server environment variables. The default model for new builds is <code><?= e(AI_MODEL) ?></code>.</p>

  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <div class="grid grid--2">
      <div class="field"><label>Provider</label>
        <select name="provider" class="input">
          <option value="anthropic" <?= $cfg['provider'] === 'anthropic' ? 'selected' : '' ?>>Anthropic (Claude)</option>
          <option value="openai" <?= $cfg['provider'] === 'openai' ? 'selected' : '' ?>>OpenAI</option>
        </select>
      </div>
      <div class="field"><label>Model</label><input class="input" name="model" value="<?= e($cfg['model']) ?>" placeholder="e.g. claude-sonnet-4-6"></div>
    </div>
    <div class="field">
      <label>API key <?= $hasKey ? '<span class="muted">(configured — leave blank to keep)</span>' : '' ?></label>
      <input class="input" type="password" name="api_key" autocomplete="off" placeholder="<?= $hasKey ? '••••••••••••' : 'paste your API key' ?>">
    </div>
    <?php if ($hasKey && !$fromEnv): ?>
      <label class="muted" style="display:inline-flex;gap:6px;align-items:center;font-size:13px"><input type="checkbox" name="clear_key" value="1"> Clear the stored key (revert to demo mode)</label>
    <?php endif; ?>
    <div class="field" style="margin-top:14px"><label>API base URL (optional override)</label><input class="input" name="api_base" value="<?= e($cfg['base']) ?>" placeholder="leave blank for provider default"></div>
    <button class="btn">Save settings</button>
  </form>
</div>

<div class="card" style="margin-top:18px">
  <h3>Test connection</h3>
  <p class="muted">Sends a tiny prompt to verify your key and model work end to end.</p>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="test">
    <button class="btn btn--ghost">Run AI test</button>
  </form>
</div>
<p class="muted" style="margin-top:14px">Security note: the key is stored in the database (not web-accessible). Prefer a server environment variable (<code>AI_API_KEY</code>) if you have shell/SSH access.</p>
<?php
admin_layout_end();
