<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

$token = input('token');

/** Resolve a valid, unexpired reset row for a token. */
function valid_reset(string $token): ?array
{
    if ($token === '') {
        return null;
    }
    return db_one('SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() ORDER BY id DESC LIMIT 1', [$token]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $token = input('token');
    $pass  = input('password');
    $pass2 = input('password_confirm');
    $reset = valid_reset($token);

    if (!$reset) {
        flash('error', 'This reset link is invalid or has expired.');
        redirect('forgot-password.php');
    }
    if (strlen($pass) < 8) {
        flash('error', 'Password must be at least 8 characters.');
        redirect('reset-password.php?token=' . urlencode($token));
    }
    if ($pass !== $pass2) {
        flash('error', 'Passwords do not match.');
        redirect('reset-password.php?token=' . urlencode($token));
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);
    db_exec('UPDATE users SET password_hash = ? WHERE email = ?', [$hash, $reset['email']]);
    // Invalidate all outstanding tokens for this email.
    db_exec('DELETE FROM password_resets WHERE email = ?', [$reset['email']]);

    flash('success', 'Your password has been reset. Please log in.');
    redirect('login.php');
}

$reset = valid_reset($token);

render_head('Reset password');
?>
<div class="auth">
  <div class="card auth__card">
    <div class="auth__brand"><?= e(APP_NAME) ?></div>
    <div class="auth__sub">Choose a new password</div>
    <?php render_flashes(); ?>
    <?php if (!$reset): ?>
      <div class="flash flash--error">This reset link is invalid or has expired.</div>
      <a class="btn btn--block" href="<?= url('forgot-password.php') ?>">Request a new link</a>
    <?php else: ?>
      <form method="post" action="<?= url('reset-password.php') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <div class="field"><label>New password (min 8)</label><input class="input" type="password" name="password" required autofocus></div>
        <div class="field"><label>Confirm password</label><input class="input" type="password" name="password_confirm" required></div>
        <button class="btn btn--block" type="submit">Reset password</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
