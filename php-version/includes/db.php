<?php
// =============================================================
// includes/db.php
// Opens ONE connection to the MySQL database using mysqli.
// Every page that needs the database includes this file.
// =============================================================
require_once __DIR__ . '/config.php';

// Make mysqli throw errors instead of failing silently
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($conn, 'utf8mb4');   // supports all characters
} catch (mysqli_sql_exception $e) {
    // Friendly message if WAMP / MySQL is not running or the DB was not imported
    die('<p style="font-family:sans-serif;padding:24px">Could not connect to the database. '
      . 'Make sure WAMP is running (green icon) and campus_db.sql was imported in phpMyAdmin.</p>');
}

// Small helper: safe output of any text (stops HTML/JS injection)
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}
