<?php
// =============================================================
// includes/alerts.php
// Shows the error list ($errors) and/or the success message ($success).
// =============================================================
if (!empty($errors)): ?>
  <div class="alert alert-error" role="alert">
    <?php if (count($errors) === 1): ?>
      <?= e($errors[0]) ?>
    <?php else: ?>
      <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
  </div>
<?php endif;
if (!empty($success)): ?>
  <div class="alert alert-success" role="status"><?= e($success) ?></div>
<?php endif; ?>
