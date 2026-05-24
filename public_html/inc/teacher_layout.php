<?php
declare(strict_types=1);
require_once __DIR__ . '/ui.php';

function teacher_layout_start(string $title, array $user, string $current): void
{
    $nav = build_nav([
        ['label' => 'Dashboard', 'href' => 'teacher/dashboard.php'],
    ], $current);
    dash_header($title, $user, $nav, 'Teacher');
}

function teacher_layout_end(): void
{
    dash_footer();
}
