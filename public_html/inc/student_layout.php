<?php
declare(strict_types=1);
require_once __DIR__ . '/ui.php';

function student_layout_start(string $title, array $user, string $current): void
{
    $nav = build_nav([
        ['label' => 'Dashboard',     'href' => 'student/dashboard.php',     'primary' => true,  'icon' => '🏠'],
        ['label' => 'AI Tutor',      'href' => 'student/tutor.php',         'primary' => true,  'icon' => '💬'],
        ['label' => 'Diagnostic',    'href' => 'student/diagnostic.php'],
        ['label' => 'Learning Path', 'href' => 'student/learning_path.php'],
        ['label' => 'Practice',      'href' => 'student/practice.php',      'primary' => true,  'icon' => '✏️'],
        ['label' => 'Library',       'href' => 'student/library.php',       'primary' => true,  'icon' => '📚'],
        ['label' => 'Writing Marker','href' => 'student/writing.php',       'primary' => true,  'icon' => '✍️'],
        ['label' => 'Snap & Check',  'href' => 'student/snap_check.php'],
        ['label' => 'Assignments',   'href' => 'student/assignments.php'],
        ['label' => 'Progress',      'href' => 'student/progress.php',      'primary' => true,  'icon' => '📈'],
        ['label' => 'Notifications', 'href' => 'notifications.php'],
        ['label' => 'Subscription',  'href' => 'student/subscription.php'],
        ['label' => 'API Access',    'href' => 'student/api_tokens.php'],
    ], $current);
    dash_header($title, $user, $nav, 'Student');
}

function student_layout_end(): void
{
    dash_footer();
}
