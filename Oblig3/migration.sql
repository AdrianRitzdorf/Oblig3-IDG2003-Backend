-- creating the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS CORAX_db;

-- selecting corax_db
USE CORAX_db;

-- creating admin table
CREATE table admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- creating employees table
CREATE table employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    photo_path VARCHAR(255) DEFAULT NULL
);