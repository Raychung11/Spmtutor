<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

if (is_logged_in()) {
    redirect(dashboard_for(current_user()['role']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(input('email'));
    $pass  = input('password');
    if (attempt_login($email, $pass)) {
        redirect(dashboard_for(current_user()['role']));
    }
    flash('error', 'Invalid email or password.');
    redirect('login.php');
}

render_head('Log in');
?>
<div class="auth">
  <div class="card auth__card">
    <div class="auth__brand"><?= e(APP_NAME) ?></div>
    <div class="auth__sub">Welcome back. Log in to keep learning.</div>
    <?php render_flashes(); ?>
    <form method="post" action="<?= url('login.php') ?>">
      <?= csrf_field() ?>
      <div class="field"><label>Email</label><input class="input" type="email" name="email" required autofocus></div>
      <div class="field"><label>Password</label><input class="input" type="password" name="password" required></div>
      <button class="btn btn--block" type="submit">Log in</button>
    </form>
    <p class="center muted" style="margin-top:16px">
      <a href="<?= url('forgot-password.php') ?>">Forgot password?</a>
    </p>
    <p class="center muted">No account? <a href="<?= url('register.php') ?>">Start free</a></p>
  </div>
</div>
</body>
</html>
