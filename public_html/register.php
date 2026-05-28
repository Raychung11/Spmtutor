<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/ui.php';
require_once __DIR__ . '/inc/notifications.php';

if (is_logged_in()) {
    redirect(dashboard_for(current_user()['role']));
}

$levels  = [];
$schools = [];
try {
    $levels  = db_all('SELECT id, name FROM education_levels WHERE status = "active" ORDER BY sort_order');
    $schools = db_all('SELECT id, name FROM schools WHERE status = "active" ORDER BY name');
} catch (Throwable $e) {
    redirect('install.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name     = input('name');
    $email    = strtolower(input('email'));
    $phone    = input('phone');
    $pass     = input('password');
    $role     = in_array(input('role'), ['student', 'parent', 'teacher'], true) ? input('role') : 'student';
    $level    = input_int('education_level_id');
    $schoolId = input_int('school_id');

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

        // Optional school join request (student/teacher only).
        if ($schoolId && in_array($role, ['student', 'teacher'], true)) {
            $school = db_one('SELECT id, name, owner_user_id FROM schools WHERE id = ? AND status = "active"', [$schoolId]);
            if ($school) {
                db_exec(
                    'INSERT INTO school_members (school_id, user_id, member_role, status) VALUES (?,?,?,?)',
                    [(int) $school['id'], $uid, $role, 'pending']
                );
                if ($school['owner_user_id']) {
                    notify((int) $school['owner_user_id'], 'New ' . $role . ' join request',
                        $name . ' (' . $email . ') asked to join ' . $school['name'] . '.', 'school');
                }
            }
        }

        boot_session();
        session_regenerate_id(true);
        $_SESSION['uid'] = $uid;
        $welcome = 'Welcome to ' . APP_NAME . '! Your free trial has started.';
        if ($schoolId && in_array($role, ['student', 'teacher'], true)) {
            $welcome .= ' Your request to join the school is pending the school admin\'s approval.';
        }
        flash('success', $welcome);
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
      <div class="field" id="schoolField">
        <label>School / learning centre (optional)</label>
        <select name="school_id" class="input">
          <option value="">-- I'm not joining a school --</option>
          <?php foreach ($schools as $s): ?>
            <option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <p class="muted" style="font-size:12px;margin:6px 0 0">Picking a school sends a join request to the school admin for approval.</p>
      </div>
      <div class="field"><label>Password * (min 8 chars)</label><input class="input" type="password" name="password" required></div>
      <button class="btn btn--block" type="submit">Create account</button>
    </form>
    <p class="center muted" style="margin-top:16px">Already have an account? <a href="<?= url('login.php') ?>">Log in</a></p>
    <p class="center muted">Registering a school or learning centre? <a href="<?= url('register-school.php') ?>">School signup</a></p>
  </div>
</div>
<script>
  var roleSel = document.getElementById('roleSel');
  var levelField = document.getElementById('levelField');
  var schoolField = document.getElementById('schoolField');
  function syncFields() {
    levelField.style.display  = roleSel.value === 'student' ? '' : 'none';
    schoolField.style.display = roleSel.value === 'parent'  ? 'none' : '';
  }
  roleSel.addEventListener('change', syncFields);
  syncFields();
</script>
</body>
</html>
