// =============================================================
// public/js/main.js
// Small vanilla JavaScript for the browser:
//   1. Mobile menu open / close
//   2. Client-side form validation (friendly messages)
//   3. confirm("Are you sure?") before deleting
// =============================================================

// ---------- 1. Mobile menu ----------
const toggle = document.querySelector('.nav-toggle');
const links = document.querySelector('.nav-links');
if (toggle && links) {
  toggle.addEventListener('click', function () {
    const open = links.classList.toggle('open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false'); // tells screen readers the state
    document.body.classList.toggle('menu-open', open);              // stop the page scrolling behind the menu
  });

  // Close the menu when a link inside it is tapped (e.g. "Courses" on the home page),
  // otherwise the menu would stay on top of the section we jumped to.
  links.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () {
      links.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('menu-open');
    });
  });
}

// ---------- 2. Client-side validation ----------
// The rules are written on the inputs themselves (required, pattern, type="email").
// The browser checks them with checkValidity(); we only show nicer messages.
function errorBox(input) {
  const field = input.closest('.field') || input.closest('form');
  return field ? field.querySelector('.field-error') : null;
}

function checkInput(input) {
  // Trim spaces first so "  " does not count as a value
  if (input.type !== 'radio' && input.type !== 'password' && typeof input.value === 'string') {
    input.value = input.value.trim();
  }
  const box = errorBox(input);
  let message = '';

  if (!input.checkValidity()) {
    if (input.validity.valueMissing) {
      message = input.type === 'radio' ? 'Please select an option.' : 'This field is required.';
    } else {
      message = input.dataset.msg || input.validationMessage;
    }
  }
  input.classList.toggle('invalid', message !== '');
  if (box) box.textContent = message;
  return message === '';
}

document.querySelectorAll('form[data-validate]').forEach(function (form) {
  const inputs = form.querySelectorAll('input:not([type=hidden]), select, textarea');

  // Check a field again as soon as the user leaves it or changes it
  inputs.forEach(function (input) {
    input.addEventListener('blur', function () { if (input.value) checkInput(input); });
    input.addEventListener('change', function () { checkInput(input); });
  });

  // On submit, check everything and stop the form if anything is wrong
  form.addEventListener('submit', function (event) {
    let ok = true;
    inputs.forEach(function (input) {
      if (!checkInput(input)) ok = false;
    });
    if (!ok) {
      event.preventDefault();                        // do not send the form
      const first = form.querySelector('.invalid');
      if (first) first.focus();                      // jump to the first problem
    }
  });
});

// ---------- 3. Confirm before delete ----------
document.querySelectorAll('form[data-confirm]').forEach(function (form) {
  form.addEventListener('submit', function (event) {
    if (!confirm(form.dataset.confirm)) {   // "Are you sure ...?"  Cancel = stop
      event.preventDefault();
    }
  });
});
