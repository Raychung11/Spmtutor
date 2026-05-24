<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';

if (is_logged_in()) {
    redirect(dashboard_for(current_user()['role']));
}

$levels = [];
try {
    $levels = db_all('SELECT id, name FROM education_levels WHERE status = "active" ORDER BY sort_order');
} catch (Throwable $e) {
    redirect('install.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name  = input('name');
    $email = strtolower(input('email'));
    $phone = input('phone');
    $pass  = input('password');
    $role  = in_array(input('role'), ['student', 'parent', 'teacher'], true) ? input('role') : 'student';
    $level = input_int('education_level_id');

    $err = null;
    if ($name === '' || $email === '' || $pass === '') {
        $err = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Please enter a valid email address.';
    } elseif (strlen($pass) < 8) {
        $err = 'Password must be at least 8 characters.';
    }

    if ($err) {
        flash('error', $err);
        redirect('register.php');
    }

    try {
        $uid = register_user($name, $email, $pass, $role, $phone ?: null);
        if ($role === 'student' && $level) {
            db_exec('UPDATE student_profiles SET education_level_id = ? WHERE user_id = ?', [$level, $uid]);
        }
        boot_session();
        session_regenerate_id(true);
        $_SESSION['uid'] = $uid;
        flash('success', 'Welcome to ' . APP_NAME . '! Your free trial has started.');
        redirect(dashboard_for($role));
    } catch (RuntimeException $e) {
        flash('error', $e->getMessage());
        redirect('register.php');
    }
}

render_head('Create account');
?>
<div class="auth">
  <div class="card auth__card">
    <div class="auth__brand"><?= e(APP_NAME) ?></div>
    <div class="auth__sub">Create your account &mdash; 14-day free trial, no card needed.</div>
    <?php render_flashes(); ?>
    <form method="post" action="<?= url('register.php') ?>">
      <?= csrf_field() ?>
      <div class="field"><label>I am a</label>
        <select name="role" id="roleSel">
          <option value="student">Student</option>
          <option value="parent">Parent</option>
          <option value="teacher">Teacher / Coach</option>
        </select>
      </div>
      <div class="field"><label>Full name *</label><input class="input" name="name" required></div>
      <div class="field"><label>Email *</label><input class="input" type="email" name="email" required></div>
      <div class="field"><label>Phone (optional)</label><input class="input" name="phone"></div>
      <div class="field" id="levelField"><label>Education level</label>
        <select name="education_level_id">
          <option value="">-- select --</option>
          <?php foreach ($levels as $l): ?>
            <option value="<?= (int)$l['id'] ?>"><?= e($l['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Password * (min 8 chars)</label><input class="input" type="password" name="password" required></div>
      <button class="btn btn--block" type="submit">Create account</button>
    </form>
    <p class="center muted" style="margin-top:16px">Already have an account? <a href="<?= url('login.php') ?>">Log in</a></p>
  </div>
</div>
<script>
  var roleSel = document.getElementById('roleSel');
  var levelField = document.getElementById('levelField');
  roleSel.addEventListener('change', function () {
    levelField.style.display = roleSel.value === 'student' ? '' : 'none';
  });
</script>
</body>
</html>
