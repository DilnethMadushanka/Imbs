<?php
// =============================================================
// index.php  -  PUBLIC HOME PAGE
// Sections, top to bottom:
//   1. Hero (photo, headline, intake card)   5. Courses (from the database)
//   2. Moving strip of programme names       6. How to join (3 steps)
//   3. About (big campus name + photo)        7. Call-to-action band
//   4. Numbers + why study here               8. Contact
// Elements with data-reveal fade up when they scroll into view (js/main.js).
// =============================================================
require_once 'includes/db.php';
$page_title = 'Home';

// Load the courses from the "course" table
$courses = mysqli_fetch_all(
    mysqli_query($conn, 'SELECT course_id, course_name FROM course ORDER BY course_id'),
    MYSQLI_ASSOC
);
$count = count($courses);

// Picture, duration, level and short description for each course (edit to match your campus)
$info = [
    'Diploma in ICT'                 => ['course-ict.webp',      '12 months', 'Diploma',     'Programming, networking, databases and web development from first principles.'],
    'HND in Computing'               => ['course-hnd.webp',      '24 months', 'HND',         'Software engineering, systems analysis and a final-year project with a real client.'],
    'Diploma in English'             => ['course-english.webp',  '6 months',  'Diploma',     'Speaking, writing and presentation skills for study and the workplace.'],
    'Diploma in Business Management' => ['course-business.webp', '12 months', 'Diploma',     'Accounting, marketing, human resources and how a small business is run.'],
    'Certificate in Graphic Design'  => ['course-design.webp',   '6 months',  'Certificate', 'Layout, typography and image editing, finished with a portfolio of your own work.'],
];

include 'includes/header.php';
?>

<!-- ===== 1. HERO: full-screen campus photo, headline on the left,
     glass "next intake" card on the right. Enters once on page load ===== -->
<section class="hero" data-enter>
  <!-- The photo sits behind the text; phones load the smaller file -->
  <img class="hero-bg" src="images/hero.webp"
       srcset="images/hero-900.webp 900w, images/hero.webp 1672w" sizes="100vw"
       alt="The <?= CAMPUS_NAME ?> building at sunset with students walking to class"
       width="1672" height="941" fetchpriority="high">
  <div class="hero-shade" aria-hidden="true"></div>

  <div class="wrap hero-layout">
    <div class="hero-content">
      <p class="hero-badge"><span class="pulse" aria-hidden="true"></span> January intake now open</p>
      <h1>Learn the skills that <em>build careers.</em></h1>
      <p class="lead">
        Diplomas, HNDs and certificates in computing, business, English and design,
        taught in small classes at <?= CAMPUS_AREA ?>.
      </p>
      <div class="hero-actions">
        <a class="btn btn-gold btn-lg" href="#courses">Explore programmes <?= icon('arrow') ?></a>
        <a class="btn btn-glass btn-lg" href="#contact">Visit the campus</a>
      </div>
    </div>

    <!-- Glass card: quick facts about the next intake -->
    <aside class="hero-card" aria-label="Next intake">
      <p class="hero-card-label">Next intake</p>
      <p class="hero-card-date">January 2027</p>
      <ul class="hero-card-list">
        <li><?= icon('book') ?><span><strong><?= $count ?></strong> programmes to choose from</span></li>
        <li><?= icon('clock') ?><span>Weekday and weekend batches</span></li>
        <li><?= icon('users') ?><span>Classes of 30 students or fewer</span></li>
      </ul>
      <a class="hero-card-link" href="#contact">Ask about enrolment <?= icon('arrow') ?></a>
    </aside>
  </div>

  <a class="scroll-cue" href="#about" aria-label="Scroll to About"><span></span></a>
</section>

<!-- ===== 2. MOVING STRIP: programme names scroll sideways.
     The list is printed twice so the loop has no gap ===== -->
<div class="marquee" aria-hidden="true">
  <div class="marquee-track">
    <?php for ($r = 0; $r < 2; $r++): foreach ($courses as $c): ?>
      <span><?= e($c['course_name']) ?></span><i>&#10022;</i>
    <?php endforeach; endfor; ?>
  </div>
</div>

<!-- ===== 3. ABOUT: the campus name set large (type moment) ===== -->
<section class="section about" id="about">
  <div class="wrap">
    <h2 class="sr-only">About the campus</h2>
    <p class="type-moment" aria-hidden="true" data-reveal><?= CAMPUS_NAME ?></p>
    <div class="about-grid">
      <div class="about-photo" data-reveal>
        <div class="photo">
          <img src="images/library.webp" alt="Students studying at long tables in the campus library" width="1000" height="563" loading="lazy">
        </div>
        <p class="photo-badge"><strong>2</strong><span>intakes every year, January and July</span></p>
      </div>
      <div class="about-text" data-reveal>
        <p class="eyebrow">About us</p>
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

<!-- ===== 4. NUMBERS + WHY STUDY HERE (navy band).
     data-count numbers count up from 0 when they appear ===== -->
