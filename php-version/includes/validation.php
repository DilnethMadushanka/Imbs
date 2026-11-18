<?php
// =============================================================
// includes/validation.php
// SERVER-SIDE validation for the student form (Register + Update).
// The browser checks too (HTML5 + js/main.js), but the server checks
// again because browser checks can be switched off.
// =============================================================

// Reads the form fields into one tidy array
function student_from_post() {
    return [
        'name'      => trim($_POST['name'] ?? ''),
        'nic'       => strtoupper(trim($_POST['nic'] ?? '')),   // 991234567v -> 991234567V
        'gender'    => $_POST['gender'] ?? '',
        'address'   => trim($_POST['address'] ?? ''),
        'contact'   => trim($_POST['contact'] ?? ''),
        'email'     => strtolower(trim($_POST['email'] ?? '')),
        'course_id' => $_POST['course_id'] ?? '',
    ];
}

// Returns a list of error messages (empty list = valid)
function validate_student($s, $course_ids) {
    $errors = [];

    // 1. All fields required
    foreach ($s as $value) {
        if ($value === '') { $errors[] = 'All fields are required.'; break; }
    }
    // 2. Format checks
    if ($s['name'] !== '' && !preg_match('/^[A-Za-z .]{3,100}$/', $s['name'])) {
        $errors[] = 'Name must contain only letters, spaces and dots (3-100 characters).';
    }
    // NIC: old = 9 digits + V/X, new = 12 digits
    if ($s['nic'] !== '' && !preg_match('/^([0-9]{9}[VX]|[0-9]{12})$/', $s['nic'])) {
        $errors[] = 'NIC must be 9 digits followed by V/X, or 12 digits.';
    }
    if ($s['gender'] !== '' && !in_array($s['gender'], ['Male', 'Female'], true)) {
        $errors[] = 'Please select a valid gender.';
    }
    // Contact: exactly 10 digits starting with 0
    if ($s['contact'] !== '' && !preg_match('/^0[0-9]{9}$/', $s['contact'])) {
        $errors[] = 'Contact number must be exactly 10 digits and start with 0.';
    }
    if ($s['email'] !== '' && !filter_var($s['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($s['course_id'] !== '' && !in_array((int)$s['course_id'], $course_ids, true)) {
        $errors[] = 'Please select a valid course.';
    }
    return $errors;
}

// Loads all courses for the drop-down list
function get_courses($conn) {
    $result = mysqli_query($conn, 'SELECT course_id, course_name FROM course ORDER BY course_id');
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
