// =============================================================
// routes/auth.js
// Public pages: Home (/), Admin login (/admin) and Logout (/logout).
// =============================================================
const express = require('express');
const bcrypt = require('bcryptjs');
const { admins, courses, configError } = require('../config/firebase');
const { needsSetup, COURSES } = require('../utils/seed-data');

const router = express.Router();

// ---------- HOME PAGE ----------
router.get('/', async (req, res) => {
  let courseList = [];
  try {
    // Load the course names from Firestore to show on the home page
    if (configError) throw new Error(configError);
    const snap = await courses.orderBy('course_id').get();
    courseList = snap.docs.map((d) => d.data());
  } catch (err) {
    console.error('Could not load courses:', err.message); // page still loads without them
  }
  // Database empty or not reachable? Show the standard course list so the
  // home page never looks broken.
  if (!courseList.length) {
    courseList = COURSES.map((name, i) => ({ course_id: i + 1, course_name: name }));
  }
  res.render('index', { title: 'Home', courses: courseList });
});

// ---------- LOGIN PAGE (show form) ----------
// Old address /login still works - it just sends you to /admin
router.get('/login', (req, res) => res.redirect(301, '/admin'));

router.get('/admin', (req, res) => {
  if (req.session.admin) return res.redirect('/dashboard'); // already logged in
  res.render('login', { title: 'Admin Login', error: null, username: '' });
});

// ---------- LOGIN (check username + password) ----------
router.post('/admin', async (req, res, next) => {
  try {
    const username = String(req.body.username || '').trim();
    const password = String(req.body.password || '');

    if (!username || !password) {
      return res.render('login', { title: 'Admin Login', error: 'Please enter username and password.', username });
    }

    // Find the admin document whose id is the username
    const doc = await admins.doc(username).get();

    // bcrypt.compare() checks the typed password against the stored hash
    // (same idea as password_verify() in PHP)
    if (doc.exists && await bcrypt.compare(password, doc.data().password)) {
      req.session.admin = username;   // remember who logged in (saved in a signed cookie)
      res.redirect('/dashboard');
    } else {
      // No admin at all yet? Point to the setup page instead.
      const error = (await needsSetup())
        ? 'No admin account exists yet. Open /setup to set up the database first.'
        : 'Invalid username or password.';
      res.render('login', { title: 'Admin Login', error, username });
    }
  } catch (err) {
    next(err);
  }
});

// ---------- LOGOUT ----------
router.get('/logout', (req, res) => {
  req.session = null;              // clears the session cookie
  res.redirect('/admin');
});

module.exports = router;
