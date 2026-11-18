<?php
// =============================================================
// includes/auth.php
// Put at the TOP of every admin page. If the admin is not logged
// in, they are sent to the login page.
// =============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
