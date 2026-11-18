// =============================================================
// utils/seed-data.js
// The starting data for the database, used by two places:
//   - seed.js        (npm run seed, on your PC)
//   - routes/setup.js (the one-time /setup page, e.g. from a phone)
// =============================================================
const bcrypt = require('bcryptjs');
const { db, admins, courses, students } = require('../config/firebase');

const COURSES = [
  'Diploma in ICT',
  'HND in Computing',
  'Diploma in English',
  'Diploma in Business Management',
  'Certificate in Graphic Design'
];

const STUDENTS = [
  { nic: '991234567V',   name: 'Kasun Perera',           gender: 'Male',   address: 'No. 45, Temple Road, Maharagama', contact: '0771234567', email: 'kasun.perera@gmail.com',    course_id: 1 },
  { nic: '200156701234', name: 'Nethmi Fernando',        gender: 'Female', address: '12/3, Galle Road, Moratuwa',      contact: '0712345678', email: 'nethmi.fernando@gmail.com', course_id: 2 },
  { nic: '985672341V',   name: 'Tharindu Jayasinghe',    gender: 'Male',   address: '78, Kandy Road, Kadawatha',       contact: '0759876543', email: 'tharindu.j@yahoo.com',      course_id: 4 },
  { nic: '200278904567', name: 'Sanduni Wickramasinghe', gender: 'Female', address: 'No. 9, Lake Drive, Kurunegala',   contact: '0703456789', email: 'sanduni.w@gmail.com',       course_id: 3 },
  { nic: '971845632X',   name: 'Mohamed Rizwan',         gender: 'Male',   address: '23, Main Street, Kalmunai',       contact: '0764567890', email: 'rizwan.m@outlook.com',      course_id: 5 }
];

// Writes the admin, the 5 courses and the 5 students.
// Running it again simply overwrites the same documents.
async function seedDatabase() {
  // Admin: password is hashed with bcrypt - never stored as plain text
  const hash = await bcrypt.hash('admin123', 10);

  const batch = db.batch();   // a batch saves many documents in one go
  batch.set(admins.doc('admin'), { username: 'admin', password: hash });

  // Courses: document id = course_id (like an AUTO_INCREMENT id)
  COURSES.forEach((name, i) => {
    batch.set(courses.doc(String(i + 1)), { course_id: i + 1, course_name: name });
  });

  // Students: document id = NIC, so a NIC can never be duplicated
  STUDENTS.forEach((s) => {
    batch.set(students.doc(s.nic), { ...s, reg_date: new Date() });
  });

  await batch.commit();
}

// True when no admin exists yet (the database has not been set up)
async function needsSetup() {
  const snap = await admins.limit(1).get();
  return snap.empty;
}

module.exports = { seedDatabase, needsSetup, COURSES };
