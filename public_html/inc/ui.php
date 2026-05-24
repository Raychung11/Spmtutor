<?php
/**
 * Shared UI rendering: HTML head, flash messages, dashboard shell with sidebar.
 */
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function render_head(string $title): void
{
    $name = APP_NAME;
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> &middot; <?= e($name) ?></title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body><?php
}

function render_flashes(): void
{
    foreach (take_flashes() as $f) {
        $cls = match ($f['type']) {
            'success' => 'flash flash--success',
            'error'   => 'flash flash--error',
            default   => 'flash flash--info',
        };
        echo '<div class="' . $cls . '">' . e($f['message']) . '</div>';
    }
}

/**
 * Render the start of a dashboard shell (sidebar + topbar).
 *
 * @param array $nav  List of ['label'=>, 'href'=>, 'active'=>bool].
 */
function dash_header(string $title, array $user, array $nav, string $panelLabel): void
{
    render_head($title);
    ?>
<div class="app">
  <aside class="sidebar">
    <a class="brand" href="<?= url('') ?>"><?= e(APP_NAME) ?></a>
    <div class="sidebar__panel"><?= e($panelLabel) ?></div>
    <nav class="sidebar__nav">
      <?php foreach ($nav as $item): ?>
        <a class="navlink<?= !empty($item['active']) ? ' navlink--active' : '' ?>" href="<?= url($item['href']) ?>"><?= e($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>
    <a class="navlink navlink--logout" href="<?= url('logout.php') ?>">Log out</a>
  </aside>
  <main class="content">
    <header class="topbar">
      <h1 class="topbar__title"><?= e($title) ?></h1>
      <div class="topbar__user">
        <span class="avatar"><?= e(strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
        <span class="topbar__name"><?= e($user['name']) ?></span>
      </div>
    </header>
    <div class="page">
      <?php render_flashes(); ?>
<?php
}

function dash_footer(): void
{
    ?>
    </div>
  </main>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html><?php
}

/** Build a nav array, marking the current file active. */
function build_nav(array $items, string $current): array
{
    foreach ($items as &$i) {
        $i['active'] = (basename($i['href']) === $current);
    }
    return $items;
}
