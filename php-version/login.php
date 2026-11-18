<?php
// =============================================================
// login.php  -  ADMIN LOGIN
// Checks the username + password against the "admin" table.
// =============================================================
session_start();
require_once 'includes/db.php';

// Already logged in? Go straight to the dashboard.
if (!empty($_SESSION['admin'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';

// The form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        // Prepared statement: the username is sent separately from the SQL (no SQL injection)
        $stmt = mysqli_prepare($conn, 'SELECT username, password FROM admin WHERE username = ?');
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        // password_verify() compares the typed password with the stored hash
        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);           // new session id = safer login
            $_SESSION['admin'] = $admin['username']; // remember who logged in
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Invalid username or password.';
    }
}

$page_title = 'Admin Login';
include 'includes/header.php';
?>
<section class="page login-page">
  <!-- Corridor photo behind the login card -->
  <img class="login-bg" src="images/corridor.webp" alt="" width="1200" height="675">
  <div class="wrap">
    <div class="login-card" data-enter>
      <h1>Admin login</h1>
      <p class="muted">Sign in to manage student records.</p>

      <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>

      <form method="POST" action="login.php" class="form" novalidate data-validate>
        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?= e($username) ?>" required autocomplete="username">
          <small class="field-error"></small>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required autocomplete="current-password">
          <small class="field-error"></small>
        </div>
        <button type="submit" class="btn btn-gold btn-block">Log in</button>
      </form>
      <a class="back-link" href="index.php">Back to the home page</a>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
