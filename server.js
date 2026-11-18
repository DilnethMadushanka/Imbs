// =============================================================
// server.js   (run with:  npm start)
// The main file. It creates the Express web server, sets up
// sessions and templates, and connects all the routes (pages).
// =============================================================
const path = require('path');
const express = require('express');
const session = require('express-session');
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
app.use(session({
  secret: 'change-this-secret-key',   // used to sign the session cookie
  resave: false,
  saveUninitialized: false,
  cookie: { httpOnly: true, maxAge: 1000 * 60 * 60 } // 1 hour
}));

// ---- Values every page can use (campus name, login status, current page) ----
app.use((req, res, next) => {
  res.locals.site = site;
  res.locals.admin = req.session.admin || null;
  res.locals.currentPath = req.path;
  next();
});

// ---- Routes (pages) ----
app.use('/', require('./routes/auth'));      // home, login, logout
app.use('/', require('./routes/students'));  // dashboard, register, search, update, delete

// ---- 404 page for unknown URLs ----
app.use((req, res) => {
  res.status(404).render('message', { title: 'Page not found', text: 'Sorry, that page does not exist.' });
});

// ---- Error handler (e.g. database not reachable) ----
app.use((err, req, res, next) => {
  console.error(err);
  res.status(500).render('message', { title: 'Something went wrong', text: 'A server or database error occurred. Please try again.' });
});

// ---- Start the server ----
app.listen(site.PORT, () => {
  console.log(`${site.CAMPUS_NAME} website running at http://localhost:${site.PORT}`);
});
