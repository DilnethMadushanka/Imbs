// =============================================================
// public/js/main.js
// Small vanilla JavaScript for the browser:
//   1. Mobile menu open / close
//   2. Client-side form validation (friendly messages)
//   3. confirm("Are you sure?") before deleting
//   4. Home page: solid navbar on scroll, fade-in on scroll, counting numbers
// =============================================================

// ---------- 1. Mobile menu ----------
// The button's aria-controls says which menu it opens
// (the top menu on public pages, the sidebar on admin pages)
const toggle = document.querySelector('.nav-toggle');
const links = toggle ? document.getElementById(toggle.getAttribute('aria-controls')) : null;
if (toggle && links) {
  toggle.addEventListener('click', function () {
    const open = links.classList.toggle('open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false'); // tells screen readers the state
    document.body.classList.toggle('menu-open', open);              // stop the page scrolling behind the menu
  });

  // Tapping outside the open menu (on the dark backdrop) closes it too
  document.addEventListener('click', function (event) {
    if (links.classList.contains('open') && !links.contains(event.target) && !toggle.contains(event.target)) {
      links.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('menu-open');
    }
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

// ---------- 4. Home page effects ----------
// a) The see-through navbar turns solid once the page is scrolled
const navbar = document.querySelector('.is-home .navbar');
if (navbar) {
  const onScroll = function () { navbar.classList.toggle('scrolled', window.scrollY > 40); };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
}

// b) Sections fade up when they scroll into view, and numbers count up from 0
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function countUp(el) {
  const target = parseInt(el.dataset.count, 10);
  if (reduceMotion || !target) return;            // keep the real number as it is
  const start = performance.now();
  const step = function (now) {
    const t = Math.min((now - start) / 1200, 1);   // 1.2 seconds
    el.textContent = Math.round(target * (1 - Math.pow(1 - t, 3)));  // ease-out
    if (t < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

const revealItems = document.querySelectorAll('[data-reveal]');
if ('IntersectionObserver' in window && revealItems.length) {
  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('in');
      // remove the delay afterwards so hover effects react straight away
      setTimeout(function () { entry.target.style.transitionDelay = ''; }, 1200);
      entry.target.querySelectorAll('[data-count]').forEach(countUp);
      observer.unobserve(entry.target);            // animate once only
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
  revealItems.forEach(function (item) {
    // cards in the same row appear one after another
    item.style.transitionDelay = (item.parentElement.children.length > 1 ? (Array.prototype.indexOf.call(item.parentElement.children, item) % 4) * 90 : 0) + 'ms';
    observer.observe(item);
  });
} else {
  revealItems.forEach(function (item) { item.classList.add('in'); });
}
