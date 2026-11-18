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

  <!-- ===== Footer ===== -->
  <footer class="footer">
    <div class="wrap footer-inner">
      <div>
        <p class="footer-name"><?= CAMPUS_NAME ?></p>
        <p><?= CAMPUS_AREA ?></p>
      </div>
      <div>
        <p><a href="mailto:<?= CAMPUS_EMAIL ?>"><?= CAMPUS_EMAIL ?></a></p>
        <p><?= CAMPUS_HOURS ?></p>
      </div>
      <p class="footer-copy">&copy; <?= date('Y') ?> <?= CAMPUS_NAME ?>. CIT1074 Web Application Development Project.</p>
    </div>
  </footer>
<?php endif; ?>

  <!-- Client-side validation, mobile menu and delete confirmation -->
  <script src="js/main.js"></script>
</body>
</html>
