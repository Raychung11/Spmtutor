<?php
declare(strict_types=1);
require_once __DIR__ . '/ui.php';

function parent_layout_start(string $title, array $user, string $current): void
{
    $nav = build_nav([
        ['label' => 'Dashboard',     'href' => 'parent/dashboard.php'],
        ['label' => 'Notifications', 'href' => 'notifications.php'],
        ['label' => 'Pricing',       'href' => 'pricing.php'],
    ], $current);
    dash_header($title, $user, $nav, 'Parent');
}

function parent_layout_end(): void
{
    dash_footer();
}
