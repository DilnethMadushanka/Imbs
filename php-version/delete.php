<?php
// =============================================================
// delete.php  -  DELETE A STUDENT
// Step 1: enter NIC -> show the details
// Step 2: JavaScript confirm("Are you sure?") -> DELETE query
// =============================================================
require_once 'includes/auth.php';
require_once 'includes/db.php';

$errors  = [];
$success = '';
$student = null;
$nic     = strtoupper(trim($_GET['nic'] ?? ''));

// Load one student with the course name
function find_student($conn, $nic) {
    $stmt = mysqli_prepare($conn,
        'SELECT s.*, c.course_name FROM student s JOIN course c ON c.course_id = s.course_id WHERE s.nic = ?');
    mysqli_stmt_bind_param($stmt, 's', $nic);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---- Step 2: delete (only reached after the user clicked OK in confirm) ----
    $nic   = strtoupper(trim($_POST['nic'] ?? ''));
    $found = $nic !== '' ? find_student($conn, $nic) : null;
    if (!$found) {
        $errors[] = 'Student not found.';
    } else {
        $stmt = mysqli_prepare($conn, 'DELETE FROM student WHERE nic = ?');
        mysqli_stmt_bind_param($stmt, 's', $nic);
        mysqli_stmt_execute($stmt);
        $success = 'Student ' . $found['name'] . ' (' . $nic . ') was deleted successfully.';
        $nic = '';
    }
} elseif ($nic !== '') {
    // ---- Step 1: show the student ----
    $student = find_student($conn, $nic);
    if (!$student) {
        $errors[] = 'Student not found.';
    }
}

$page_title = 'Delete Student';
include 'includes/header.php';
?>
<section class="page">
  <div class="wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow"><a href="dashboard.php">Dashboard</a></p>
        <h1>Delete a student</h1>
      </div>
    </div>

    <form method="GET" action="delete.php" class="search-bar" novalidate data-validate>
      <label for="find-nic" class="sr-only">NIC number</label>
      <input type="text" id="find-nic" name="nic" value="<?= e($nic) ?>" required maxlength="12"
             pattern="([0-9]{9}[VvXx]|[0-9]{12})" data-msg="Enter 9 digits followed by V or X, or 12 digits."
             placeholder="Enter the student's NIC">
      <button type="submit" class="btn btn-gold">Find student</button>
      <small class="field-error"></small>
    </form>

    <?php include 'includes/alerts.php'; ?>

    <?php if ($student): ?>
      <div class="panel form-single">
        <h2>Student details</h2>
        <dl class="details">
          <div><dt>NIC</dt><dd class="mono"><?= e($student['nic']) ?></dd></div>
          <div><dt>Name</dt><dd><?= e($student['name']) ?></dd></div>
          <div><dt>Gender</dt><dd><?= e($student['gender']) ?></dd></div>
          <div><dt>Address</dt><dd><?= e($student['address']) ?></dd></div>
          <div><dt>Contact</dt><dd class="mono"><?= e($student['contact']) ?></dd></div>
          <div><dt>Email</dt><dd><?= e($student['email']) ?></dd></div>
          <div><dt>Course</dt><dd><?= e($student['course_name']) ?></dd></div>
          <div><dt>Registered</dt><dd><?= date('d/m/Y', strtotime($student['reg_date'])) ?></dd></div>
        </dl>

        <!-- data-confirm makes js/main.js ask "Are you sure?" before sending -->
        <form method="POST" action="delete.php" data-confirm="Are you sure you want to delete <?= e($student['name']) ?>?">
          <input type="hidden" name="nic" value="<?= e($student['nic']) ?>">
          <button type="submit" class="btn btn-danger">Delete student</button>
        </form>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
