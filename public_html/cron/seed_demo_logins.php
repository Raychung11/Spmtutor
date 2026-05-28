<?php
/**
 * Lightweight role demo seeder (CLI only). Creates one demo account per
 * role — student, parent, teacher — with predictable credentials and
 * sensible relationships (parent linked to student; teacher with a class
 * containing the student). Idempotent: existing accounts are left in
 * place, missing pieces are added.
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
    ['role' => 'student', 'name' => 'Demo Student', 'email' => 'student@demo.lulusai.my'],
    ['role' => 'parent',  'name' => 'Demo Parent',  'email' => 'parent@demo.lulusai.my'],
    ['role' => 'teacher', 'name' => 'Demo Teacher', 'email' => 'teacher@demo.lulusai.my'],
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

// If at least one active school exists, enrol the student there (active).
$school = db_one("SELECT id FROM schools WHERE status = 'active' ORDER BY id LIMIT 1");
if ($school) {
    db_exec(
        "INSERT IGNORE INTO school_members (school_id, user_id, member_role, status)
         VALUES (?, ?, 'student', 'active')",
        [(int) $school['id'], $ids['student']]
    );
}

echo "Demo logins ready (password: {$password})\n";
echo str_repeat('-', 60) . "\n";
foreach ($results as $r) {
    printf("  [%s] %-7s %s\n", $r['action'], $r['role'], $r['email']);
}
echo str_repeat('-', 60) . "\n";
echo "Relationships:\n";
echo "  - Parent linked to Student\n";
echo "  - Teacher owns 'Demo Class' containing Student\n";
if ($school) {
    echo "  - Student enrolled in school id #" . (int) $school['id'] . "\n";
}
