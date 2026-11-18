// =============================================================
// seed.js   (run with:  npm run seed)
// Fills the Firestore database with starting data:
//   - 1 admin   (username: admin, password: admin123 - stored as a bcrypt hash)
//   - 5 courses
//   - 5 sample students
// This replaces the campus_db.sql file of a MySQL project.
// Running it again simply overwrites the same documents.
// =============================================================
const bcrypt = require('bcryptjs');
const { db, admins, courses, students } = require('./config/firebase');

async function seed() {
  // ---- Admin (password is hashed - never store plain passwords) ----
  const hash = await bcrypt.hash('admin123', 10);
  await admins.doc('admin').set({ username: 'admin', password: hash });
  console.log('Admin created  -> username: admin  password: admin123');

  // ---- Courses (document id = course_id, like an AUTO_INCREMENT id) ----
  const courseList = [
    'Diploma in ICT',
    'HND in Computing',
    'Diploma in English',
    'Diploma in Business Management',
    'Certificate in Graphic Design'
  ];
  const batch = db.batch();                 // a batch saves many documents in one go
  courseList.forEach((name, i) => {
    const id = String(i + 1);
    batch.set(courses.doc(id), { course_id: i + 1, course_name: name });
  });

  // ---- Students (document id = NIC, so a NIC can never be duplicated) ----
  const studentList = [
    { nic: '991234567V',   name: 'Kasun Perera',           gender: 'Male',   address: 'No. 45, Temple Road, Maharagama', contact: '0771234567', email: 'kasun.perera@gmail.com',    course_id: 1 },
    { nic: '200156701234', name: 'Nethmi Fernando',        gender: 'Female', address: '12/3, Galle Road, Moratuwa',      contact: '0712345678', email: 'nethmi.fernando@gmail.com', course_id: 2 },
    { nic: '985672341V',   name: 'Tharindu Jayasinghe',    gender: 'Male',   address: '78, Kandy Road, Kadawatha',       contact: '0759876543', email: 'tharindu.j@yahoo.com',      course_id: 4 },
    { nic: '200278904567', name: 'Sanduni Wickramasinghe', gender: 'Female', address: 'No. 9, Lake Drive, Kurunegala',   contact: '0703456789', email: 'sanduni.w@gmail.com',       course_id: 3 },
    { nic: '971845632X',   name: 'Mohamed Rizwan',         gender: 'Male',   address: '23, Main Street, Kalmunai',       contact: '0764567890', email: 'rizwan.m@outlook.com',      course_id: 5 }
  ];
  studentList.forEach((s) => {
    batch.set(students.doc(s.nic), { ...s, reg_date: new Date() });
  });

  await batch.commit();
  console.log('5 courses and 5 students added.');
  console.log('Done! Now run:  npm start');
  process.exit(0);
}

seed().catch((err) => {
  console.error('Seeding failed:', err.message);
  process.exit(1);
});
