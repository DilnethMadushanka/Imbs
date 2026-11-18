// =============================================================
// config/firebase.js
// Connects the app to the Firebase Firestore database.
// (This plays the same role as db.php in a PHP project.)
//
// Where the key comes from (first one found wins):
//   1. FIREBASE_SERVICE_ACCOUNT environment variable (used on Vercel)
//   2. FIRESTORE_EMULATOR_HOST (local test database, optional)
//   3. config/serviceAccountKey.json (used on your PC)
// If none works, "configError" explains why, and server.js shows that
// message on every page instead of crashing.
// =============================================================
const fs = require('fs');
const path = require('path');
const admin = require('firebase-admin');   // official Firebase server SDK

const keyPath = path.join(__dirname, 'serviceAccountKey.json');
let configError = null;

// Reads the key from the environment variable and checks it looks right
function keyFromEnv(text) {
  let key;
  try {
    key = JSON.parse(text.trim());
  } catch (e) {
    throw new Error('FIREBASE_SERVICE_ACCOUNT is not valid JSON. Paste the WHOLE serviceAccountKey.json file, from the first { to the last }.');
  }
  if (!key.project_id || !key.private_key || !key.client_email) {
    throw new Error('FIREBASE_SERVICE_ACCOUNT is missing project_id, private_key or client_email. Use the key from Project settings > Service accounts > Generate new private key.');
  }
  // Some copy-paste tools turn line breaks into the text "\n" - put them back
  key.private_key = key.private_key.replace(/\\n/g, '\n');
  return key;
}

try {
  if (admin.apps.length) {
    // Already connected (Vercel can reuse a warm function) - do nothing.
  } else if (process.env.FIREBASE_SERVICE_ACCOUNT) {
    admin.initializeApp({ credential: admin.credential.cert(keyFromEnv(process.env.FIREBASE_SERVICE_ACCOUNT)) });
  } else if (process.env.FIRESTORE_EMULATOR_HOST) {
    admin.initializeApp({ projectId: process.env.GCLOUD_PROJECT || 'campus-demo' });
  } else if (fs.existsSync(keyPath)) {
    admin.initializeApp({ credential: admin.credential.cert(require(keyPath)) });
  } else {
    throw new Error(process.env.VERCEL
      ? 'The FIREBASE_SERVICE_ACCOUNT environment variable is not set. Add it in Vercel > Project > Settings > Environment Variables, then Redeploy.'
      : 'config/serviceAccountKey.json not found. Download it from Firebase Console > Project settings > Service accounts > Generate new private key.');
  }
} catch (err) {
  configError = err.message;
  console.error('\n[Firebase setup error] ' + configError + '\n');
  // Start a placeholder app so the rest of the code can load; pages show the error.
  if (!admin.apps.length) admin.initializeApp({ projectId: 'not-configured' });
}

// "db" is our Firestore database object, used by all routes.
const db = admin.firestore();

// Collection names (like table names in MySQL)
module.exports = {
  db,
  configError,
  admins: db.collection('admin'),      // document id = username
  courses: db.collection('course'),    // document id = course_id
  students: db.collection('student')   // document id = NIC (acts as the primary key)
};
