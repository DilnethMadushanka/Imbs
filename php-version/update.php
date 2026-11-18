<?php
// =============================================================
// update.php  -  UPDATE A STUDENT
// Step 1: enter NIC (GET)  -> the form is filled with the saved details
// Step 2: edit and save (POST) -> UPDATE query
// =============================================================
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/validation.php';

$courses = get_courses($conn);
$errors  = [];
$success = '';
$student = null;                                   // the student being edited
$nic     = strtoupper(trim($_GET['nic'] ?? ''));

// Helper: load one student by NIC (returns null if not found)
function find_student($conn, $nic) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM student WHERE nic = ?');
    mysqli_stmt_bind_param($stmt, 's', $nic);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---- Step 2: save changes ----
    $form   = student_from_post();       // NIC comes from the read-only field
    $nic    = $form['nic'];
    $errors = validate_student($form, array_map('intval', array_column($courses, 'course_id')));

    if (!$errors && !find_student($conn, $nic)) {
        $errors[] = 'Student not found.';
    } elseif (!$errors) {
        $stmt = mysqli_prepare($conn,
            'UPDATE student SET name = ?, gender = ?, address = ?, contact = ?, email = ?, course_id = ? WHERE nic = ?');
        $course_id = (int)$form['course_id'];
        mysqli_stmt_bind_param($stmt, 'sssssis', $form['name'], $form['gender'], $form['address'],
                               $form['contact'], $form['email'], $course_id, $nic);
        mysqli_stmt_execute($stmt);
        $success = 'Details of ' . $form['name'] . ' updated successfully.';
    }
    $student = $errors === ['Student not found.'] ? null : $form;
} elseif ($nic !== '') {
    // ---- Step 1: load the student ----
    $student = find_student($conn, $nic);
    if (!$student) {
        $errors[] = 'Student not found.';
    }
}

$page_title = 'Update Student';
include 'includes/header.php';
?>
<section class="page">
  <div class="wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow"><a href="dashboard.php">Dashboard</a></p>
        <h1>Update a student</h1>
      </div>
    </div>

    <!-- Step 1: find the student by NIC -->
    <form method="GET" action="update.php" class="search-bar" novalidate data-validate>
      <label for="find-nic" class="sr-only">NIC number</label>
      <input type="text" id="find-nic" name="nic" value="<?= e($nic) ?>" required maxlength="12"
             pattern="([0-9]{9}[VvXx]|[0-9]{12})" data-msg="Enter 9 digits followed by V or X, or 12 digits."
             placeholder="Enter the student's NIC">
      <button type="submit" class="btn btn-gold">Load student</button>
      <small class="field-error"></small>
    </form>

    <?php include 'includes/alerts.php'; ?>

    <!-- Step 2: the form, filled with the saved details (NIC cannot be changed) -->
    <?php if ($student): ?>
      <div class="panel form-single">
        <?php
          $form        = $student;
          $form_action = 'update.php';
          $lock_nic    = true;
          $button_text = 'Save changes';
          include 'includes/student_form.php';
        ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
