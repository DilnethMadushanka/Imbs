// =============================================================
// config/firebase.js
// Connects the app to the Firebase Firestore database.
// (This plays the same role as db.php in a PHP project.)
// =============================================================
const fs = require('fs');
const path = require('path');
const admin = require('firebase-admin');   // official Firebase server SDK

const keyPath = path.join(__dirname, 'serviceAccountKey.json');

if (process.env.FIRESTORE_EMULATOR_HOST) {
  // Optional: local Firestore emulator (used for testing only).
  admin.initializeApp({ projectId: process.env.GCLOUD_PROJECT || 'campus-demo' });
} else if (fs.existsSync(keyPath)) {
  // Normal case: log in to Firebase with the downloaded service-account key.
  admin.initializeApp({ credential: admin.credential.cert(require(keyPath)) });
} else {
  // Friendly error if the key has not been added yet.
  console.error('\n[ERROR] config/serviceAccountKey.json not found.');
  console.error('Download it from Firebase Console > Project settings > Service accounts');
  console.error('> "Generate new private key", and save it as config/serviceAccountKey.json\n');
  process.exit(1);
}

// "db" is our Firestore database object, used by all routes.
const db = admin.firestore();

// Collection names (like table names in MySQL)
module.exports = {
  db,
  admins: db.collection('admin'),      // document id = username
  courses: db.collection('course'),    // document id = course_id
  students: db.collection('student')   // document id = NIC (acts as the primary key)
};
