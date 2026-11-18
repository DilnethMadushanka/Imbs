# Horizon Campus – Student Management System

CIT1074 – Web Application Development Project
Built with **Node.js + Express**, **Firebase Cloud Firestore**, **HTML5 (EJS templates)**, **hand-written CSS3** and **vanilla JavaScript**. No CSS framework.

> **Change the campus name:** open `config/site.js` and edit `CAMPUS_NAME` (and the address, email and opening hours under it). Every page reads it from there.

---

## 1. Folder structure

```
campus/
├── server.js                  Main file: starts the Express web server
├── seed.js                    Fills Firestore with the admin, 5 courses and 5 students
├── package.json               Project info + list of libraries (npm install reads it)
├── config/
│   ├── site.js                Campus name, address, email, hours, port
│   ├── firebase.js            Connects to Firestore (like db.php)
│   └── serviceAccountKey.example.json   Shows where your Firebase key goes
├── middleware/
│   └── auth.js                Blocks admin pages unless logged in (like auth.php)
├── routes/
│   ├── auth.js                Home page, login, logout
│   └── students.js            Dashboard, register, search, update, delete
├── utils/
│   └── validation.js          Server-side validation (NIC, contact, email, required)
├── views/                     HTML pages (EJS templates)
│   ├── partials/header.ejs    Navbar on every page (like header.php)
│   ├── partials/footer.ejs    Footer on every page (like footer.php)
│   ├── partials/student-form.ejs  Form shared by Register and Update
│   ├── partials/alerts.ejs    Success / error boxes
│   ├── index.ejs  login.ejs  dashboard.ejs  register.ejs
│   ├── search.ejs  update.ejs  delete.ejs  message.ejs
└── public/                    Files sent straight to the browser
    ├── css/style.css          All styling
    ├── js/main.js             Client-side validation, mobile menu, delete confirm
    └── images/                Logo + campus photographs (hero, library, courses, corridor)
```

## 2. Database (Firestore collections)

Firestore is a NoSQL database: it stores **documents** inside **collections** instead of rows inside tables.

| Collection | Document id | Fields |
|---|---|---|
| `admin` | the username (`admin`) | `username`, `password` (bcrypt hash) |
| `course` | `1` … `5` | `course_id`, `course_name` |
| `student` | the **NIC** | `nic`, `name`, `gender`, `address`, `contact`, `email`, `course_id`, `reg_date` |

* The NIC is the student's document id, so it works like a **primary key**: registering uses `create()`, which fails if that NIC already exists.
* `course_id` in a student links to a course document (like a foreign key).
* Default login: **username `admin` / password `admin123`** (stored with `bcrypt`, checked with `bcrypt.compare()` – the Node.js version of `password_hash()` / `password_verify()`).

## 3. Setup, step by step (Windows)

### Step 1 – Install Node.js
Download the **LTS** version from https://nodejs.org and install it (keep the default options).
Check it in Command Prompt:
```
node -v
npm -v
```

### Step 2 – Create the Firebase project and database
1. Go to https://console.firebase.google.com and sign in with a Google account.
2. Click **Add project** (or **Create a project**), give it a name such as `horizon-campus`, and finish the steps (Google Analytics is not needed).
3. In the left menu open **Build → Firestore Database** and click **Create database**.
   Choose a location close to you (for example `asia-south1`), and start in **production mode** (our server uses the Admin SDK, which is allowed anyway).

### Step 3 – Download the service account key
1. In the Firebase console click the **gear icon → Project settings → Service accounts**.
2. Click **Generate new private key** → **Generate key**. A `.json` file downloads.
3. Rename it to **`serviceAccountKey.json`** and put it in the **`campus/config/`** folder.
   (Keep this file private. Never upload it to GitHub – `.gitignore` already excludes it.)

### Step 4 – Install the libraries
Open Command Prompt **inside the `campus` folder** (in File Explorer, type `cmd` in the address bar and press Enter), then run:
```
npm install
```

