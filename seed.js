// =============================================================
// seed.js   (run with:  npm run seed)
// Fills the Firestore database with starting data:
//   - 1 admin   (username: admin, password: admin123 - stored as a bcrypt hash)
//   - 5 courses
//   - 5 sample students
// This replaces the campus_db.sql file of a MySQL project.
// No PC? Open /setup on the website instead (works once, on an empty database).
// =============================================================
const { configError } = require('./config/firebase');
const { seedDatabase } = require('./utils/seed-data');

(async () => {
  if (configError) throw new Error(configError);
  await seedDatabase();
  console.log('Admin created  -> username: admin  password: admin123');
  console.log('5 courses and 5 students added.');
  console.log('Done! Now run:  npm start');
  process.exit(0);
})().catch((err) => {
  console.error('Seeding failed:', err.message);
  process.exit(1);
});
