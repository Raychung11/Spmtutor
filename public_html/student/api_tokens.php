<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/api.php';
require_once __DIR__ . '/../inc/student_layout.php';

$user = require_role('student');
$uid  = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');
    if ($action === 'create') {
        $plain = api_issue_token($uid, input('name') ?: 'mobile');
        // Show the plaintext token exactly once via the session.
        boot_session();
        $_SESSION['new_api_token'] = $plain;
        flash('success', 'Token created. Copy it now — it will not be shown again.');
    } elseif ($action === 'revoke') {
        db_exec('DELETE FROM api_tokens WHERE id = ? AND user_id = ?', [input_int('id'), $uid]);
        flash('success', 'Token revoked.');
    }
    redirect('student/api_tokens.php');
}

boot_session();
$newToken = $_SESSION['new_api_token'] ?? null;
unset($_SESSION['new_api_token']);

$tokens = db_all('SELECT id, name, last_used_at, expires_at, created_at FROM api_tokens WHERE user_id = ? ORDER BY id DESC', [$uid]);

student_layout_start('API Access', $user, 'api_tokens.php');
?>
<div class="card">
  <h2>Mobile API access</h2>
  <p class="muted">Create a personal token to use the SkillTutor AI mobile API. Send it as <code>Authorization: Bearer &lt;token&gt;</code>. See <a href="<?= url('api/openapi.yaml') ?>">the API spec</a>.</p>
  <?php if ($newToken): ?>
    <div class="flash flash--success">
      <strong>Your new token (shown once):</strong>
      <div style="word-break:break-all;font-family:monospace;margin-top:6px"><?= e($newToken) ?></div>
    </div>
  <?php endif; ?>
  <form method="post" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="field" style="margin:0"><label>Device / token name</label><input class="input" name="name" placeholder="e.g. My phone"></div>
    <button class="btn">Generate token</button>
  </form>
</div>

<div class="card" style="margin-top:18px">
  <h3>Your tokens</h3>
  <?php if ($tokens): ?>
    <table class="table"><thead><tr><th>Name</th><th>Last used</th><th>Expires</th><th>Created</th><th></th></tr></thead><tbody>
    <?php foreach ($tokens as $t): ?>
      <tr>
        <td><?= e($t['name'] ?? 'token') ?></td>
        <td class="muted"><?= $t['last_used_at'] ? e(date('d M, H:i', strtotime((string)$t['last_used_at']))) : 'never' ?></td>
        <td class="muted"><?= $t['expires_at'] ? e(date('d M Y', strtotime((string)$t['expires_at']))) : '—' ?></td>
        <td class="muted"><?= e(date('d M Y', strtotime((string)$t['created_at']))) ?></td>
        <td>
          <form method="post" onsubmit="return confirm('Revoke this token?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="revoke">
            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
            <button class="btn btn--sm btn--danger">Revoke</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  <?php else: ?><p class="muted">No tokens yet.</p><?php endif; ?>
</div>
<?php
student_layout_end();
