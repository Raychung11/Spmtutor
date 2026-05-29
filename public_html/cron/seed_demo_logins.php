<?php
/**
 * Lightweight demo logins seeder (CLI only). Creates one demo account per
 * role — student, parent, teacher, school admin, platform admin — with
 * predictable credentials and sensible relationships:
 *
 *   - parent linked to student
 *   - teacher owns "Demo Class" containing the student
 *   - school admin owns "Demo School" (active); student & teacher are
 *     enrolled as active members of that school
 *
 * Idempotent — existing accounts and links are kept, missing pieces added.
 *
 *   php public_html/cron/seed_demo_logins.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/classes.php';

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

// Link the parent to the student.
db_exec(
    'INSERT IGNORE INTO parent_student_links (parent_user_id, student_user_id, relationship, status)
     VALUES (?,?,?,?)',
    [$ids['parent'], $ids['student'], 'parent', 'active']
);

// Make sure the teacher has a class that includes the student.
$class = db_one("SELECT id FROM teacher_classes WHERE teacher_user_id = ? AND name = 'Demo Class' LIMIT 1", [$ids['teacher']]);
if ($class) {
    $classId = (int) $class['id'];
} else {
    $math = db_one("SELECT id FROM subjects WHERE slug = 'mathematics'");
    $classId = create_class($ids['teacher'], 'Demo Class', $math ? (int) $math['id'] : null);
}
db_exec(
    'INSERT IGNORE INTO class_students (class_id, student_user_id) VALUES (?, ?)',
    [$classId, $ids['student']]
);

// Demo school owned by the demo school admin.
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

// Enrol student + teacher as active members of the demo school.
foreach ([['role' => 'student', 'uid' => $ids['student']], ['role' => 'teacher', 'uid' => $ids['teacher']]] as $m) {
    db_exec(
        'INSERT INTO school_members (school_id, user_id, member_role, status) VALUES (?,?,?,?)
         ON DUPLICATE KEY UPDATE status = "active"',
        [$schoolId, $m['uid'], $m['role'], 'active']
    );
}

echo "Demo logins ready (password: {$password})\n";
echo str_repeat('-', 70) . "\n";
foreach ($results as $r) {
    printf("  [%s] %-13s %s\n", $r['action'], $r['role'], $r['email']);
}
echo str_repeat('-', 70) . "\n";
echo "Relationships:\n";
echo "  - Parent linked to Student\n";
echo "  - Teacher owns 'Demo Class' containing Student\n";
echo "  - School Admin owns 'Demo School' (active)\n";
echo "  - Student + Teacher are active members of Demo School\n";
