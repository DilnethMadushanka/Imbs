// =============================================================
// routes/auth.js
// Public pages: Home (/), Login (/login) and Logout (/logout).
// =============================================================
const express = require('express');
const bcrypt = require('bcryptjs');
const { admins, courses } = require('../config/firebase');

const router = express.Router();

// ---------- HOME PAGE ----------
router.get('/', async (req, res) => {
  let courseList = [];
  try {
    // Load the course names from Firestore to show on the home page
    const snap = await courses.orderBy('course_id').get();
    courseList = snap.docs.map((d) => d.data());
  } catch (err) {
    console.error('Could not load courses:', err.message); // page still loads without them
  }
  res.render('index', { title: 'Home', courses: courseList });
});

// ---------- LOGIN PAGE (show form) ----------
router.get('/login', (req, res) => {
  if (req.session.admin) return res.redirect('/dashboard'); // already logged in
  res.render('login', { title: 'Admin Login', error: null, username: '' });
});

// ---------- LOGIN (check username + password) ----------
router.post('/login', async (req, res, next) => {
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
      res.render('login', { title: 'Admin Login', error: 'Invalid username or password.', username });
    }
  } catch (err) {
    next(err);
  }
});

// ---------- LOGOUT ----------
router.get('/logout', (req, res) => {
  req.session = null;              // clears the session cookie
  res.redirect('/login');
});

module.exports = router;
