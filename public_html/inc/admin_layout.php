<?php
declare(strict_types=1);
require_once __DIR__ . '/ui.php';

function admin_layout_start(string $title, array $user, string $current): void
{
    $nav = build_nav([
        ['label' => 'Dashboard',    'href' => 'admin/dashboard.php'],
        ['label' => 'Analytics',    'href' => 'admin/analytics.php'],
        ['label' => 'Users',        'href' => 'admin/users.php'],
        ['label' => 'Subjects',     'href' => 'admin/subjects.php'],
        ['label' => 'Topics',       'href' => 'admin/topics.php'],
        ['label' => 'Skills',       'href' => 'admin/skills.php'],
        ['label' => 'Questions',    'href' => 'admin/questions.php'],
        ['label' => 'Subscriptions', 'href' => 'admin/subscriptions.php'],
        ['label' => 'Schools',      'href' => 'admin/schools.php'],
        ['label' => 'AI Prompts',   'href' => 'admin/ai_prompts.php'],
        ['label' => 'Landing CMS',  'href' => 'admin/landing.php'],
    ], $current);
    dash_header($title, $user, $nav, 'Admin');
}

function admin_layout_end(): void
{
    dash_footer();
}
