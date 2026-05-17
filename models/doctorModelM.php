<?php
// Doctor Model - Zoita
// Location: HospitalAppointmentSystem/models/doctorModelZ.php

require_once dirname(__DIR__) . '/config/database.php';

// Get doctor info
function getDoctorInfoZ($conn, $user_id) {
    $sql = "SELECT d.*, u.name, u.email, u.phone, s.name as specialization
            FROM doctors d
            INNER JOIN users u ON d.user_id = u.id
            INNER JOIN specializations s ON d.specialization_id = s.id
            WHERE u.id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Get doctor appointments
function getDoctorAppointmentsZ($conn, $doctor_id) {
    $sql = "SELECT a.*, u.name as patient_name, u.email as patient_email, u.phone as patient_phone
            FROM appointments a
            INNER JOIN users u ON a.patient_id = u.id
            WHERE a.doctor_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Update appointment status
function updateAppointmentStatusZ($conn, $appointment_id, $status) {
    $sql = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $appointment_id);
    return mysqli_stmt_execute($stmt);
}

// Add consultation note
function addConsultationNoteZ($conn, $appointment_id, $doctor_id, $patient_id, $diagnosis, $prescription) {
    $sql = "INSERT INTO consultation_notes (appointment_id, doctor_id, patient_id, diagnosis, prescription) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiiss", $appointment_id, $doctor_id, $patient_id, $diagnosis, $prescription);
    return mysqli_stmt_execute($stmt);
}

// Get consultation note for a specific appointment
function getConsultationNoteByAppointmentZ($conn, $appointment_id, $doctor_id) {
    $sql = "SELECT * FROM consultation_notes WHERE appointment_id = ? AND doctor_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $doctor_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Get patient appointment history for a specific patient
function getPatientHistoryZ($conn, $patient_id, $doctor_id) {
    $sql = "SELECT a.*, u.name as patient_name
            FROM appointments a
            INNER JOIN users u ON a.patient_id = u.id
            WHERE a.patient_id = ? AND a.doctor_id = ?
            ORDER BY a.appointment_date DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $patient_id, $doctor_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Update doctor profile
function updateDoctorProfileZ($conn, $user_id, $name, $phone, $consultation_fee) {
    // Update user table
    $sql1 = "UPDATE users SET name = ?, phone = ? WHERE id = ?";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "ssi", $name, $phone, $user_id);
    
    // Update doctors table
    $sql2 = "UPDATE doctors SET consultation_fee = ? WHERE user_id = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "di", $consultation_fee, $user_id);
    
    return mysqli_stmt_execute($stmt1) && mysqli_stmt_execute($stmt2);
}
?>