<?php
/**
 * Demo data seeder (CLI only). Creates sample students, attempts,
 * subscriptions, a class, assignments, snap & check uploads and parent
 * reports so dashboards and analytics look populated.
 *
 *   php public_html/cron/seed_demo.php          # seed once
 *   php public_html/cron/seed_demo.php --force   # seed again
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/progress.php';
require_once __DIR__ . '/../inc/billing.php';
require_once __DIR__ . '/../inc/classes.php';
require_once __DIR__ . '/../inc/marking.php';
require_once __DIR__ . '/../inc/reports.php';

$force = in_array('--force', $argv, true);

$marker = db_one("SELECT setting_value FROM site_settings WHERE setting_key = 'demo_seeded'");
if ($marker && $marker['setting_value'] === '1' && !$force) {
    echo "Demo data already seeded. Use --force to seed again.\n";
    exit;
}

$pw    = 'Demo@123';
$names = ['Aiman Demo', 'Bella Demo', 'Chong Demo', 'Divya Demo', 'Ethan Demo', 'Farah Demo'];
$questions = db_all('SELECT id, subject_id, topic_id, marks FROM questions WHERE status = "active"');
if (!$questions) {
    exit("No questions found — run install / content.sql first.\n");
}

$spm = db_one("SELECT id FROM education_levels WHERE slug = 'spm'")['id'] ?? null;
$studentIds = [];

foreach ($names as $i => $name) {
    $email = 'student' . ($i + 1) . '@demo.lulusai.my';
    $existing = db_one('SELECT id FROM users WHERE email = ?', [$email]);
    if ($existing) {
        $uid = (int) $existing['id'];
    } else {
        $uid = register_user($name, $email, $pw, 'student', '60' . random_int(100000000, 199999999));
        if ($spm) {
            db_exec('UPDATE student_profiles SET education_level_id = ? WHERE user_id = ?', [$spm, $uid]);
        }
    }
    $studentIds[] = $uid;

    // Spread signup date across the last 14 days for the analytics chart.
    db_exec('UPDATE users SET created_at = DATE_SUB(NOW(), INTERVAL ? DAY) WHERE id = ?', [random_int(0, 13), $uid]);

    // Simulate today's practice so rollups (stats, streak, XP) populate.
    $n = random_int(8, 20);
    for ($k = 0; $k < $n; $k++) {
        $q = $questions[array_rand($questions)];
        $full = db_one('SELECT * FROM questions WHERE id = ?', [$q['id']]);
        $correct = random_int(0, 100) < 65; // ~65% correct
        record_attempt($uid, $full, null, null, $correct);
    }

    // Backdated raw attempts to enrich the 14-day chart.
    for ($d = 1; $d <= 13; $d++) {
        if (random_int(0, 100) < 55) {
            $q = $questions[array_rand($questions)];
            db_exec(
                'INSERT INTO question_attempts (user_id, question_id, is_correct, score, created_at)
                 VALUES (?,?,?,?, DATE_SUB(NOW(), INTERVAL ? DAY))',
                [$uid, $q['id'], random_int(0, 1), 0, $d]
            );
        }
    }
}

// Subscriptions: put a couple of students on the monthly plan (demo payment).
$monthly = get_plan('monthly');
if ($monthly) {
    foreach (array_slice($studentIds, 0, 2) as $uid) {
        $txn = db_exec(
            'INSERT INTO payment_transactions (user_id, gateway, gateway_ref, amount, currency, status) VALUES (?,?,?,?,?,?)',
            [$uid, 'billplz', 'DEMO', (float) $monthly['price'], $monthly['currency'], 'paid']
        );
        $sub = activate_subscription($uid, $monthly, $txn);
        db_exec('UPDATE payment_transactions SET subscription_id = ? WHERE id = ?', [$sub, $txn]);
    }
}

// Parent linked to the first two students.
$parentEmail = 'parent@demo.lulusai.my';
$parent = db_one('SELECT id FROM users WHERE email = ?', [$parentEmail]);
$parentId = $parent ? (int) $parent['id'] : register_user('Parent Demo', $parentEmail, $pw, 'parent');
foreach (array_slice($studentIds, 0, 2) as $uid) {
    db_exec('INSERT IGNORE INTO parent_student_links (parent_user_id, student_user_id, relationship, status) VALUES (?,?,?,?)', [$parentId, $uid, 'parent', 'active']);
    generate_weekly_report($uid);
}

// Teacher + class + assignment.
$teacherEmail = 'teacher@demo.lulusai.my';
$teacher = db_one('SELECT id FROM users WHERE email = ?', [$teacherEmail]);
$teacherId = $teacher ? (int) $teacher['id'] : register_user('Teacher Demo', $teacherEmail, $pw, 'teacher');
$mathId = db_one("SELECT id FROM subjects WHERE slug = 'mathematics'")['id'] ?? null;
$classId = create_class($teacherId, 'SPM Maths — Demo Class', $mathId ? (int) $mathId : null);
foreach ($studentIds as $uid) {
    db_exec('INSERT IGNORE INTO class_students (class_id, student_user_id) VALUES (?,?)', [$classId, $uid]);
}
create_assignment($classId, 'Algebra practice set 1', 'Complete questions 1–10 on linear equations.', date('Y-m-d', strtotime('+7 days')));

// Snap & Check upload + AI marking for the first student.
$firstQ = $questions[0]['id'];
$uploadId = store_answer_upload($studentIds[0], (int) $firstQ, [], 'x = 4 because 2x + 3 = 11 gives 2x = 8.');
if ($uploadId) {
    run_ai_marking($uploadId);
}

db_exec(
    "INSERT INTO site_settings (setting_key, setting_value) VALUES ('demo_seeded','1')
     ON DUPLICATE KEY UPDATE setting_value = '1'"
);

echo 'Demo data seeded: ' . count($studentIds) . " students, 1 parent, 1 teacher, 1 class.\n";
echo "Logins — students: student1@demo.lulusai.my .. student6@... | parent@demo... | teacher@demo... (password: {$pw})\n";
