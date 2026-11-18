<?php
// =============================================================
// dashboard.php  -  ADMIN DASHBOARD
// Stat tiles, students per course, recent registrations and quick actions.
// =============================================================
require_once 'includes/auth.php';   // only logged-in admins
require_once 'includes/db.php';

// ---- Numbers for the stat tiles (one query each, easy to explain) ----
$total = (int)mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM student'))[0];
$course_count = (int)mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM course'))[0];
$this_month = (int)mysqli_fetch_row(mysqli_query($conn,
    'SELECT COUNT(*) FROM student
     WHERE YEAR(reg_date) = YEAR(CURDATE()) AND MONTH(reg_date) = MONTH(CURDATE())'))[0];
$male = (int)mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM student WHERE gender = 'Male'"))[0];
$female = $total - $male;

// ---- Students per course: LEFT JOIN keeps courses with 0 students ----
$per_course = mysqli_fetch_all(mysqli_query($conn,
    'SELECT c.course_name, COUNT(s.nic) AS total
     FROM course c LEFT JOIN student s ON s.course_id = c.course_id
     GROUP BY c.course_id, c.course_name
     ORDER BY c.course_id'), MYSQLI_ASSOC);
$max_count = max(1, ...array_map('intval', array_column($per_course, 'total')));

// ---- 5 most recently registered students ----
$recent = mysqli_fetch_all(mysqli_query($conn,
    'SELECT s.name, s.reg_date, c.course_name
     FROM student s JOIN course c ON c.course_id = s.course_id
     ORDER BY s.reg_date DESC LIMIT 5'), MYSQLI_ASSOC);

$page_title = 'Dashboard';
include 'includes/header.php';
?>
<section class="page">
  <div class="wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow"><?= date('l, j F Y') ?></p>
        <h1>Welcome back, <?= e($_SESSION['admin']) ?></h1>
      </div>
      <a class="btn btn-gold" href="register.php"><?= icon('register') ?> Register student</a>
    </div>

    <!-- ===== Stat tiles ===== -->
    <div class="stat-grid">
      <div class="stat-tile stat-tile-main">
        <span class="stat-icon"><?= icon('users') ?></span>
        <span class="stat-value"><?= $total ?></span>
        <span class="stat-name">Students registered</span>
      </div>
      <div class="stat-tile">
        <span class="stat-icon"><?= icon('book') ?></span>
        <span class="stat-value"><?= $course_count ?></span>
        <span class="stat-name">Courses offered</span>
      </div>
      <div class="stat-tile">
        <span class="stat-icon"><?= icon('calendar') ?></span>
        <span class="stat-value"><?= $this_month ?></span>
        <span class="stat-name">Registered this month</span>
      </div>
      <div class="stat-tile">
        <span class="stat-icon"><?= icon('gender') ?></span>
        <span class="stat-value"><?= $male ?><span class="stat-sep">/</span><?= $female ?></span>
        <span class="stat-name">Male / Female</span>
      </div>
    </div>

    <div class="dash-panels">
      <!-- ===== Students per course: one bar per course ===== -->
      <section class="panel">
        <div class="panel-head">
          <h2>Students per course</h2>
        </div>
        <ul class="bar-list">
          <?php foreach ($per_course as $c): $n = (int)$c['total']; ?>
            <li title="<?= e($c['course_name']) ?>: <?= $n ?> <?= $n === 1 ? 'student' : 'students' ?>">
              <div class="bar-label"><span><?= e($c['course_name']) ?></span><strong><?= $n ?></strong></div>
              <div class="bar-track"><span class="bar-fill" style="width: <?= round($n / $max_count * 100) ?>%"></span></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>

      <!-- ===== Most recent registrations ===== -->
      <section class="panel">
        <div class="panel-head">
          <h2>Recently registered</h2>
          <a class="text-link" href="search.php">View all <?= icon('arrow') ?></a>
        </div>
        <?php if ($recent): ?>
          <ul class="recent-list">
            <?php foreach ($recent as $s): ?>
              <li>
                <span class="avatar"><?= e(initials($s['name'])) ?></span>
                <span class="recent-text">
                  <strong><?= e($s['name']) ?></strong>
                  <small><?= e($s['course_name']) ?></small>
                </span>
                <span class="recent-date"><?= date('j M', strtotime($s['reg_date'])) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="muted">No students yet. <a href="register.php">Register the first one</a>.</p>
        <?php endif; ?>
      </section>
    </div>

    <!-- ===== Quick actions ===== -->
    <h2 class="section-title">Quick actions</h2>
    <div class="dash-grid">
      <a class="dash-card" href="register.php">
        <span class="dash-icon"><?= icon('register') ?></span>
        <h3>Register</h3>
        <p>Add a new student and assign a course.</p>
      </a>
      <a class="dash-card" href="search.php">
        <span class="dash-icon"><?= icon('search') ?></span>
        <h3>Search</h3>
        <p>Find students by NIC or name, or list them all.</p>
      </a>
      <a class="dash-card" href="update.php">
        <span class="dash-icon"><?= icon('update') ?></span>
        <h3>Update</h3>
        <p>Load a student by NIC and edit their details.</p>
      </a>
      <a class="dash-card dash-card-danger" href="delete.php">
        <span class="dash-icon"><?= icon('delete') ?></span>
        <h3>Delete</h3>
        <p>Remove a student record after confirming.</p>
      </a>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
