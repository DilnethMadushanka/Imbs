<?php
// =============================================================
// includes/student_form.php
// The student form, shared by Register and Update so it is written once.
// Uses: $form (field values), $courses (drop-down list),
//       $form_action (page to send to), $lock_nic (true on Update), $button_text
// HTML5 attributes (required, pattern, type="email") check in the browser;
// js/main.js shows friendly messages; PHP checks everything again.
// =============================================================
?>
<form method="POST" action="<?= e($form_action) ?>" class="form student-form" novalidate data-validate>
  <div class="field">
    <label for="name">Full name</label>
    <input type="text" id="name" name="name" value="<?= e($form['name'] ?? '') ?>"
           required minlength="3" maxlength="100" pattern="[A-Za-z .]{3,100}"
           data-msg="Use letters, spaces and dots only (at least 3 characters)." placeholder="e.g. Kasun Perera">
    <small class="field-error"></small>
  </div>

  <div class="field">
    <label for="nic">NIC number</label>
    <!-- On Update the NIC is the primary key, so it is read-only -->
    <input type="text" id="nic" name="nic" value="<?= e($form['nic'] ?? '') ?>"
           required maxlength="12" pattern="([0-9]{9}[VvXx]|[0-9]{12})"
           data-msg="Enter 9 digits followed by V or X, or 12 digits."
           placeholder="991234567V or 200012345678" <?= $lock_nic ? 'readonly' : '' ?>>
    <small class="field-error"></small>
  </div>

  <fieldset class="field">
    <legend>Gender</legend>
    <div class="radio-row">
      <label class="radio"><input type="radio" name="gender" value="Male" required <?= ($form['gender'] ?? '') === 'Male' ? 'checked' : '' ?>> Male</label>
      <label class="radio"><input type="radio" name="gender" value="Female" <?= ($form['gender'] ?? '') === 'Female' ? 'checked' : '' ?>> Female</label>
    </div>
    <small class="field-error"></small>
  </fieldset>

  <div class="field">
    <label for="address">Address</label>
    <textarea id="address" name="address" rows="3" required maxlength="255"
              data-msg="Address is required."><?= e($form['address'] ?? '') ?></textarea>
    <small class="field-error"></small>
  </div>

  <div class="field-row">
    <div class="field">
      <label for="contact">Contact number</label>
      <input type="tel" id="contact" name="contact" value="<?= e($form['contact'] ?? '') ?>"
             required maxlength="10" pattern="0[0-9]{9}" inputmode="numeric"
             data-msg="Enter 10 digits starting with 0." placeholder="0771234567">
      <small class="field-error"></small>
    </div>
    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= e($form['email'] ?? '') ?>"
             required maxlength="100" data-msg="Enter a valid email address." placeholder="name@example.com">
      <small class="field-error"></small>
    </div>
  </div>

  <div class="field">
    <label for="course_id">Course</label>
    <!-- Drop-down list filled from the "course" table -->
    <select id="course_id" name="course_id" required data-msg="Please choose a course.">
      <option value="">Select a course</option>
      <?php foreach ($courses as $c): ?>
        <option value="<?= (int)$c['course_id'] ?>" <?= (string)($form['course_id'] ?? '') === (string)$c['course_id'] ? 'selected' : '' ?>><?= e($c['course_name']) ?></option>
      <?php endforeach; ?>
    </select>
    <small class="field-error"></small>
  </div>

  <button type="submit" class="btn btn-gold"><?= e($button_text) ?></button>
</form>
