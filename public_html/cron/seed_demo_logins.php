<?php
/**
 * Demo logins seeder. Creates one demo account per role with predictable
 * credentials and sensible relationships. Idempotent.
 *
 * Run from CLI:   php public_html/cron/seed_demo_logins.php
 * Or from the admin panel: Admin → Seeders → "Demo logins".
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/classes.php';

function seed_demo_logins_run(): array
{
    $password = 'Demo@123';
    $accounts = [
        ['role' => 'student',      'name' => 'Demo Student',      'email' => 'student@demo.lulusai.my'],
        ['role' => 'parent',       'name' => 'Demo Parent',       'email' => 'parent@demo.lulusai.my'],
        ['role' => 'teacher',      'name' => 'Demo Teacher',      'email' => 'teacher@demo.lulusai.my'],
        ['role' => 'school_admin', 'name' => 'Demo School Admin', 'email' => 'school@demo.lulusai.my'],
        ['role' => 'admin',        'name' => 'Demo Admin',        'email' => 'admin@demo.lulusai.my'],
    ];

    $results = [];
    $ids = [];
    foreach ($accounts as $a) {
        $existing = db_one('SELECT id FROM users WHERE email = ?', [$a['email']]);
        if ($existing) {
            $ids[$a['role']] = (int) $existing['id'];
            $results[] = ['action' => 'kept', 'role' => $a['role'], 'email' => $a['email']];
        } else {
            $ids[$a['role']] = register_user($a['name'], $a['email'], $password, $a['role']);
            $results[] = ['action' => 'created', 'role' => $a['role'], 'email' => $a['email']];
        }
    }

    db_exec(
        'INSERT IGNORE INTO parent_student_links (parent_user_id, student_user_id, relationship, status)
         VALUES (?,?,?,?)',
        [$ids['parent'], $ids['student'], 'parent', 'active']
    );

    $class = db_one("SELECT id FROM teacher_classes WHERE teacher_user_id = ? AND name = 'Demo Class' LIMIT 1", [$ids['teacher']]);
    if ($class) {
        $classId = (int) $class['id'];
    } else {
        $math = db_one("SELECT id FROM subjects WHERE slug = 'mathematics'");
        $classId = create_class($ids['teacher'], 'Demo Class', $math ? (int) $math['id'] : null);
    }
    db_exec('INSERT IGNORE INTO class_students (class_id, student_user_id) VALUES (?, ?)', [$classId, $ids['student']]);

    $school = db_one('SELECT id FROM schools WHERE owner_user_id = ? LIMIT 1', [$ids['school_admin']]);
    if ($school) {
        $schoolId = (int) $school['id'];
        db_exec('UPDATE schools SET status = "active" WHERE id = ?', [$schoolId]);
    } else {
        $schoolId = db_exec(
            'INSERT INTO schools (name, type, owner_user_id, contact_email, status) VALUES (?,?,?,?,?)',
            ['Demo School', 'learning_center', $ids['school_admin'], 'school@demo.lulusai.my', 'active']
        );
    }
    foreach ([['role' => 'student', 'uid' => $ids['student']], ['role' => 'teacher', 'uid' => $ids['teacher']]] as $m) {
        db_exec(
            'INSERT INTO school_members (school_id, user_id, member_role, status) VALUES (?,?,?,?)
             ON DUPLICATE KEY UPDATE status = "active"',
            [$schoolId, $m['uid'], $m['role'], 'active']
        );
    }

    return ['password' => $password, 'accounts' => $results];
}

if (PHP_SAPI === 'cli') {
    $r = seed_demo_logins_run();
    echo "Demo logins ready (password: {$r['password']})\n";
    echo str_repeat('-', 70) . "\n";
    foreach ($r['accounts'] as $a) {
        printf("  [%s] %-13s %s\n", $a['action'], $a['role'], $a['email']);
    }
    echo str_repeat('-', 70) . "\n";
    echo "Relationships: parent<->student, teacher class with student,\n";
    echo "                school admin owns 'Demo School', student+teacher active members.\n";
}
