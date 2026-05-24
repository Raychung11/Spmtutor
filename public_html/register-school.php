<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';
require_once __DIR__ . '/inc/schools.php';

if (is_logged_in()) {
    redirect(dashboard_for(current_user()['role']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $schoolName = input('school_name');
    $type       = in_array(input('type'), ['school', 'learning_center'], true) ? input('type') : 'learning_center';
    $adminName  = input('name');
    $email      = strtolower(input('email'));
    $phone      = input('phone');
    $pass       = input('password');

    $err = null;
    if ($schoolName === '' || $adminName === '' || $email === '' || $pass === '') {
        $err = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Please enter a valid email address.';
    } elseif (strlen($pass) < 8) {
        $err = 'Password must be at least 8 characters.';
    }

    if ($err) {
        flash('error', $err);
        redirect('register-school.php');
    }

    try {
        // Create the school-admin account (active so they can sign in), and
        // the school itself as PENDING until a platform admin approves it.
        $uid = register_user($adminName, $email, $pass, 'school_admin', $phone ?: null);
        db_exec(
            'INSERT INTO schools (name, type, owner_user_id, contact_email, phone, status) VALUES (?,?,?,?,?,?)',
            [$schoolName, $type, $uid, $email, $phone ?: null, 'pending']
        );
        notify_admins_new_school($schoolName);

        boot_session();
        session_regenerate_id(true);
        $_SESSION['uid'] = $uid;
        flash('success', 'Registration received! Your school is pending approval — we\'ll notify you once it\'s active.');
        redirect('school/dashboard.php');
    } catch (RuntimeException $e) {
        flash('error', $e->getMessage());
        redirect('register-school.php');
    }
}

render_head('Register your school');
?>
<div class="auth">
  <div class="card auth__card">
    <div class="auth__brand"><?= e(APP_NAME) ?></div>
    <div class="auth__sub">Register your school or learning centre</div>
    <?php render_flashes(); ?>
    <form method="post" action="<?= url('register-school.php') ?>">
      <?= csrf_field() ?>
      <div class="field"><label>School / centre name *</label><input class="input" name="school_name" required></div>
      <div class="field"><label>Type</label>
        <select name="type" class="input">
          <option value="learning_center">Learning centre</option>
          <option value="school">School</option>
        </select>
      </div>
      <div class="field"><label>Your name (administrator) *</label><input class="input" name="name" required></div>
      <div class="field"><label>Email *</label><input class="input" type="email" name="email" required></div>
      <div class="field"><label>Phone</label><input class="input" name="phone"></div>
      <div class="field"><label>Password * (min 8 chars)</label><input class="input" type="password" name="password" required></div>
      <button class="btn btn--block" type="submit">Register school</button>
    </form>
    <p class="center muted" style="margin-top:16px">Registering as an individual? <a href="<?= url('register.php') ?>">Student / parent / teacher signup</a></p>
    <p class="center muted">Already have an account? <a href="<?= url('login.php') ?>">Log in</a></p>
  </div>
</div>
</body>
</html>
