-- Create database
CREATE DATABASE IF NOT EXISTS cms;
USE cms;

-- Create users table for login
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL,
    role ENUM('admin', 'lecturer') NOT NULL
);

-- Create lecturers table
CREATE TABLE lecturers (
    lecturer_id CHAR(4) PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    phone VARCHAR(20),
    course_code CHAR(7),
    course_name VARCHAR(50),
    date DATE,
    time TIME,
    note TEXT
);

-- Create classrooms table
CREATE TABLE classrooms (
    id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    equipment TEXT,
    available BOOLEAN DEFAULT TRUE
);

-- Create schedule table for managing classroom schedules
CREATE TABLE schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lecturer_id CHAR(4),
    classroom_id VARCHAR(10),
    day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
    scheduled_time TIME,
    check_in_time DATETIME,
    check_out_time DATETIME,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(lecturer_id),
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id)
);

-- Create check_in_out table
CREATE TABLE check_in_out (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lecturer_id CHAR(4),
    classroom_id VARCHAR(10),
    check_in_time DATETIME,
    check_out_time DATETIME,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(lecturer_id),
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id)
);

-- Create logs view for monitoring check-in/out times
CREATE VIEW logs AS
SELECT
    c.id as classroom_id,
    l.lecturer_id,
    l.name as lecturer_name,
    c.name as classroom_name,
    cio.check_in_time,
    cio.check_out_time
FROM
    check_in_out cio
    JOIN lecturers l ON cio.lecturer_id = l.lecturer_id
    JOIN classrooms c ON cio.classroom_id = c.id;

-- Add initial admin user
INSERT INTO users (username, password, role) VALUES ('admin', 'admin_password', 'admin');
