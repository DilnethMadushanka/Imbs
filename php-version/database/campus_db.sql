-- =============================================================
--  campus_db.sql  -  Database for the Trivex Campus website
--  Module: CIT1074 - Web Application Development Project
--
--  HOW TO USE: open phpMyAdmin (http://localhost/phpmyadmin)
--  -> "Import" tab -> choose this file -> click "Go".
--  It creates the database, the 3 tables and the sample data.
-- =============================================================

-- Create the database (only if it does not exist yet)
CREATE DATABASE IF NOT EXISTS campus_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

-- Work inside this database for the commands below
USE campus_db;

-- Remove old tables so the file can be imported again safely.
-- student is dropped first because it depends on course (foreign key).
DROP TABLE IF EXISTS student;
DROP TABLE IF EXISTS course;
DROP TABLE IF EXISTS admin;

-- -------------------------------------------------------------
-- Table: admin  (people who can log in to the admin panel)
-- -------------------------------------------------------------
CREATE TABLE admin (
  id       INT AUTO_INCREMENT PRIMARY KEY,   -- unique number for each admin
  username VARCHAR(50)  NOT NULL UNIQUE,     -- login name (no duplicates)
  password VARCHAR(255) NOT NULL             -- hashed password, never plain text
) ENGINE=InnoDB;

-- Default admin:  username = admin   password = admin123
-- The long text is the result of PHP password_hash('admin123', PASSWORD_DEFAULT).
-- login.php checks it with password_verify().
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$.Q6n.QRCBW79A2dtrjOSLeLU9KAxLBoKUZMGCkoUkI1aYO4oJ6PxC');

-- -------------------------------------------------------------
-- Table: course  (courses offered by the campus)
-- -------------------------------------------------------------
CREATE TABLE course (
  course_id   INT AUTO_INCREMENT PRIMARY KEY,  -- unique course number
  course_name VARCHAR(100) NOT NULL            -- name of the course
) ENGINE=InnoDB;

INSERT INTO course (course_name) VALUES
('Diploma in ICT'),
('HND in Computing'),
('Diploma in English'),
('Diploma in Business Management'),
('Certificate in Graphic Design');

-- -------------------------------------------------------------
-- Table: student  (registered students)
-- -------------------------------------------------------------
CREATE TABLE student (
  nic       VARCHAR(12)  NOT NULL PRIMARY KEY,    -- National Identity Card number (unique)
  name      VARCHAR(100) NOT NULL,                -- full name
  gender    ENUM('Male','Female') NOT NULL,       -- only these two values allowed
  address   VARCHAR(255) NOT NULL,                -- home address
  contact   VARCHAR(10)  NOT NULL,                -- 10 digit phone number
  email     VARCHAR(100) NOT NULL,                -- email address
  course_id INT NOT NULL,                         -- the course the student follows
  reg_date  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- filled in automatically
  -- Foreign key: course_id must exist in the course table
  CONSTRAINT fk_student_course FOREIGN KEY (course_id)
    REFERENCES course(course_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 5 sample students (old 9-digit + V/X NICs and new 12-digit NICs)
INSERT INTO student (nic, name, gender, address, contact, email, course_id) VALUES
('991234567V',   'Kasun Perera',           'Male',   'No. 45, Temple Road, Maharagama', '0771234567', 'kasun.perera@gmail.com',    1),
('200156701234', 'Nethmi Fernando',        'Female', '12/3, Galle Road, Moratuwa',      '0712345678', 'nethmi.fernando@gmail.com', 2),
('985672341V',   'Tharindu Jayasinghe',    'Male',   '78, Kandy Road, Kadawatha',       '0759876543', 'tharindu.j@yahoo.com',      4),
('200278904567', 'Sanduni Wickramasinghe', 'Female', 'No. 9, Lake Drive, Kurunegala',   '0703456789', 'sanduni.w@gmail.com',       3),
('971845632X',   'Mohamed Rizwan',         'Male',   '23, Main Street, Kalmunai',       '0764567890', 'rizwan.m@outlook.com',      5);
