CREATE DATABASE IF NOT EXISTS cms;

USE cms;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'lecturer') NOT NULL
);

CREATE TABLE lecturers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    schedule TEXT
);

CREATE TABLE classrooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    equipment TEXT,
    available BOOLEAN DEFAULT TRUE
);

CREATE TABLE check_in_out (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lecturer_id INT,
    check_in_time DATETIME,
    check_out_time DATETIME,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id)
);
