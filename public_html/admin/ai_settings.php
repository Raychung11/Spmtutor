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
        $maxTok = max(512, min(16384, input_int('max_tokens', 4096)));
        setting_set('ai_max_tokens', (string) $maxTok);

        if (input('clear_key') === '1') {
            setting_set('ai_api_key', '');
        } elseif (input('api_key') !== '') {
            setting_set('ai_api_key', input('api_key'));
        }
        flash('success', 'AI settings saved.');
        redirect('admin/ai_settings.php');
    }

    if ($action === 'save_caps') {
        setting_set('ai_cap_per_user', (string) max(0, input_int('cap_per_user')));
        setting_set('ai_cap_global',   (string) max(0, input_int('cap_global')));
        flash('success', 'Daily caps updated.');
        redirect('admin/ai_settings.php');
    }

    if ($action === 'apply_defaults') {
        require_once __DIR__ . '/../inc/ai_questions.php';
        $force = input('force_overwrite') === '1';
        $touched = apply_default_subject_prompts($force);
        flash('success', "Applied defaults to {$touched['updated']} subjects (skipped {$touched['kept']} that already had a custom prompt).");
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
    <div class="grid grid--2" style="margin-top:14px">
      <div class="field"><label>API base URL (optional override)</label><input class="input" name="api_base" value="<?= e($cfg['base']) ?>" placeholder="leave blank for provider default"></div>
      <div class="field"><label>Max output tokens per call</label>
        <input class="input" type="number" name="max_tokens" min="512" max="16384" step="256" value="<?= e(setting_get('ai_max_tokens', '4096') ?? '4096') ?>">
        <p class="muted" style="font-size:12px;margin:4px 0 0">Higher = longer answers but more cost. 4096 fits ~5 SPM MCQs with explanations. Raise to 8192 if you're generating 10+ questions per call and seeing truncated JSON.</p>
      </div>
    </div>
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

<div class="card" style="margin-top:18px">
  <h3>Daily cost guardrails</h3>
  <?php $caps = ai_daily_caps(); $q = ai_quota_status((int) $admin['id']); ?>
  <p class="muted">Today's usage: <strong><?= $q['used_global'] ?></strong> calls platform-wide (cap <?= $caps['global'] ?>) · you personally <strong><?= $q['used_user'] ?></strong> (cap <?= $caps['per_user'] ?>).</p>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_caps">
    <div class="grid grid--2">
      <div class="field"><label>Per-user daily cap</label><input class="input" type="number" min="0" name="cap_per_user" value="<?= $caps['per_user'] ?>"></div>
      <div class="field"><label>Platform-wide daily cap</label><input class="input" type="number" min="0" name="cap_global" value="<?= $caps['global'] ?>"></div>
    </div>
    <p class="muted" style="font-size:13px;margin:0 0 10px">Hitting a cap returns a friendly message and stops the call before it hits your provider. Caps reset at midnight server time.</p>
    <button class="btn btn--ghost">Save caps</button>
  </form>
</div>

<div class="card" style="margin-top:18px">
  <h3>Apply default prompts to all subjects</h3>
  <p class="muted">Picks a sensible subject type / language for each of the <?= (int)(db_one('SELECT COUNT(*) c FROM subjects')['c'] ?? 0) ?> subjects and writes the composed default into <code>ai_prompt</code>. Safer than editing each subject by hand.</p>
  <form method="post" onsubmit="return confirm('Apply default AI prompts now?')">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="apply_defaults">
    <label class="muted" style="display:inline-flex;gap:6px;align-items:center;font-size:13px">
      <input type="checkbox" name="force_overwrite" value="1"> Overwrite subjects that already have a custom prompt
    </label>
    <p><button class="btn">Apply defaults</button></p>
  </form>
</div>

<p class="muted" style="margin-top:14px">Security note: the key is stored in the database (not web-accessible). Prefer a server environment variable (<code>AI_API_KEY</code>) if you have shell/SSH access.</p>
<?php
admin_layout_end();
