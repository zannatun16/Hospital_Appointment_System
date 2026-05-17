<?php
// Admin Model - Mayeesha
// Location: HospitalAppointmentSystem/models/adminModelM.php

require_once dirname(__DIR__) . '/config/database.php';

// Get all doctors
function getAllDoctorsM($conn) {
    $sql = "SELECT u.id as user_id, u.name, u.email, u.phone, 
                   d.id as doctor_id, d.consultation_fee, d.experience_years,
                   s.id as specialization_id, s.name as specialization
            FROM users u
            INNER JOIN doctors d ON u.id = d.user_id
            INNER JOIN specializations s ON d.specialization_id = s.id
            WHERE u.role = 'doctor'
            ORDER BY u.name";
    
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get all specializations
function getAllSpecializationsM($conn) {
    $sql = "SELECT * FROM specializations ORDER BY name";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Add doctor
function addDoctorM($conn, $name, $email, $phone, $password, $specialization_id, $consultation_fee, $experience_years) {
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert into users table
    $sql1 = "INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'doctor')";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "ssss", $name, $email, $phone, $hashed_password);
    
    if (mysqli_stmt_execute($stmt1)) {
        $user_id = mysqli_insert_id($conn);
        
        // Insert into doctors table
        $sql2 = "INSERT INTO doctors (user_id, specialization_id, consultation_fee, experience_years) VALUES (?, ?, ?, ?)";
        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "iidi", $user_id, $specialization_id, $consultation_fee, $experience_years);
        
        return mysqli_stmt_execute($stmt2);
    }
    
    return false;
}

// Update doctor
function updateDoctorM($conn, $user_id, $doctor_id, $name, $phone, $specialization_id, $consultation_fee, $experience_years) {
    // Update users table
    $sql1 = "UPDATE users SET name = ?, phone = ? WHERE id = ?";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "ssi", $name, $phone, $user_id);
    
    // Update doctors table
    $sql2 = "UPDATE doctors SET specialization_id = ?, consultation_fee = ?, experience_years = ? WHERE id = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "idii", $specialization_id, $consultation_fee, $experience_years, $doctor_id);
    
    return mysqli_stmt_execute($stmt1) && mysqli_stmt_execute($stmt2);
}

// Delete doctor
function deleteDoctorM($conn, $user_id) {
    $sql = "DELETE FROM users WHERE id = ? AND role = 'doctor'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    return mysqli_stmt_execute($stmt);
}

// Add specialization
function addSpecializationM($conn, $name) {
    $sql = "INSERT INTO specializations (name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $name);
    return mysqli_stmt_execute($stmt);
}

// Update specialization
function updateSpecializationM($conn, $id, $name) {
    $sql = "UPDATE specializations SET name = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $name, $id);
    return mysqli_stmt_execute($stmt);
}

// Delete specialization
function deleteSpecializationM($conn, $id) {
    $sql = "DELETE FROM specializations WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

// Get all appointments
function getAllAppointmentsM($conn) {
    $sql = "SELECT a.*, 
                   p.name as patient_name, p.email as patient_email,
                   d.name as doctor_name, d.email as doctor_email,
                   s.name as specialization
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors doc ON a.doctor_id = doc.id
            INNER JOIN users d ON doc.user_id = d.id
            INNER JOIN specializations s ON doc.specialization_id = s.id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get all patients
function getAllPatientsM($conn) {
    $sql = "SELECT id, name, email, phone, created_at FROM users WHERE role = 'patient' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Delete patient
function deletePatientM($conn, $user_id) {
    $sql = "DELETE FROM users WHERE id = ? AND role = 'patient'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    return mysqli_stmt_execute($stmt);
}

// Get dashboard stats
function getDashboardStatsM($conn) {
    $stats = [];
    
    // Total doctors
    $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'doctor'";
    $result = mysqli_query($conn, $sql);
    $stats['total_doctors'] = mysqli_fetch_assoc($result)['total'];
    
    // Total patients
    $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'patient'";
    $result = mysqli_query($conn, $sql);
    $stats['total_patients'] = mysqli_fetch_assoc($result)['total'];
    
    // Total appointments
    $sql = "SELECT COUNT(*) as total FROM appointments";
    $result = mysqli_query($conn, $sql);
    $stats['total_appointments'] = mysqli_fetch_assoc($result)['total'];
    
    // Pending appointments
    $sql = "SELECT COUNT(*) as total FROM appointments WHERE status = 'pending'";
    $result = mysqli_query($conn, $sql);
    $stats['pending_appointments'] = mysqli_fetch_assoc($result)['total'];
    
    // Completed appointments
    $sql = "SELECT COUNT(*) as total FROM appointments WHERE status = 'completed'";
    $result = mysqli_query($conn, $sql);
    $stats['completed_appointments'] = mysqli_fetch_assoc($result)['total'];
    
    return $stats;
}
?>