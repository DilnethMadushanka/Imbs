// =============================================================
// middleware/auth.js
// Protects admin pages. If the admin has not logged in, they are
// sent back to the login page. (Same job as auth.php in PHP.)
// =============================================================
module.exports = function requireLogin(req, res, next) {
  // req.session.admin is set in routes/auth.js after a successful login
  if (req.session && req.session.admin) {
    return next();               // logged in -> continue to the page
  }
  return res.redirect('/admin'); // not logged in -> go to the admin login page
};
