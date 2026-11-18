<?php
// =============================================================
// register.php  -  REGISTER A NEW STUDENT (form + guidelines)
// =============================================================
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/validation.php';

$courses = get_courses($conn);
$errors  = [];
$success = '';
$form    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form   = student_from_post();
    $errors = validate_student($form, array_map('intval', array_column($courses, 'course_id')));

    if (!$errors) {
        // Check if this NIC is already registered (NIC is the primary key)
        $check = mysqli_prepare($conn, 'SELECT nic FROM student WHERE nic = ?');
        mysqli_stmt_bind_param($check, 's', $form['nic']);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $errors[] = 'A student with NIC ' . $form['nic'] . ' is already registered.';
        }
    }

    if (!$errors) {
        // Save the student with a prepared statement (no SQL injection)
        $stmt = mysqli_prepare($conn,
            'INSERT INTO student (nic, name, gender, address, contact, email, course_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $course_id = (int)$form['course_id'];
        mysqli_stmt_bind_param($stmt, 'ssssssi', $form['nic'], $form['name'], $form['gender'],
                               $form['address'], $form['contact'], $form['email'], $course_id);
        mysqli_stmt_execute($stmt);

        $success = 'Student ' . $form['name'] . ' (' . $form['nic'] . ') registered successfully.';
        $form = [];   // empty the form for the next student
    }
}

$page_title = 'Register Student';
include 'includes/header.php';
?>
<section class="page">
  <div class="wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow"><a href="dashboard.php">Dashboard</a></p>
        <h1>Register a student</h1>
      </div>
    </div>

    <?php include 'includes/alerts.php'; ?>

    <div class="form-layout">
      <div class="panel">
        <?php
          $form_action = 'register.php';
          $lock_nic    = false;
          $button_text = 'Register student';
          include 'includes/student_form.php';
        ?>
      </div>

      <!-- Registration guidelines (bullet points) beside the form -->
      <aside class="panel guidelines">
        <h2>Registration guidelines</h2>
        <ul>
          <li>Fill in every field. The form will not save with a blank field.</li>
          <li>Enter the name as it appears on the NIC.</li>
          <li>NIC: old format is 9 digits and V or X (991234567V); new format is 12 digits (200012345678).</li>
          <li>Each NIC can be registered only once.</li>
          <li>Contact number: 10 digits starting with 0 (0771234567).</li>
          <li>Use an email address the student checks often.</li>
          <li>Choose the course the student has paid for.</li>
        </ul>
      </aside>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
