<?php
declare(strict_types=1);
require_once __DIR__ . '/ui.php';

function admin_layout_start(string $title, array $user, string $current): void
{
    $nav = build_nav([
        ['label' => 'Dashboard',     'href' => 'admin/dashboard.php',     'primary' => true, 'icon' => '🏠'],
        ['label' => 'Analytics',     'href' => 'admin/analytics.php',     'primary' => true, 'icon' => '📊'],
        ['label' => 'Users',         'href' => 'admin/users.php',         'primary' => true, 'icon' => '👥'],
        ['label' => 'Subjects',      'href' => 'admin/subjects.php'],
        ['label' => 'Topics',        'href' => 'admin/topics.php'],
        ['label' => 'Skills',        'href' => 'admin/skills.php'],
        ['label' => 'Questions',     'href' => 'admin/questions.php'],
        ['label' => 'Subscriptions', 'href' => 'admin/subscriptions.php'],
        ['label' => 'Schools',       'href' => 'admin/schools.php'],
        ['label' => 'Leads',         'href' => 'admin/leads.php',         'primary' => true, 'icon' => '✉️'],
        ['label' => 'AI Settings',   'href' => 'admin/ai_settings.php'],
        ['label' => 'AI Prompts',    'href' => 'admin/ai_prompts.php'],
        ['label' => 'AI Batch Gen',  'href' => 'admin/ai_batch_generate.php'],
        ['label' => 'Landing CMS',   'href' => 'admin/landing.php'],
        ['label' => 'Seeders',       'href' => 'admin/seeders.php'],
    ], $current);
    dash_header($title, $user, $nav, 'Admin');
}

function admin_layout_end(): void
{
    dash_footer();
}
