<?php


session_start();
require_once '../config/database.php';
require_once '../models/doctorModelM.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../views/shared/login.php");
    exit();
}

$doctor_info = getDoctorInfoZ($conn, $_SESSION['user_id']);
$doctor_id = $doctor_info['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'update_appointment_status':
                $appointment_id = $_POST['appointment_id'];
                $status = $_POST['status'];
                
                if (updateAppointmentStatusZ($conn, $appointment_id, $status)) {
                    $_SESSION['success'] = "Appointment status updated successfully";
                } else {
                    $_SESSION['error'] = "Failed to update appointment status";
                }
                
                header("Location: ../views/doctor/myAppointmentsM.php");
                break;
                
            case 'add_consultation_note':
                $appointment_id = $_POST['appointment_id'];
                $patient_id = $_POST['patient_id'];
                $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
                $prescription = mysqli_real_escape_string($conn, $_POST['prescription']);
                
                $errors = [];
                if (empty($diagnosis)) {
                    $errors[] = "Diagnosis is required";
                }
                if (empty($prescription)) {
                    $errors[] = "Prescription is required";
                }
                
                if (empty($errors)) {
                    if (addConsultationNoteZ($conn, $appointment_id, $doctor_id, $patient_id, $diagnosis, $prescription)) {
                        
                        updateAppointmentStatusZ($conn, $appointment_id, 'completed');
                        $_SESSION['success'] = "Consultation note added successfully";
                    } else {
                        $_SESSION['error'] = "Failed to add consultation note";
                    }
                } else {
                    $_SESSION['errors'] = $errors;
                }
                
                header("Location: ../views/doctor/myAppointmentsM.php");
                break;
                
            case 'update_profile':
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $consultation_fee = $_POST['consultation_fee'];
                
                $errors = [];
                if (empty($name)) {
                    $errors[] = "Name is required";
                } elseif (!preg_match('/^[A-Za-z ]+$/', $name)) {
                    $errors[] = "Name must contain only letters and spaces";
                }
                if (empty($phone)) {
                    $errors[] = "Phone is required";
                } elseif (!preg_match("/^[0-9]{10,11}$/", $phone)) {
                    $errors[] = "Invalid phone number";
                }
                if (empty($consultation_fee) || $consultation_fee <= 0) {
                    $errors[] = "Valid consultation fee is required";
                }
                
                if (empty($errors)) {
                    if (updateDoctorProfileZ($conn, $_SESSION['user_id'], $name, $phone, $consultation_fee)) {
                        $_SESSION['user_name'] = $name;
                        $_SESSION['success'] = "Profile updated successfully";
                    } else {
                        $_SESSION['error'] = "Failed to update profile";
                    }
                } else {
                    $_SESSION['errors'] = $errors;
                }
                
                header("Location: ../views/doctor/profileM.php");
                break;
        }
    }
}
?>