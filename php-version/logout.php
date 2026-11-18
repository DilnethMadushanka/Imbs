<?php
// =============================================================
// logout.php  -  ends the admin session and goes back to login
// =============================================================
session_start();
$_SESSION = [];          // clear all session data
session_destroy();       // delete the session
header('Location: login.php');
exit;
