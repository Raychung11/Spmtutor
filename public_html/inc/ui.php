<?php
/**
 * Shared UI rendering: HTML head, flash messages, dashboard shell with sidebar.
 */
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/notifications.php';

function render_head(string $title): void
{
    $name = APP_NAME;
    // Cache-bust assets on file modification so users never get a stale CSS/JS.
    $cssVer = @filemtime(APP_ROOT . '/assets/css/style.css') ?: time();
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> &middot; <?= e($name) ?></title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>?v=<?= $cssVer ?>">
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
    $primary = array_values(array_filter($nav, fn($i) => !empty($i['primary'])));
    ?>
<div class="app">
  <aside class="sidebar" id="sidebar" aria-label="Main navigation">
    <a class="brand" href="<?= url('') ?>"><?= e(APP_NAME) ?></a>
    <div class="sidebar__panel"><?= e($panelLabel) ?></div>
    <nav class="sidebar__nav">
      <?php foreach ($nav as $item): ?>
        <a class="navlink<?= !empty($item['active']) ? ' navlink--active' : '' ?>" href="<?= url($item['href']) ?>"><?= e($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>
    <a class="navlink navlink--logout" href="<?= url('logout.php') ?>">Log out</a>
  </aside>
  <div class="drawer-backdrop" data-nav-close aria-hidden="true"></div>
  <main class="content">
    <header class="topbar">
      <button class="topbar__toggle" type="button" aria-label="Open menu" aria-controls="sidebar" data-nav-toggle>
        <span></span><span></span><span></span>
      </button>
      <h1 class="topbar__title"><?= e($title) ?></h1>
      <div class="topbar__user">
        <?php $unread = unread_count((int) $user['id']); ?>
        <a class="bell" href="<?= url('notifications.php') ?>" title="Notifications">
          🔔<?php if ($unread): ?><span class="bell__badge"><?= $unread > 9 ? '9+' : $unread ?></span><?php endif; ?>
        </a>
        <span class="avatar"><?= e(strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
        <span class="topbar__name"><?= e($user['name']) ?></span>
      </div>
    </header>
    <div class="page">
      <?php render_flashes(); ?>
<?php
    if ($primary) {
        ?>
<nav class="botnav" aria-label="Quick navigation">
  <?php foreach ($primary as $item): ?>
    <a class="botnav__link<?= !empty($item['active']) ? ' botnav__link--active' : '' ?>" href="<?= url($item['href']) ?>">
      <span class="botnav__icon" aria-hidden="true"><?= e($item['icon'] ?? '•') ?></span>
      <span class="botnav__label"><?= e($item['label']) ?></span>
    </a>
  <?php endforeach; ?>
</nav>
<?php
    }
}

function dash_footer(): void
{
    $jsVer = @filemtime(APP_ROOT . '/assets/js/app.js') ?: time();
    ?>
    </div>
  </main>
</div>
<script src="<?= url('assets/js/app.js') ?>?v=<?= $jsVer ?>"></script>
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
