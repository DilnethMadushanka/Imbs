<?php
// =============================================================
// includes/footer.php
// Shared bottom part of every page: footer + JavaScript file.
// =============================================================
?>
<?php if (!empty($is_admin)): ?>
    <!-- Admin pages: a small footer inside the content area -->
    <footer class="admin-footer">&copy; <?= date('Y') ?> <?= CAMPUS_NAME ?> &middot; Admin panel</footer>
  </main>
<?php else: ?>
  </main>

  <!-- ===== Footer: brand, links, programmes, contact ===== -->
  <footer class="footer">
    <div class="wrap footer-grid">
      <div class="footer-about">
        <a class="brand" href="index.php">
          <img src="images/logo.png" alt="" width="40" height="40">
          <span><?= CAMPUS_NAME ?></span>
        </a>
        <p>Diplomas, HNDs and certificates in computing, business, English and design.</p>
      </div>
      <div>
        <p class="footer-head">Explore</p>
        <ul class="footer-links">
          <li><a href="index.php#about">About</a></li>
          <li><a href="index.php#courses">Courses</a></li>
          <li><a href="index.php#contact">Contact</a></li>
          <li><a href="login.php">Admin login</a></li>
        </ul>
      </div>
      <div>
        <p class="footer-head">Visit</p>
        <ul class="footer-links">
          <li><?= CAMPUS_AREA ?></li>
          <li><a href="mailto:<?= CAMPUS_EMAIL ?>"><?= CAMPUS_EMAIL ?></a></li>
          <li><?= CAMPUS_HOURS ?></li>
        </ul>
      </div>
      <p class="footer-copy">&copy; <?= date('Y') ?> <?= CAMPUS_NAME ?>. CIT1074 Web Application Development Project.</p>
    </div>
  </footer>
<?php endif; ?>

  <!-- Client-side validation, mobile menu and delete confirmation -->
  <script src="js/main.js"></script>
</body>
</html>
