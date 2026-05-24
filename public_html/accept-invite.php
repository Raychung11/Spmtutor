<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';
require_once __DIR__ . '/inc/schools.php';

$token  = input('token');
$invite = valid_invitation($token);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $token  = input('token');
    $invite = valid_invitation($token);
    if (!$invite) {
        flash('error', 'This invitation is invalid or has expired.');
        redirect('accept-invite.php?token=' . urlencode($token));
    }

    $email = strtolower($invite['email']);
    $role  = $invite['member_role'];

    $existing = db_one('SELECT id, role FROM users WHERE email = ?', [$email]);
    if ($existing) {
        // Account already exists — link it if the role matches, then ask them to log in.
        if ($existing['role'] !== $role) {
            flash('error', "An account with this email exists as a {$existing['role']} and cannot join as a {$role}.");
            redirect('accept-invite.php?token=' . urlencode($token));
        }
        accept_invitation($invite, (int) $existing['id']);
        flash('success', 'You have joined ' . $invite['school_name'] . '. Please log in.');
        redirect('login.php');
    }

    $name = input('name');
    $pass = input('password');
    if ($name === '' || strlen($pass) < 8) {
        flash('error', 'Enter your name and a password of at least 8 characters.');
        redirect('accept-invite.php?token=' . urlencode($token));
    }

    try {
        $uid = register_user($name, $email, $pass, $role);
        accept_invitation($invite, $uid);
        boot_session();
        session_regenerate_id(true);
        $_SESSION['uid'] = $uid;
        flash('success', 'Welcome! You have joined ' . $invite['school_name'] . '.');
        redirect(dashboard_for($role));
    } catch (RuntimeException $e) {
        flash('error', $e->getMessage());
        redirect('accept-invite.php?token=' . urlencode($token));
    }
}

render_head('Accept invitation');
?>
<div class="auth">
  <div class="card auth__card">
    <div class="auth__brand"><?= e(APP_NAME) ?></div>
    <?php render_flashes(); ?>
    <?php if (!$invite): ?>
      <div class="flash flash--error">This invitation is invalid or has expired.</div>
      <a class="btn btn--block" href="<?= url('') ?>">Go home</a>
    <?php else:
      $accountExists = (bool) db_one('SELECT id FROM users WHERE email = ?', [strtolower($invite['email'])]); ?>
      <div class="auth__sub">Join <strong><?= e($invite['school_name']) ?></strong> as a <?= e($invite['member_role']) ?></div>
      <form method="post" action="<?= url('accept-invite.php') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <div class="field"><label>Email</label><input class="input" type="email" value="<?= e($invite['email']) ?>" disabled></div>
        <?php if ($accountExists): ?>
          <p class="muted">You already have an account with this email. Accept to link it to the school, then log in.</p>
          <button class="btn btn--block" type="submit">Join school</button>
        <?php else: ?>
          <div class="field"><label>Your name</label><input class="input" name="name" required></div>
          <div class="field"><label>Create a password (min 8)</label><input class="input" type="password" name="password" required></div>
          <button class="btn btn--block" type="submit">Create account &amp; join</button>
        <?php endif; ?>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
