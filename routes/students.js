// =============================================================
// routes/students.js
// All ADMIN pages: Dashboard, Register, Search, Update, Delete.
// Every route uses requireLogin, so only a logged-in admin can open them.
// =============================================================
const express = require('express');
const requireLogin = require('../middleware/auth');
const { courses, students } = require('../config/firebase');
const { cleanNic, validateStudent } = require('../utils/validation');

const router = express.Router();
router.use(['/dashboard', '/register', '/search', '/update', '/delete'], requireLogin);

// ---------- Helper: load all courses (for drop-downs and names) ----------
async function getCourses() {
  const snap = await courses.orderBy('course_id').get();
  return snap.docs.map((d) => ({ id: d.id, ...d.data() }));
}

// ---------- Helper: turn a Firestore document into a simple student object ----------
function toStudent(doc, courseList) {
  const s = doc.data();
  const course = courseList.find((c) => String(c.course_id) === String(s.course_id));
  return {
    ...s,
    course_name: course ? course.course_name : 'Unknown',
    // Firestore stores dates as Timestamps; convert to normal JS Date
    reg_date: s.reg_date && s.reg_date.toDate ? s.reg_date.toDate() : s.reg_date
  };
}

// ---------- Helper: pick only the fields we save (ignore anything else sent) ----------
function studentFromForm(body) {
  return {
    name: String(body.name || '').trim(),
    nic: cleanNic(body.nic),
    gender: body.gender || '',
    address: String(body.address || '').trim(),
    contact: String(body.contact || '').trim(),
    email: String(body.email || '').trim().toLowerCase(),
    course_id: body.course_id ? Number(body.course_id) : ''
  };
}

// =============================================================
// DASHBOARD
// =============================================================
router.get('/dashboard', async (req, res, next) => {
  try {
    const snap = await students.get();            // all students
    res.render('dashboard', { title: 'Dashboard', total: snap.size });
  } catch (err) { next(err); }
});

// =============================================================
// REGISTER STUDENT
// =============================================================
router.get('/register', async (req, res, next) => {
  try {
    res.render('register', { title: 'Register Student', courses: await getCourses(), errors: [], success: null, form: {} });
  } catch (err) { next(err); }
});

router.post('/register', async (req, res, next) => {
  try {
    const courseList = await getCourses();
    const form = studentFromForm(req.body);

    // 1. Server-side validation
    const errors = validateStudent(form, courseList.map((c) => String(c.course_id)));
    if (errors.length) {
      return res.render('register', { title: 'Register Student', courses: courseList, errors, success: null, form });
    }

    // 2. Save. The NIC is the document id, and create() FAILS if that id
    //    already exists - this is how we stop duplicate NICs (like a PRIMARY KEY).
    try {
      await students.doc(form.nic).create({ ...form, reg_date: new Date() });
    } catch (err) {
      if (err.code === 6) { // 6 = ALREADY_EXISTS
        return res.render('register', {
          title: 'Register Student', courses: courseList, success: null, form,
          errors: [`A student with NIC ${form.nic} is already registered.`]
        });
      }
      throw err;
    }

    // 3. Success -> show message and an empty form
    res.render('register', {
      title: 'Register Student', courses: courseList, errors: [], form: {},
      success: `Student ${form.name} (${form.nic}) registered successfully.`
    });
  } catch (err) { next(err); }
});

// =============================================================
// SEARCH STUDENTS (by NIC or part of a name; empty = show all)
// =============================================================
router.get('/search', async (req, res, next) => {
  try {
    const q = String(req.query.q || '').trim();
    const courseList = await getCourses();
    const snap = await students.get();
    let list = snap.docs.map((d) => toStudent(d, courseList));

    if (q) {
      // Firestore has no SQL "LIKE", so we filter in JavaScript.
      // This is fine for a campus-sized list of students.
      const term = q.toLowerCase();
      list = list.filter((s) => s.nic.toLowerCase().includes(term) || s.name.toLowerCase().includes(term));
    }
    list.sort((a, b) => a.name.localeCompare(b.name)); // A to Z

    res.render('search', { title: 'Search Students', q, results: list });
  } catch (err) { next(err); }
});

// =============================================================
// UPDATE STUDENT
// Step 1: GET /update?nic=...  -> load student into the form
// Step 2: POST /update         -> save the changes
// =============================================================
router.get('/update', async (req, res, next) => {
  try {
    const nic = cleanNic(req.query.nic);
    const courseList = await getCourses();
    let student = null;
    let errors = [];

    if (nic) {
      const doc = await students.doc(nic).get();
      if (doc.exists) student = doc.data();
      else errors = ['Student not found.'];
    }
    res.render('update', { title: 'Update Student', courses: courseList, nic, student, errors, success: null });
  } catch (err) { next(err); }
});

router.post('/update', async (req, res, next) => {
  try {
    const courseList = await getCourses();
    const form = studentFromForm(req.body);   // NIC comes from the read-only field
    const errors = validateStudent(form, courseList.map((c) => String(c.course_id)));

    if (errors.length) {
      return res.render('update', { title: 'Update Student', courses: courseList, nic: form.nic, student: form, errors, success: null });
    }

    const ref = students.doc(form.nic);
    if (!(await ref.get()).exists) {
      return res.render('update', { title: 'Update Student', courses: courseList, nic: form.nic, student: null, errors: ['Student not found.'], success: null });
    }

    // update() changes only these fields; NIC and reg_date stay the same
    const { nic, ...changes } = form;
    await ref.update(changes);

    res.render('update', {
      title: 'Update Student', courses: courseList, nic, student: form, errors: [],
      success: `Details of ${form.name} updated successfully.`
    });
  } catch (err) { next(err); }
});

// =============================================================
// DELETE STUDENT
// Step 1: GET /delete?nic=...  -> show details
// Step 2: POST /delete         -> delete (after JavaScript confirm())
// =============================================================
router.get('/delete', async (req, res, next) => {
  try {
    const nic = cleanNic(req.query.nic);
    let student = null;
    let errors = [];

    if (nic) {
      const doc = await students.doc(nic).get();
      if (doc.exists) student = toStudent(doc, await getCourses());
      else errors = ['Student not found.'];
    }
    res.render('delete', { title: 'Delete Student', nic, student, errors, success: null });
  } catch (err) { next(err); }
});

router.post('/delete', async (req, res, next) => {
  try {
    const nic = cleanNic(req.body.nic);
    const ref = students.doc(nic);
    const doc = nic ? await ref.get() : null;

    if (!doc || !doc.exists) {
      return res.render('delete', { title: 'Delete Student', nic, student: null, errors: ['Student not found.'], success: null });
    }

    await ref.delete();
    res.render('delete', {
      title: 'Delete Student', nic: '', student: null, errors: [],
      success: `Student ${doc.data().name} (${nic}) was deleted successfully.`
    });
  } catch (err) { next(err); }
});

module.exports = router;
