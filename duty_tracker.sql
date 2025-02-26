-- Create the database
CREATE DATABASE duty_tracker;
USE duty_tracker;

-- Admin Table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Hashed password
    role ENUM('Super Admin', 'Department Admin') DEFAULT 'Department Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,  -- Unique student identifier
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Hashed password
    scholarship_type VARCHAR(100),
    course VARCHAR(100) NOT NULL,  
    department VARCHAR(100) NOT NULL,  
    hk_duty_status VARCHAR(100) NOT NULL,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    year_level VARCHAR(50) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Inactive',
    total_hours DECIMAL(10,2) DEFAULT 0.00  -- Total accumulated duty hours
);

-- Duty Logs Table
CREATE TABLE duty_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,  -- Matches students.student_id
    duty_date DATE DEFAULT NULL,  -- Date of duty log
    time_in TIME NOT NULL,  -- Check-in time
    time_out TIME NOT NULL,  -- Check-out time
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',  
    admin_id INT NULL,  -- Admin who approves/rejects the duty log
    approved_at TIMESTAMP NULL,  -- Timestamp when approved/rejected
    duration FLOAT NOT NULL,  -- Duration of duty session
    log_date DATE DEFAULT CURRENT_TIMESTAMP,  -- System-generated log date
    hours_worked DECIMAL(5,2) NOT NULL,  -- Hours worked in this session
    total_hours DECIMAL(10,2) NOT NULL DEFAULT 0.00,  -- Running total of student's hours
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL
);

-- Notifications Table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- Can be either an Admin or Student ID
    role ENUM('Admin', 'Student') NOT NULL,  
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Email Verification Table
CREATE TABLE email_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- Can be either a Student or Admin ID
    role ENUM('Admin', 'Student') NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert a default Super Admin
INSERT INTO admin (name, email, password, role) 
VALUES ('Super Admin', 'admin@example.com', 'HASHED_PASSWORD_HERE', 'Super Admin');
