<?php
// Patient Model - Sadman
// Location: HospitalAppointmentSystem/models/patientModelS.php

require_once dirname(__DIR__) . '/config/database.php';

// Get all doctors
function getAllDoctorsS($conn) {
    $sql = "SELECT u.id as user_id, u.name, u.email, u.phone, 
                   d.id as doctor_id, d.consultation_fee, d.experience_years,
                   s.name as specialization
            FROM users u
            INNER JOIN doctors d ON u.id = d.user_id
            INNER JOIN specializations s ON d.specialization_id = s.id
            WHERE u.role = 'doctor'
            ORDER BY u.name";
    
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getDoctorAvailabilityS($conn, $doctor_id) {
    $sql = "SELECT day_of_week, start_time, end_time, slot_duration_minutes, is_available
            FROM doctor_availability
            WHERE doctor_id = ? AND is_available = 1
            ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), start_time";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Search doctors by name or specialization
function searchDoctorsS($conn, $search_term) {
    $sql = "SELECT u.id as user_id, u.name, u.email, u.phone, 
                   d.id as doctor_id, d.consultation_fee, d.experience_years,
                   s.name as specialization
            FROM users u
            INNER JOIN doctors d ON u.id = d.user_id
            INNER JOIN specializations s ON d.specialization_id = s.id
            WHERE u.role = 'doctor' 
            AND (u.name LIKE ? OR s.name LIKE ?)
            ORDER BY u.name";
    
    $stmt = mysqli_prepare($conn, $sql);
    $search_pattern = "%$search_term%";
    mysqli_stmt_bind_param($stmt, "ss", $search_pattern, $search_pattern);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Book an appointment
function bookAppointmentS($conn, $patient_id, $doctor_id, $date, $time, $reason) {
    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iisss", $patient_id, $doctor_id, $date, $time, $reason);
    return mysqli_stmt_execute($stmt);
}

// Get patient appointments
function getPatientAppointmentsS($conn, $patient_id) {
    $sql = "SELECT a.*, u.name as doctor_name, d.id as doctor_id, d.consultation_fee, s.name as specialization,
                   dr.id as review_id, dr.rating, dr.review_text
            FROM appointments a
            INNER JOIN doctors d ON a.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id
            INNER JOIN specializations s ON d.specialization_id = s.id
            LEFT JOIN doctor_reviews dr ON a.id = dr.appointment_id AND dr.patient_id = ?
            WHERE a.patient_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $patient_id, $patient_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Add doctor review
function addDoctorReviewS($conn, $appointment_id, $patient_id, $doctor_id, $rating, $review_text) {
    $sql = "INSERT INTO doctor_reviews (appointment_id, patient_id, doctor_id, rating, review_text)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiiis", $appointment_id, $patient_id, $doctor_id, $rating, $review_text);
    return mysqli_stmt_execute($stmt);
}

// Cancel appointment
function cancelAppointmentS($conn, $appointment_id, $patient_id) {
    $sql = "UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ? AND status = 'pending'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $patient_id);
    return mysqli_stmt_execute($stmt);
}

// Get consultation notes for patient
function getConsultationNotesS($conn, $patient_id) {
    $sql = "SELECT cn.*, u.name as doctor_name, a.appointment_date
            FROM consultation_notes cn
            INNER JOIN appointments a ON cn.appointment_id = a.id
            INNER JOIN doctors d ON cn.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id
            WHERE cn.patient_id = ?
            ORDER BY cn.created_at DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getPatientReviewsS($conn, $patient_id) {
    $sql = "SELECT dr.*, u.name as doctor_name, a.appointment_date
            FROM doctor_reviews dr
            INNER JOIN doctors d ON dr.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id
            INNER JOIN appointments a ON dr.appointment_id = a.id
            WHERE dr.patient_id = ?
            ORDER BY dr.created_at DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Update patient profile
function updatePatientProfileS($conn, $user_id, $name, $phone, $date_of_birth, $blood_group, $gender) {
    mysqli_begin_transaction($conn);

    $sql1 = "UPDATE users SET name = ?, phone = ? WHERE id = ? AND role = 'patient'";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "ssi", $name, $phone, $user_id);
    $ok1 = mysqli_stmt_execute($stmt1);

    $sql2 = "INSERT INTO patients (user_id, date_of_birth, blood_group, gender)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE date_of_birth = VALUES(date_of_birth), blood_group = VALUES(blood_group), gender = VALUES(gender)";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "isss", $user_id, $date_of_birth, $blood_group, $gender);
    $ok2 = mysqli_stmt_execute($stmt2);

    if ($ok1 && $ok2) {
        mysqli_commit($conn);
        return true;
    }

    mysqli_rollback($conn);
    return false;
}
?>