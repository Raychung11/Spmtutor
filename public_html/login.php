<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

$existing = current_user();

// Allow ?switch=1 to force the login form even when a session exists,
// so users can switch to a different role without manual logout.
$forceForm = input('switch') === '1';

if ($existing && !$forceForm) {
    // Don't silently bounce — show them exactly which account they're on.
    render_head('Already signed in');
    ?>
    <div class="auth">
      <div class="card auth__card">
        <div class="auth__brand"><?= e(APP_NAME) ?></div>
        <div class="auth__sub">You're already signed in.</div>
        <div class="flash flash--info" style="margin-top:14px">
          Signed in as <strong><?= e($existing['name']) ?></strong> &lt;<?= e($existing['email']) ?>&gt;
          <br>Role: <span class="badge"><?= e($existing['role']) ?></span>
        </div>
        <a class="btn btn--block" href="<?= url(dashboard_for($existing['role'])) ?>">Go to my dashboard</a>
        <a class="btn btn--block btn--ghost" style="margin-top:10px" href="<?= url('logout.php') ?>">Log out</a>
        <a class="btn btn--block btn--ghost" style="margin-top:10px" href="<?= url('login.php?switch=1') ?>">Sign in as someone else</a>
      </div>
    </div>
    </body></html>
    <?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(input('email'));
    $pass  = input('password');
    if (attempt_login($email, $pass)) {
        $user = current_user();
        flash('success', 'Signed in as ' . $user['name'] . ' (' . $user['role'] . ').');
        redirect(dashboard_for($user['role']));
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
    <?php if ($existing): ?>
      <div class="flash flash--info">
        You're switching from <strong><?= e($existing['email']) ?></strong> (<?= e($existing['role']) ?>). Submitting the form below will sign you in as a different account.
      </div>
    <?php endif; ?>
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
