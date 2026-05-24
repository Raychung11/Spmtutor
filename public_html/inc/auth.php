<?php
/**
 * Authentication & role guards.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/ratelimit.php';

function current_user(): ?array
{
    boot_session();
    if (empty($_SESSION['uid'])) {
        return null;
    }
    static $cache = null;
    if ($cache !== null && $cache['id'] === $_SESSION['uid']) {
        return $cache;
    }
    $user = db_one('SELECT id, name, email, role, status FROM users WHERE id = ?', [$_SESSION['uid']]);
    if (!$user || $user['status'] !== 'active') {
        logout();
        return null;
    }
    return $cache = $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

/** True while the email/IP is locked out due to repeated failures. */
function login_locked(string $email): bool
{
    $cutoff = date('Y-m-d H:i:s', strtotime('-' . LOGIN_LOCKOUT_MINUTES . ' minutes'));
    $fails  = (int) (db_one(
        'SELECT COUNT(*) c FROM login_logs
         WHERE result = "failed" AND created_at >= ? AND (email = ? OR ip_address = ?)',
        [$cutoff, $email, $_SERVER['REMOTE_ADDR'] ?? '']
    )['c'] ?? 0);
    return $fails >= LOGIN_MAX_ATTEMPTS;
}

function attempt_login(string $email, string $password): bool
{
    if (login_locked($email)) {
        flash('error', 'Too many failed attempts. Please try again in ' . LOGIN_LOCKOUT_MINUTES . ' minutes.');
        return false;
    }

    $user = db_one('SELECT * FROM users WHERE email = ?', [$email]);
    $ok   = $user && password_verify($password, $user['password_hash']);

    db_exec(
        'INSERT INTO login_logs (user_id, email, ip_address, user_agent, result) VALUES (?,?,?,?,?)',
        [
            $user['id'] ?? null,
            $email,
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            $ok ? 'success' : 'failed',
        ]
    );

    if (!$ok) {
        return false;
    }
    if ($user['status'] !== 'active') {
        flash('error', 'Your account is ' . $user['status'] . '. Please contact support.');
        return false;
    }

    boot_session();
    session_regenerate_id(true);
    $_SESSION['uid'] = (int) $user['id'];
    db_exec('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
    return true;
}

function logout(): void
{
    boot_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_login(): array
{
    $u = current_user();
    if (!$u) {
        flash('error', 'Please log in to continue.');
        redirect('login.php');
    }
    return $u;
}

/** Require one of the given roles, else 403. */
function require_role(string ...$roles): array
{
    $u = require_login();
    if (!in_array($u['role'], $roles, true)) {
        http_response_code(403);
        exit('403 - You do not have access to this page.');
    }
    return $u;
}

/** Where to send a user after login based on role. */
function dashboard_for(string $role): string
{
    return match ($role) {
        'admin'        => 'admin/dashboard.php',
        'teacher'      => 'teacher/dashboard.php',
        'parent'       => 'parent/dashboard.php',
        'creator'      => 'admin/dashboard.php',
        'school_admin' => 'school/dashboard.php',
        default        => 'student/dashboard.php',
    };
}

/** Register a new user; returns user id or throws on duplicate email. */
function register_user(string $name, string $email, string $password, string $role, ?string $phone = null): int
{
    $exists = db_one('SELECT id FROM users WHERE email = ?', [$email]);
    if ($exists) {
        throw new RuntimeException('An account with that email already exists.');
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $uid  = db_exec(
        'INSERT INTO users (name, email, phone, password_hash, role, status) VALUES (?,?,?,?,?,?)',
        [$name, $email, $phone ?: null, $hash, $role, 'active']
    );

    // Create the role-specific profile row.
    switch ($role) {
        case 'student':
            db_exec('INSERT INTO student_profiles (user_id) VALUES (?)', [$uid]);
            db_exec('INSERT INTO student_streaks (user_id) VALUES (?)', [$uid]);
            db_exec('INSERT INTO student_progress_summary (user_id) VALUES (?)', [$uid]);
            // Auto-start free trial.
            $plan = db_one("SELECT id, trial_days FROM subscription_plans WHERE code = 'free_trial'");
            if ($plan) {
                $ends = date('Y-m-d H:i:s', strtotime('+' . (int) $plan['trial_days'] . ' days'));
                db_exec(
                    'INSERT INTO user_subscriptions (user_id, plan_id, status, ends_at) VALUES (?,?,?,?)',
                    [$uid, $plan['id'], 'trialing', $ends]
                );
            }
            break;
        case 'parent':
            db_exec('INSERT INTO parent_profiles (user_id) VALUES (?)', [$uid]);
            break;
        case 'teacher':
            db_exec('INSERT INTO teacher_profiles (user_id) VALUES (?)', [$uid]);
            break;
    }

    return $uid;
}
