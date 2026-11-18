// =============================================================
// routes/setup.js
// One-time setup page: /setup
// Fills an EMPTY database with the admin account, courses and sample
// students (same as "npm run seed"), so the site can be set up from a phone.
// Once an admin exists the page refuses to run again, so nobody can
// reset the data later.
// =============================================================
const express = require('express');
const { seedDatabase, needsSetup } = require('../utils/seed-data');

const router = express.Router();

router.get('/setup', async (req, res, next) => {
  try {
    res.render('setup', { title: 'Setup', done: !(await needsSetup()), justRan: false });
  } catch (err) { next(err); }
});

router.post('/setup', async (req, res, next) => {
  try {
    if (await needsSetup()) {      // check again: only ever runs on an empty database
      await seedDatabase();
      return res.render('setup', { title: 'Setup', done: true, justRan: true });
    }
    res.render('setup', { title: 'Setup', done: true, justRan: false });
  } catch (err) { next(err); }
});

module.exports = router;
