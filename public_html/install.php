<?php
/**
 * One-time installer: runs schema.sql + seed.sql and creates the admin user.
 * Delete this file (or the generated install.lock) after running in production.
 */
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';

$lockFile = __DIR__ . '/install.lock';
$installed = is_file($lockFile);
$messages = [];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$installed) {
    csrf_check();
    $adminName  = input('admin_name', 'Site Admin');
    $adminEmail = strtolower(input('admin_email'));
    $adminPass  = input('admin_pass');

    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL) || strlen($adminPass) < 8) {
        $error = 'Enter a valid admin email and a password of at least 8 characters.';
    } else {
        try {
            $cfg = require __DIR__ . '/config/db_config.php';
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['name'], $cfg['charset']);
            $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => true,
            ]);

            $pdo->exec(file_get_contents(__DIR__ . '/sql/schema.sql'));
            $messages[] = 'Schema created.';
            $pdo->exec(file_get_contents(__DIR__ . '/sql/seed.sql'));
            $messages[] = 'Seed data inserted.';
            $pdo->exec(file_get_contents(__DIR__ . '/sql/content.sql'));
            $messages[] = 'Content pack inserted.';

            // Run the idempotent course seeder for the broader catalog.
            require_once __DIR__ . '/cron/seed_courses.php';
            $cs = seed_courses();
            $messages[] = "Courses seeded — topics: {$cs['topics']}, skills: {$cs['skills']}, questions: {$cs['questions']}.";

            // Create / update the admin account with a real password hash.
            $hash = password_hash($adminPass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$adminEmail]);
            if ($stmt->fetchColumn()) {
                $up = $pdo->prepare("UPDATE users SET password_hash = ?, role = 'admin', status = 'active', name = ? WHERE email = ?");
                $up->execute([$hash, $adminName, $adminEmail]);
            } else {
                $ins = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, status) VALUES (?,?,?,'admin','active')");
                $ins->execute([$adminName, $adminEmail, $hash]);
            }
            $messages[] = 'Admin account ready: ' . $adminEmail;

            file_put_contents($lockFile, date('c'));
            $installed = true;
            $messages[] = 'Installation complete. For security, delete install.php now.';
        } catch (Throwable $e) {
            $error = 'Install failed: ' . $e->getMessage();
        }
    }
}
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Install <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head><body>
<div class="auth"><div class="card auth__card">
  <div class="auth__brand"><?= e(APP_NAME) ?></div>
  <div class="auth__sub">Installer</div>

  <?php foreach ($messages as $m): ?><div class="flash flash--success"><?= e($m) ?></div><?php endforeach; ?>
  <?php if ($error): ?><div class="flash flash--error"><?= e($error) ?></div><?php endif; ?>

  <?php if ($installed): ?>
    <p class="muted">The system is installed. Delete <code>install.php</code> and <code>install.lock</code> is in place to prevent re-runs.</p>
    <a class="btn btn--block" href="<?= url('login.php') ?>">Go to login</a>
  <?php else: ?>
    <p class="muted">This creates all tables, seed data, and your admin account. Make sure your database credentials in <code>config/db_config.php</code> are correct first.</p>
    <form method="post" action="<?= url('install.php') ?>">
      <?= csrf_field() ?>
      <div class="field"><label>Admin name</label><input class="input" name="admin_name" value="Site Admin" required></div>
      <div class="field"><label>Admin email</label><input class="input" type="email" name="admin_email" required></div>
      <div class="field"><label>Admin password (min 8)</label><input class="input" type="password" name="admin_pass" required></div>
      <button class="btn btn--block" type="submit">Run installer</button>
    </form>
  <?php endif; ?>
</div></div>
</body></html>