<section class="section why">
  <div class="wrap">
    <div class="stats-row" data-reveal>
      <p><strong data-count="<?= $count ?>"><?= $count ?></strong><span>Programmes</span></p>
      <p><strong data-count="2">2</strong><span>Intakes a year</span></p>
      <p><strong data-count="2">2</strong><span>Computer labs</span></p>
      <p><strong data-count="30">30</strong><span>Max class size</span></p>
    </div>

    <div class="section-head section-head-light" data-reveal>
      <div>
        <p class="eyebrow">Why <?= CAMPUS_NAME ?></p>
        <h2>Small classes. Real skills. A certificate that counts.</h2>
      </div>
    </div>
    <div class="feature-grid">
      <article class="feature" data-reveal>
        <span class="feature-icon"><?= icon('users') ?></span>
        <h3>Lecturers who work in the field</h3>
        <p>Every class is taught by someone who uses what they teach in their own job.</p>
      </article>
      <article class="feature" data-reveal>
        <span class="feature-icon"><?= icon('monitor') ?></span>
        <h3>Hands-on labs</h3>
        <p>Two fully equipped computer labs, open to students outside class hours.</p>
      </article>
      <article class="feature" data-reveal>
        <span class="feature-icon"><?= icon('clock') ?></span>
        <h3>Study while you work</h3>
        <p>Weekday and weekend batches, so you can keep your job while you learn.</p>
      </article>
      <article class="feature" data-reveal>
        <span class="feature-icon"><?= icon('award') ?></span>
        <h3>Recognised certificates</h3>
        <p>Every programme ends with a campus certificate and a portfolio of your work.</p>
      </article>
    </div>
  </div>
</section>

<!-- ===== 5. COURSES: loaded from the "course" table ===== -->
<section class="section courses" id="courses">
  <div class="wrap">
    <div class="section-head" data-reveal>
      <div>
        <p class="eyebrow">Programmes</p>
        <h2>Courses offered</h2>
      </div>
      <p>Every course ends with a campus certificate. Intakes run in January and July.</p>
    </div>
    <div class="course-grid">
      <?php foreach ($courses as $i => $c):
        $d = $info[$c['course_name']] ?? [null, 'Ask us', 'Course', 'Contact the campus for the full syllabus.']; ?>
        <article class="course-card" data-reveal>
          <div class="course-photo photo">
            <?php if ($d[0]): ?>
              <img src="images/<?= $d[0] ?>" alt="<?= e($c['course_name']) ?> class work" width="1000" height="563" loading="lazy">
            <?php endif; ?>
            <span class="course-level"><?= e($d[2]) ?></span>
          </div>
          <div class="course-body">
            <span class="course-no"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
            <h3><?= e($c['course_name']) ?></h3>
            <p><?= e($d[3]) ?></p>
            <div class="course-foot">
              <span class="course-meta"><?= icon('clock') ?><?= e($d[1]) ?></span>
              <a class="course-link" href="#contact" aria-label="Ask about <?= e($c['course_name']) ?>"><?= icon('arrow') ?></a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== 6. HOW TO JOIN: three numbered steps ===== -->
<section class="section steps">
  <div class="wrap">
    <div class="section-head" data-reveal>
      <div>
        <p class="eyebrow">Admissions</p>
        <h2>How to join</h2>
      </div>
      <p>Three steps from your first visit to your first class.</p>
    </div>
    <ol class="step-list">
      <li data-reveal>
        <span class="step-no">01</span>
        <h3>Talk to us</h3>
        <p>Visit the campus or email us. We help you pick the right programme for your goals.</p>
      </li>
      <li data-reveal>
        <span class="step-no">02</span>
        <h3>Register</h3>
        <p>Bring your NIC and contact details. Our office registers you in a few minutes.</p>
      </li>
      <li data-reveal>
        <span class="step-no">03</span>
        <h3>Start learning</h3>
        <p>Join your batch in January or July and get your timetable on day one.</p>
      </li>
    </ol>
  </div>
</section>

<!-- ===== 7. CALL TO ACTION: photo band ===== -->
<section class="cta">
  <img class="cta-bg" src="images/corridor.webp" alt="" width="1200" height="675" loading="lazy">
  <div class="wrap cta-inner" data-reveal>
    <h2>Your next step starts at <?= CAMPUS_NAME ?>.</h2>
    <p>Seats for the January batch are limited. Come in and reserve yours.</p>
    <div class="hero-actions">
      <a class="btn btn-gold btn-lg" href="#contact">Book a visit <?= icon('arrow') ?></a>
      <a class="btn btn-glass btn-lg" href="mailto:<?= CAMPUS_EMAIL ?>">Email us</a>
    </div>
  </div>
</section>

<!-- ===== 8. CONTACT ===== -->
<section class="section contact" id="contact">
  <div class="wrap contact-grid">
    <div data-reveal>
      <p class="eyebrow">Contact</p>
      <h2>Visit the campus</h2>
      <p class="lead">Come in to talk about a course, see the labs or pick up an application form.</p>
    </div>
    <div class="contact-cards">
      <div class="contact-card" data-reveal>
        <span class="feature-icon"><?= icon('map') ?></span>
        <div><h3>Address</h3><p><?= CAMPUS_AREA ?></p></div>
      </div>
      <div class="contact-card" data-reveal>
        <span class="feature-icon"><?= icon('mail') ?></span>
        <div><h3>Email</h3><p><a href="mailto:<?= CAMPUS_EMAIL ?>"><?= CAMPUS_EMAIL ?></a></p></div>
      </div>
      <div class="contact-card" data-reveal>
        <span class="feature-icon"><?= icon('clock') ?></span>
        <div><h3>Open</h3><p><?= CAMPUS_HOURS ?></p></div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
