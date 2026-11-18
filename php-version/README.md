# Trivex Campus – Student Management System (PHP + MySQL)

CIT1074 – Web Application Development Project
Built with **PHP (procedural, mysqli prepared statements)**, **MySQL**, **HTML5**, **hand-written CSS3** and a little **vanilla JavaScript**, running on **WAMP server**. No frameworks.

> **Change the campus name:** open `includes/config.php` and edit `CAMPUS_NAME` (address, email and hours are under it).

## 1. Folder structure

```
campus/
├── index.php            Public home page: hero, about, courses (from DB), contact, Admin login button
├── login.php            Admin login form (password_verify)
├── logout.php           Ends the session
├── dashboard.php        4 cards (Register, Search, Update, Delete) + total students
├── register.php         Register a student (form + guidelines list)
├── search.php           Search by NIC or part of a name (empty = show all)
├── update.php           Load by NIC -> pre-filled form (NIC read-only) -> save
├── delete.php           Find by NIC -> details -> confirm("Are you sure?") -> delete
├── includes/
│   ├── config.php       Campus name + database login details
│   ├── db.php           mysqli connection + e() helper (htmlspecialchars)
│   ├── auth.php         Session check: sends to login.php if not logged in
│   ├── header.php       <head> + sticky navbar (same on every page)
│   ├── footer.php       Footer + JavaScript (same on every page)
│   ├── validation.php   Server-side validation (NIC, contact, email, required)
│   ├── student_form.php Form shared by Register and Update
│   └── alerts.php       Success / error boxes
├── css/style.css        All styling
├── js/main.js           Client-side validation, mobile menu, delete confirm
├── images/              Logo, campus photographs
└── database/campus_db.sql   CREATE DATABASE + tables + sample data
```

## 2. Database: `campus_db`

| Table | Columns |
|---|---|
| `admin` | `id` INT AI PK, `username` VARCHAR(50) UNIQUE, `password` VARCHAR(255) (hashed) |
| `course` | `course_id` INT AI PK, `course_name` VARCHAR(100) – 5 sample courses |
| `student` | `nic` VARCHAR(12) **PK**, `name`, `gender` ENUM('Male','Female'), `address`, `contact` VARCHAR(10), `email`, `course_id` **FK → course**, `reg_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP – 5 sample students |

Default login: **username `admin` / password `admin123`** (stored with `password_hash()`).

## 3. How to run on WAMP (step by step)

1. **Install WAMP** from https://www.wampserver.com and start it. Wait until the WAMP icon in the taskbar turns **green**.
2. **Copy the folder:** copy this whole `campus` folder into `C:\wamp64\www\`
   so you have `C:\wamp64\www\campus\index.php`.
3. **Import the database:**
   1. Open **http://localhost/phpmyadmin** (username `root`, password empty, click **Log in**).
   2. Click the **Import** tab at the top.
   3. Click **Choose File** and select `C:\wamp64\www\campus\database\campus_db.sql`.
   4. Click **Import** (or **Go**) at the bottom. You should see `campus_db` in the left list with 3 tables.
4. **Open the website:** go to **http://localhost/campus/**
5. Click **Admin login** and sign in with `admin` / `admin123`.

If your MySQL root user has a password, put it in `DB_PASS` in `includes/config.php`.

## 4. Validation and security (for the viva)

* **Client side:** HTML5 `required`, `pattern`, `type="email"` + `js/main.js` shows a message under each field.
* **Server side:** `includes/validation.php` checks everything again:
  * NIC `^([0-9]{9}[VX]|[0-9]{12})$` – 991234567V or 200012345678
  * Contact `^0[0-9]{9}$` – 10 digits starting with 0
  * Email with `filter_var(..., FILTER_VALIDATE_EMAIL)`
  * All fields required, gender Male/Female, course must exist
* **SQL injection:** every query that uses user input is a **prepared statement** (`mysqli_prepare` + `mysqli_stmt_bind_param`).
* **XSS:** all output goes through `e()` = `htmlspecialchars()`.
* **Passwords:** `password_hash()` / `password_verify()`; `session_regenerate_id()` on login.
* **Duplicate NIC:** checked before INSERT, and NIC is the PRIMARY KEY.

## 5. Hosting online (optional)

Vercel cannot run PHP + MySQL. Free PHP hosts such as **InfinityFree** work:
create an account, create a MySQL database in its control panel, import `campus_db.sql` in its phpMyAdmin
(delete the first two lines `CREATE DATABASE` / `USE` first, because the host gives you a database),
put that host's database name, user and password in `includes/config.php`, and upload all files to `htdocs`.
