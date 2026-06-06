<?php
declare(strict_types=1);
require_once __DIR__ . '/ui.php';

function parent_layout_start(string $title, array $user, string $current): void
{
    $nav = build_nav([
        ['label' => 'Dashboard',     'href' => 'parent/dashboard.php', 'primary' => true, 'icon' => '🏠'],
        ['label' => 'Notifications', 'href' => 'notifications.php',    'primary' => true, 'icon' => '🔔'],
        ['label' => 'Pricing',       'href' => 'pricing.php',          'primary' => true, 'icon' => '💳'],
    ], $current);
    dash_header($title, $user, $nav, 'Parent');
}

function parent_layout_end(): void
{
    dash_footer();
}
