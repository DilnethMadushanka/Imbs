<?php
// =============================================================
// includes/header.php
// Shared top part of every page: <head>, fonts, CSS and the
// sticky navbar. Each page sets $page_title before including it.
// =============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/icons.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_admin = !empty($_SESSION['admin']);          // logged in?
$current  = basename($_SERVER['PHP_SELF']);      // e.g. "search.php", to highlight the menu link

// Helper: prints class="active" for the current page's link
function active($file, $current) {
    return $file === $current ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Home') ?> | <?= CAMPUS_NAME ?></title>
  <meta name="description" content="<?= CAMPUS_NAME ?> - diplomas, HND and certificate courses in ICT, business, English and design.">
  <meta name="theme-color" content="#14223f">
  <link rel="icon" href="images/favicon.png" type="image/png">
  <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
  <!-- Google Fonts: Fraunces for headings, Manrope for body text -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= $is_admin ? 'is-admin' : 'is-public' ?>">
  <!-- Skip link: lets keyboard users jump straight to the content -->
  <a class="skip-link" href="#main">Skip to content</a>

<?php if ($is_admin): ?>
  <!-- ================= ADMIN LAYOUT: sidebar + content ================= -->
  <?php
    $admin_links = [
        ['dashboard.php', 'Dashboard', 'dashboard'],
        ['register.php',  'Register',  'register'],
        ['search.php',    'Search',    'search'],
        ['update.php',    'Update',    'update'],
        ['delete.php',    'Delete',    'delete'],
    ];
  ?>
  <!-- Top bar: only shown on phones and tablets -->
  <header class="admin-topbar">
    <a class="brand" href="dashboard.php">
      <img src="images/logo.png" alt="" width="36" height="36">
      <span><?= CAMPUS_NAME ?></span>
    </a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="admin-sidebar">
      <span class="bar"></span><span class="bar"></span><span class="bar"></span>
      <span class="sr-only">Menu</span>
    </button>
  </header>

  <!-- Sidebar: fixed on the left on desktop, slides in on phones -->
  <aside id="admin-sidebar" class="sidebar">
    <a class="brand sidebar-brand" href="dashboard.php">
      <img src="images/logo.png" alt="" width="40" height="40">
      <span><?= CAMPUS_NAME ?><small>Admin panel</small></span>
    </a>
    <nav class="side-nav" aria-label="Admin">
      <p class="side-label">Menu</p>
      <?php foreach ($admin_links as $l): ?>
        <a href="<?= $l[0] ?>" class="<?= active($l[0], $current) ?>"><?= icon($l[2]) ?><span><?= $l[1] ?></span></a>
      <?php endforeach; ?>
      <p class="side-label">Website</p>
      <a href="index.php"><?= icon('home') ?><span>View website</span></a>
    </nav>
    <div class="side-user">
      <span class="avatar"><?= strtoupper(substr($_SESSION['admin'], 0, 1)) ?></span>
      <span class="side-user-name"><?= htmlspecialchars($_SESSION['admin']) ?><small>Administrator</small></span>
      <a href="logout.php" class="side-logout" title="Log out"><?= icon('logout') ?><span class="sr-only">Log out</span></a>
    </div>
  </aside>
<?php else: ?>
  <!-- ===== PUBLIC LAYOUT: sticky navigation bar ===== -->
  <header class="navbar">
    <div class="wrap nav-inner">
      <a class="brand" href="index.php">
        <img src="images/logo.png" alt="" width="40" height="40">
        <span><?= CAMPUS_NAME ?></span>
      </a>

      <!-- Menu button: only visible on small screens -->
      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links">
        <span class="bar"></span><span class="bar"></span><span class="bar"></span>
        <span class="sr-only">Menu</span>
      </button>

      <nav id="nav-links" class="nav-links" aria-label="Main">
          <!-- Links for public visitors -->
          <a href="index.php#about">About</a>
          <a href="index.php#courses">Courses</a>
          <a href="index.php#contact">Contact</a>
          <a href="login.php" class="btn btn-small btn-gold">Admin login</a>
      </nav>
    </div>
  </header>

<?php endif; ?>

  <main id="main" class="<?= $is_admin ? 'admin-main' : '' ?>">
