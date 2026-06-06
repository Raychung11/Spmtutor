<?php
/**
 * Class & assignment management for teachers / learning centers.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/notifications.php';

function create_class(int $teacherId, string $name, ?int $subjectId, ?int $schoolId = null): int
{
    return db_exec(
        'INSERT INTO teacher_classes (teacher_user_id, school_id, name, subject_id) VALUES (?,?,?,?)',
        [$teacherId, $schoolId, $name, $subjectId]
    );
}

function teacher_classes(int $teacherId): array
{
    return db_all(
        'SELECT c.*, s.name AS subject,
                (SELECT COUNT(*) FROM class_students cs WHERE cs.class_id = c.id) AS student_count
         FROM teacher_classes c LEFT JOIN subjects s ON s.id = c.subject_id
         WHERE c.teacher_user_id = ? ORDER BY c.id DESC',
        [$teacherId]
    );
}

function teacher_owns_class(int $teacherId, int $classId): bool
{
    return (bool) db_one('SELECT 1 FROM teacher_classes WHERE id = ? AND teacher_user_id = ?', [$classId, $teacherId]);
}

/** Add a student to a class by email. Returns [ok, message]. */
function add_student_to_class(int $classId, string $email): array
{
    $student = db_one("SELECT id, name FROM users WHERE email = ? AND role = 'student'", [$email]);
    if (!$student) {
        return [false, 'No student found with that email.'];
    }
    try {
        db_exec('INSERT INTO class_students (class_id, student_user_id) VALUES (?,?)', [$classId, $student['id']]);
        notify((int) $student['id'], 'Added to a class', 'You were added to a class by your teacher.', 'class');
        return [true, $student['name'] . ' added.'];
    } catch (Throwable $e) {
        return [false, 'That student is already in this class.'];
    }
}

function class_students(int $classId): array
{
    return db_all(
        'SELECT u.id, u.name, u.email,
                COALESCE(ps.total_questions,0) answered, COALESCE(ps.avg_score,0) avg_score,
                COALESCE(st.current_streak,0) streak
         FROM class_students cs JOIN users u ON u.id = cs.student_user_id
         LEFT JOIN student_progress_summary ps ON ps.user_id = u.id
         LEFT JOIN student_streaks st ON st.user_id = u.id
         WHERE cs.class_id = ? ORDER BY u.name',
        [$classId]
    );
}

function create_assignment(int $classId, string $title, ?string $description, ?string $dueDate): int
{
    $id = db_exec(
        'INSERT INTO assignments (class_id, title, description, due_date) VALUES (?,?,?,?)',
        [$classId, $title, $description ?: null, $dueDate ?: null]
    );
    // Notify all class students.
    foreach (db_all('SELECT student_user_id FROM class_students WHERE class_id = ?', [$classId]) as $row) {
        notify((int) $row['student_user_id'], 'New assignment: ' . $title, $description, 'assignment');
    }
    return $id;
}

function class_assignments(int $classId): array
{
    return db_all(
        'SELECT a.*, (SELECT COUNT(*) FROM assignment_submissions s WHERE s.assignment_id = a.id) AS submissions
         FROM assignments a WHERE a.class_id = ? ORDER BY a.id DESC',
        [$classId]
    );
}

/** Assignments visible to a student (across their classes), with submission state. */
function student_assignments(int $studentId): array
{
    return db_all(
        'SELECT a.*, c.name AS class_name,
                s.id AS submission_id, s.status AS submission_status, s.score
         FROM class_students cs
         JOIN assignments a ON a.class_id = cs.class_id
         JOIN teacher_classes c ON c.id = a.class_id
         LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.student_user_id = cs.student_user_id
         WHERE cs.student_user_id = ? ORDER BY a.due_date IS NULL, a.due_date ASC, a.id DESC',
        [$studentId]
    );
}

function submit_assignment(int $assignmentId, int $studentId, string $content, ?string $filePath): bool
{
    // Verify the student belongs to the assignment's class.
    $ok = db_one(
        'SELECT 1 FROM assignments a JOIN class_students cs ON cs.class_id = a.class_id
         WHERE a.id = ? AND cs.student_user_id = ?',
        [$assignmentId, $studentId]
    );
    if (!$ok) {
        return false;
    }
    $existing = db_one('SELECT id FROM assignment_submissions WHERE assignment_id = ? AND student_user_id = ?', [$assignmentId, $studentId]);
    if ($existing) {
        db_exec('UPDATE assignment_submissions SET content = ?, file_path = ?, status = "submitted" WHERE id = ?', [$content, $filePath, $existing['id']]);
    } else {
        db_exec(
            'INSERT INTO assignment_submissions (assignment_id, student_user_id, content, file_path, status) VALUES (?,?,?,?,?)',
            [$assignmentId, $studentId, $content, $filePath, 'submitted']
        );
    }
    return true;
}
