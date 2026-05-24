<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(input('email'));
    // Always create a token if the user exists, but never reveal whether it does.
    $user = db_one('SELECT id FROM users WHERE email = ?', [$email]);
    if ($user) {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        db_exec('INSERT INTO password_resets (email, token, expires_at) VALUES (?,?,?)', [$email, $token, $expires]);
        // Email delivery is integration-ready (Phase 2). For now we log the link.
        error_log('[SkillTutor] Password reset link: ' . url('reset-password.php?token=' . $token));
    }
    flash('info', 'If that email is registered, we have sent a reset link.');
    redirect('forgot-password.php');
}

render_head('Forgot password');
?>
<div class="auth">
  <div class="card auth__card">
    <div class="auth__brand"><?= e(APP_NAME) ?></div>
    <div class="auth__sub">Reset your password</div>
    <?php render_flashes(); ?>
    <form method="post" action="<?= url('forgot-password.php') ?>">
      <?= csrf_field() ?>
      <div class="field"><label>Email</label><input class="input" type="email" name="email" required autofocus></div>
      <button class="btn btn--block" type="submit">Send reset link</button>
    </form>
    <p class="center muted" style="margin-top:16px"><a href="<?= url('login.php') ?>">Back to login</a></p>
  </div>
</div>
</body>
</html>
