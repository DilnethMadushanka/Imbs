<?php
// =============================================================
// search.php  -  SEARCH STUDENTS by NIC or part of a name
// Empty search = show every student.
// =============================================================
require_once 'includes/auth.php';
require_once 'includes/db.php';

$q = trim($_GET['q'] ?? '');

// JOIN with the course table to show the course name
$sql = 'SELECT s.*, c.course_name
        FROM student s
        JOIN course c ON c.course_id = s.course_id';

if ($q === '') {
    // No search word: list everyone
    $result = mysqli_query($conn, $sql . ' ORDER BY s.name');
} else {
    // LIKE '%word%' finds the word anywhere in the NIC or name
    $stmt = mysqli_prepare($conn, $sql . ' WHERE s.nic LIKE ? OR s.name LIKE ? ORDER BY s.name');
    $like = '%' . $q . '%';
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
$results = mysqli_fetch_all($result, MYSQLI_ASSOC);

$page_title = 'Search Students';
include 'includes/header.php';
?>
<section class="page">
  <div class="wrap">
    <div class="page-head">
      <div>
        <p class="eyebrow"><a href="dashboard.php">Dashboard</a></p>
        <h1>Search students</h1>
      </div>
    </div>

    <!-- GET form: the search word goes in the URL, e.g. search.php?q=kasun -->
    <form method="GET" action="search.php" class="search-bar">
      <label for="q" class="sr-only">NIC or name</label>
      <input type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="NIC or name (leave empty for all)">
      <button type="submit" class="btn btn-gold">Search</button>
    </form>

    <p class="result-count">
      <?= count($results) ?> <?= count($results) === 1 ? 'student' : 'students' ?>
      <?= $q !== '' ? 'found for "' . e($q) . '"' : 'in total' ?>
    </p>

    <?php if ($results): ?>
      <div class="table-wrap">
        <!-- On phones each row turns into a card (see "Tables" in style.css) -->
        <table class="table table-cards">
          <thead>
            <tr>
              <th>NIC</th><th>Name</th><th>Gender</th><th>Address</th>
              <th>Contact</th><th>Email</th><th>Course</th><th>Registered</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($results as $s): ?>
              <tr>
                <td data-label="NIC" class="mono"><?= e($s['nic']) ?></td>
                <td data-label="Name"><span class="name-cell"><span class="avatar avatar-sm"><?= e(initials($s['name'])) ?></span><?= e($s['name']) ?></span></td>
                <td data-label="Gender"><?= e($s['gender']) ?></td>
                <td data-label="Address"><?= e($s['address']) ?></td>
                <td data-label="Contact" class="mono"><?= e($s['contact']) ?></td>
                <td data-label="Email"><?= e($s['email']) ?></td>
                <td data-label="Course"><span class="chip"><?= e($s['course_name']) ?></span></td>
                <td data-label="Registered"><?= date('d/m/Y', strtotime($s['reg_date'])) ?></td>
                <td class="row-actions">
                  <a href="update.php?nic=<?= urlencode($s['nic']) ?>">Edit</a>
                  <a href="delete.php?nic=<?= urlencode($s['nic']) ?>" class="danger-link">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-error">No students match your search.</div>
    <?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
