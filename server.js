// =============================================================
// server.js   (run with:  npm start)
// The main file. It creates the Express web server, sets up
// sessions and templates, and connects all the routes (pages).
// =============================================================
const path = require('path');
const express = require('express');
const session = require('cookie-session');   // session stored in a signed cookie (works on Vercel)
const site = require('./config/site');

const app = express();

// ---- View engine: EJS lets us put data inside HTML pages ----
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// ---- Read form data sent with method="POST" ----
app.use(express.urlencoded({ extended: false }));

// ---- Static files: CSS, JavaScript and images in /public ----
app.use(express.static(path.join(__dirname, 'public')));

// ---- Sessions: remember the admin after they log in ----
// cookie-session keeps the login inside a signed cookie, so it keeps working
// on Vercel where every request may run on a different server.
app.set('trust proxy', 1);   // Vercel sits in front of the app (needed for secure cookies)
app.use(session({
  name: 'campus_session',
  keys: [process.env.SESSION_SECRET || 'change-this-secret-key'], // signs the cookie so it cannot be faked
  maxAge: 1000 * 60 * 60,           // 1 hour
  httpOnly: true,
  sameSite: 'lax',
  secure: process.env.NODE_ENV === 'production'
}));

// ---- Values every page can use (campus name, login status, current page) ----
app.use((req, res, next) => {
  res.locals.site = site;
  res.locals.admin = (req.session && req.session.admin) || null;
  res.locals.currentPath = req.path;
  res.locals.icon = require('./utils/icons');   // SVG icons for the admin area
  next();
});

// ---- If Firebase is not set up, show the reason instead of crashing ----
const { configError } = require('./config/firebase');
app.use((req, res, next) => {
  if (!configError || req.path === '/') return next();   // the home page still works
  res.status(500).render('message', { title: 'Database not connected', text: configError });
});

// ---- Routes (pages) ----
app.use('/', require('./routes/auth'));      // home, login, logout
app.use('/', require('./routes/setup'));     // one-time database setup
app.use('/', require('./routes/students'));  // dashboard, register, search, update, delete

// ---- 404 page for unknown URLs ----
app.use((req, res) => {
  res.status(404).render('message', { title: 'Page not found', text: 'Sorry, that page does not exist.' });
});

// ---- Error handler (e.g. database not reachable) ----
app.use((err, req, res, next) => {
  console.error(err);
  // Common Firestore problems, explained in plain words
  let text = 'A server or database error occurred. Please try again.';
  if (err.code === 5) text = 'The Firestore database was not found. In the Firebase console open Build > Firestore Database and click Create database, then run npm run seed.';
  if (err.code === 7 || err.code === 16) text = 'Firebase refused the key. Generate a new private key and update FIREBASE_SERVICE_ACCOUNT (or config/serviceAccountKey.json).';
  res.status(500).render('message', { title: 'Something went wrong', text });
});

// ---- Start the server ----
// On your PC (npm start) we listen on port 3000.
// On Vercel the app is exported instead, and Vercel runs it (see api/index.js).
if (require.main === module) {
  app.listen(site.PORT, () => {
    console.log(`${site.CAMPUS_NAME} website running at http://localhost:${site.PORT}`);
  });
}

module.exports = app;