### Step 5 – Add the starting data
```
npm run seed
```
You should see `Admin created`, `5 courses and 5 students added.`

### Step 6 – Start the website
```
npm start
```
Open **http://localhost:3000** in your browser.
Click **Admin login** and sign in with `admin` / `admin123`.

To stop the server press **Ctrl + C** in the Command Prompt window.

## 4. Features

| Page | URL | What it does |
|---|---|---|
| Home | `/` | Hero, about the campus, courses (loaded from Firestore), contact, Admin login button |
| Login | `/login` | Username + password, bcrypt check, session started, error on wrong details |
| Dashboard | `/dashboard` | 4 cards (Register, Search, Update, Delete) + total number of students |
| Register | `/register` | Full form with gender radio buttons, address textarea, course drop-down from Firestore, guidelines list beside it, duplicate NIC error, success message |
| Search | `/search` | Search by NIC or part of a name; empty search shows every student in a styled table |
| Update | `/update` | Enter NIC → pre-filled form (NIC read-only) → save; "Student not found" if missing |
| Delete | `/delete` | Enter NIC → student details → JavaScript `confirm("Are you sure…?")` → delete → success message |
| Logout | `/logout` | Destroys the session and returns to login |

All admin pages go through `middleware/auth.js`; opening them without logging in sends you to `/login`.

## 5. Validation and security

* **Client side** (`public/js/main.js` + HTML5 `required`, `pattern`, `type="email"`): the browser stops a wrong form and shows the message under the field.
* **Server side** (`utils/validation.js`): every rule is checked again, because browser checks can be switched off.
  * NIC: `^([0-9]{9}[VvXx]|[0-9]{12})$` – old format 991234567V or new format 200012345678
  * Contact: `^0[0-9]{9}$` – 10 digits starting with 0
  * Email: must look like `name@domain.tld`
  * All fields required, gender must be Male/Female, course must exist
* **No injection:** Firestore is not SQL, and every value is saved as a plain field, so SQL injection is not possible. Only the expected fields are copied from the form.
* **Output escaping:** EJS `<%= %>` escapes HTML (same job as `htmlspecialchars()`), so a name like `<script>` is shown as text.
* **Passwords:** stored only as bcrypt hashes. A new session id is created on login.

## 6. Design

* Palette: deep navy `#14223f`, warm gold `#c8962e`, paper white `#f7f4ee` (all in `:root` at the top of `style.css`).
* Fonts: **Fraunces** for headings and **Manrope** for text (Google Fonts).
* Design rules taken from the *taste-skill* guidance: flat colours (no gradients), equal left/right page edges, the hero alone on the first screen and entering once on load, the campus name set large as one "type moment", cards that lift 2px on hover, photos that zoom slightly inside their frame, buttons that step up on hover and press down on click, no fade-ins while scrolling, and everything still when the user turns off animations.
* Sticky navbar, full-screen menu on phones, rounded tables with alternating rows, success/error alert boxes, responsive down to 320px.

**Pictures** (in `public/images/`, WebP format to keep the page fast):
`hero.webp` + `hero-900.webp` (home hero, phones load the smaller one), `library.webp` (about section), `course-ict/hnd/english/business/design.webp` (course cards) and `corridor.webp` (login background).
To swap a picture, save a new file with the same name. The course pictures are matched to course names in `views/index.ejs` (the `pics` list).

**Course text:** course durations and descriptions on the home page are sample text in `views/index.ejs` – edit them to match your campus.

## 7. Common problems

| Message | Fix |
|---|---|
| `config/serviceAccountKey.json not found` | Do Step 3 and check the file name and folder. |
| `5 NOT_FOUND` when seeding | The Firestore database was not created yet – do Step 2.3. |
| `EADDRINUSE: port 3000` | Another program uses port 3000. Close it, or change `PORT` in `config/site.js`. |
| `'npm' is not recognized` | Node.js is not installed, or restart Command Prompt after installing. |
