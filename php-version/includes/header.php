<?php
// =============================================================
// includes/header.php
// Shared top part of every page: <head>, fonts, CSS and the
// sticky navbar. Each page sets $page_title before including it.
// =============================================================
require_once __DIR__ . '/config.php';
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

  <!-- ===== Sticky navigation bar ===== -->
  <header class="navbar">
    <div class="wrap nav-inner">
      <a class="brand" href="<?= $is_admin ? 'dashboard.php' : 'index.php' ?>">
        <img src="images/logo.png" alt="" width="40" height="40">
        <span><?= CAMPUS_NAME ?></span>
      </a>

      <!-- Menu button: only visible on small screens -->
      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links">
        <span class="bar"></span><span class="bar"></span><span class="bar"></span>
        <span class="sr-only">Menu</span>
      </button>

      <nav id="nav-links" class="nav-links" aria-label="Main">
        <?php if ($is_admin): ?>
          <!-- Links for a logged-in admin -->
          <a href="dashboard.php" class="<?= active('dashboard.php', $current) ?>">Dashboard</a>
          <a href="register.php"  class="<?= active('register.php', $current) ?>">Register</a>
          <a href="search.php"    class="<?= active('search.php', $current) ?>">Search</a>
          <a href="update.php"    class="<?= active('update.php', $current) ?>">Update</a>
          <a href="delete.php"    class="<?= active('delete.php', $current) ?>">Delete</a>
          <a href="logout.php" class="btn btn-small btn-outline">Log out</a>
        <?php else: ?>
          <!-- Links for public visitors -->
          <a href="index.php#about">About</a>
          <a href="index.php#courses">Courses</a>
          <a href="index.php#contact">Contact</a>
          <a href="login.php" class="btn btn-small btn-gold">Admin login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main id="main">
