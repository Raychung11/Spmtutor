<?php
declare(strict_types=1);
require_once __DIR__ . '/ui.php';

function student_layout_start(string $title, array $user, string $current): void
{
    $nav = build_nav([
        ['label' => 'Dashboard',     'href' => 'student/dashboard.php'],
        ['label' => 'AI Tutor',      'href' => 'student/tutor.php'],
        ['label' => 'Diagnostic',    'href' => 'student/diagnostic.php'],
        ['label' => 'Learning Path', 'href' => 'student/learning_path.php'],
        ['label' => 'Practice',      'href' => 'student/practice.php'],
        ['label' => 'Snap & Check',  'href' => 'student/snap_check.php'],
        ['label' => 'Progress',      'href' => 'student/progress.php'],
        ['label' => 'Notifications', 'href' => 'notifications.php'],
        ['label' => 'Subscription',  'href' => 'student/subscription.php'],
    ], $current);
    dash_header($title, $user, $nav, 'Student');
}

function student_layout_end(): void
{
    dash_footer();
}
