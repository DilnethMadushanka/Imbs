<?php
// =============================================================
// index.php  -  PUBLIC HOME PAGE
// Sections: hero, about, courses (loaded from the database), contact.
// =============================================================
require_once 'includes/db.php';
$page_title = 'Home';

// Load the courses from the "course" table
$courses = mysqli_fetch_all(
    mysqli_query($conn, 'SELECT course_id, course_name FROM course ORDER BY course_id'),
    MYSQLI_ASSOC
);

// Short description, duration and picture for each course (edit to match your campus)
$info = [
    'Diploma in ICT'                 => ['12 months', 'Programming, networking, databases and web development from first principles.', 'course-ict.webp'],
    'HND in Computing'               => ['24 months', 'Software engineering, systems analysis and a final-year project with a real client.', 'course-hnd.webp'],
    'Diploma in English'             => ['6 months',  'Speaking, writing and presentation skills for study and the workplace.', 'course-english.webp'],
    'Diploma in Business Management' => ['12 months', 'Accounting, marketing, human resources and how a small business is run.', 'course-business.webp'],
    'Certificate in Graphic Design'  => ['6 months',  'Layout, typography and image editing, finished with a portfolio of your own work.', 'course-design.webp'],
];

include 'includes/header.php';
?>

<!-- ===== HERO: full-width campus photo with the headline on top ===== -->
<section class="hero" data-enter>
  <!-- The photo sits behind the text; phones load the smaller file -->
  <img class="hero-bg" src="images/hero.webp"
       srcset="images/hero-900.webp 900w, images/hero.webp 1672w" sizes="100vw"
       alt="The <?= CAMPUS_NAME ?> building at sunset with students walking to class"
       width="1672" height="941" fetchpriority="high">
  <div class="hero-shade" aria-hidden="true"></div>

  <div class="wrap hero-content">
    <p class="eyebrow"><?= CAMPUS_AREA ?></p>
    <h1>Diplomas and HNDs in computing, business and English.</h1>
    <p class="lead">
      <?= CAMPUS_NAME ?> runs <?= count($courses) ?> full and part-time programmes,
      taught in small classes by lecturers who work in the field.
    </p>
    <div class="hero-actions">
      <a class="btn btn-gold" href="#courses">See the courses</a>
      <a class="btn btn-outline-light" href="login.php">Admin login</a>
    </div>
  </div>

  <!-- Three quick facts along the bottom of the hero -->
  <div class="hero-strip">
    <div class="wrap hero-strip-inner">
      <p><strong><?= count($courses) ?></strong> programmes</p>
      <p><strong>2</strong> intakes a year</p>
      <p><strong>2</strong> computer labs</p>
    </div>
  </div>
</section>

<!-- ===== ABOUT: the campus name set large ===== -->
<section class="section about" id="about">
  <div class="wrap">
    <h2 class="sr-only">About the campus</h2>
    <p class="type-moment" aria-hidden="true"><?= CAMPUS_NAME ?></p>
    <div class="about-grid">
      <div class="photo">
        <img src="images/library.webp" alt="Students studying at long tables in the campus library" width="1000" height="563" loading="lazy">
      </div>
      <div class="about-text">
        <h3>A campus built around the classroom</h3>
        <p>
          We started with one lab and one diploma. Today students come to us for computing,
          business, English and design, and most of them study while they work.
        </p>
        <ul class="facts">
          <li><strong>Weekday and weekend</strong><span>batches for every programme</span></li>
          <li><strong>Two computer labs</strong><span>open to students until 5 pm</span></li>
          <li><strong>One-to-one</strong><span>guidance before you choose a course</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ===== COURSES: loaded from the "course" table ===== -->
<section class="section courses" id="courses">
  <div class="wrap">
    <div class="section-head">
      <h2>Courses offered</h2>
      <p>Every course ends with a campus certificate. Intakes run in January and July.</p>
    </div>
    <div class="course-grid">
      <?php foreach ($courses as $i => $c):
        $d = $info[$c['course_name']] ?? ['Ask us', 'Contact the campus for the full syllabus.', null]; ?>
        <article class="course-card">
          <?php if ($d[2]): ?>
            <div class="course-photo photo">
              <img src="images/<?= $d[2] ?>" alt="<?= e($c['course_name']) ?> class work" width="1000" height="563" loading="lazy">
            </div>
          <?php endif; ?>
          <span class="course-no"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
          <h3><?= e($c['course_name']) ?></h3>
          <p><?= e($d[1]) ?></p>
          <span class="course-meta"><?= e($d[0]) ?></span>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== CONTACT ===== -->
<section class="section contact" id="contact">
  <div class="wrap contact-grid">
    <div>
      <h2>Visit the campus</h2>
      <p class="lead">Come in to talk about a course, see the labs or pick up an application form.</p>
    </div>
    <dl class="contact-list">
      <div><dt>Address</dt><dd><?= CAMPUS_AREA ?></dd></div>
      <div><dt>Email</dt><dd><a href="mailto:<?= CAMPUS_EMAIL ?>"><?= CAMPUS_EMAIL ?></a></dd></div>
      <div><dt>Open</dt><dd><?= CAMPUS_HOURS ?></dd></div>
    </dl>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
