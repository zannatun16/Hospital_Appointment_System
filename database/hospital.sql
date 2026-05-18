

CREATE DATABASE IF NOT EXISTS HospitalAppointment;
USE HospitalAppointment;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('patient', 'doctor', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE specializations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization_id INT NOT NULL,
    consultation_fee DECIMAL(10,2) NOT NULL,
    experience_years INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE CASCADE
);


CREATE TABLE doctor_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    slot_duration_minutes INT DEFAULT 30,
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);


CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);


CREATE TABLE consultation_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    diagnosis TEXT NOT NULL,
    prescription TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date_of_birth DATE NOT NULL,
    blood_group VARCHAR(10) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    UNIQUE KEY unique_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE doctor_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_appointment_review (appointment_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);


INSERT INTO specializations (name) VALUES
('Cardiology'),
('Neurology'),
('Pediatrics'),
('Orthopedics'),
('Dermatology'),
('Gynecology'),
('Ophthalmology'),
('ENT Specialist');


INSERT INTO users (name, email, password_hash, phone, role) VALUES
( 'Zoita', 'Zoita@gmail.com', '$2y$10$97kb54Tl1WY9aLgRV6Wm.OeAP/1ZnFBbdlo.TEKCAu5OPc5jviAGu', '01711111111', 'admin');


INSERT INTO users (name, email, password_hash, phone, role) VALUES
 ('Sadman', 'Sadman@gmail.com', '$2y$10$ZZ4ZePKsjaPK76f6GRAH9u2usl62brtMfRUS/.lPL4JQ2nbTtF1tG', '01722222222', 'patient');


INSERT INTO users (name, email, password_hash, phone, role) VALUES
('Mayeesha', 'Mayeesha@gmail.com', '$2y$10$V0PqVNw5a53CsdYIVLy7IeKWwvwBEon.auyVGhgp476i4YhG3HUTu', '01733333333', 'doctor');


INSERT INTO doctors (user_id, specialization_id, consultation_fee, experience_years) VALUES
(3, 1, 800.00, 10);

-- Note: Demo account passwords and hashes
-- Admin: admin123 -> $2y$10$97kb54Tl1WY9aLgRV6Wm.OeAP/1ZnFBbdlo.TEKCAu5OPc5jviAGu
-- Patient: Patient123 -> $2y$10$ZZ4ZePKsjaPK76f6GRAH9u2usl62brtMfRUS/.lPL4JQ2nbTtF1tG
-- Doctor: Doctor123 -> $2y$10$V0PqVNw5a53CsdYIVLy7IeKWwvwBEon.auyVGhgp476i4YhG3HUTu