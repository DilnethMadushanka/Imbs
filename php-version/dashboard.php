<?php
// =============================================================
// dashboard.php  -  ADMIN DASHBOARD: 4 action cards + total students
// =============================================================
require_once 'includes/auth.php';   // only logged-in admins
require_once 'includes/db.php';

// Count all students
$row   = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) AS total FROM student'));
$total = (int)$row['total'];

$page_title = 'Dashboard';
include 'includes/header.php';
?>
<section class="page">
  <div class="wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow">Signed in as <?= e($_SESSION['admin']) ?></p>
        <h1>Dashboard</h1>
      </div>
      <div class="stat">
        <span class="stat-number"><?= $total ?></span>
        <span class="stat-label">students registered</span>
      </div>
    </div>

    <div class="dash-grid">
      <a class="dash-card" href="register.php">
        <span class="dash-icon" aria-hidden="true">+</span>
        <h2>Register</h2>
        <p>Add a new student and assign a course.</p>
      </a>
      <a class="dash-card" href="search.php">
        <span class="dash-icon" aria-hidden="true">&#8981;</span>
        <h2>Search</h2>
        <p>Find students by NIC or name, or list them all.</p>
      </a>
      <a class="dash-card" href="update.php">
        <span class="dash-icon" aria-hidden="true">&#9998;</span>
        <h2>Update</h2>
        <p>Load a student by NIC and edit their details.</p>
      </a>
      <a class="dash-card dash-card-danger" href="delete.php">
        <span class="dash-icon" aria-hidden="true">&times;</span>
        <h2>Delete</h2>
        <p>Remove a student record after confirming.</p>
      </a>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
