<?php
declare(strict_types=1);
require_once __DIR__ . '/ui.php';

function school_layout_start(string $title, array $user, string $current): void
{
    $nav = build_nav([
        ['label' => 'Dashboard',     'href' => 'school/dashboard.php'],
        ['label' => 'Teachers',      'href' => 'school/teachers.php'],
        ['label' => 'Students',      'href' => 'school/students.php'],
        ['label' => 'Analytics',     'href' => 'school/analytics.php'],
        ['label' => 'Billing',       'href' => 'school/billing.php'],
        ['label' => 'Notifications', 'href' => 'notifications.php'],
    ], $current);
    dash_header($title, $user, $nav, 'School');
}

function school_layout_end(): void
{
    dash_footer();
}
