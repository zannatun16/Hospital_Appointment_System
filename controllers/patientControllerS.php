<?php
// Patient Controller - Sadman
// Location: HospitalAppointmentSystem/controllers/patientControllerS.php

session_start();
require_once '../config/database.php';
require_once '../models/patientModelS.php';

// Check if user is logged in and is patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    header("Location: ../views/shared/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'book_appointment':
                // Validate inputs
                $errors = [];
                
                if (empty($_POST['doctor_id'])) {
                    $errors[] = "Please select a doctor";
                }
                if (empty($_POST['appointment_date'])) {
                    $errors[] = "Please select a date";
                }
                if (empty($_POST['appointment_time'])) {
                    $errors[] = "Please select a time";
                }
                if (empty($_POST['reason'])) {
                    $errors[] = "Please provide a reason for appointment";
                }
                
                if (empty($errors)) {
                    $patient_id = intval($_SESSION['user_id']);
                    $doctor_id = intval($_POST['doctor_id']);
                    $date = $_POST['appointment_date'];
                    $time = $_POST['appointment_time'];
                    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

                    // Validate patient and doctor existence
                    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE id = ? AND role = 'patient'");
                    mysqli_stmt_bind_param($stmt, "i", $patient_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) === 0) {
                        $errors[] = "Invalid patient session. Please log in again.";
                    }
                    mysqli_stmt_close($stmt);

                    $stmt = mysqli_prepare($conn, "SELECT id FROM doctors WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) === 0) {
                        $errors[] = "Selected doctor not found.";
                    }
                    mysqli_stmt_close($stmt);
                }

                if (empty($errors)) {
                    if (bookAppointmentS($conn, $patient_id, $doctor_id, $date, $time, $reason)) {
                        $_SESSION['success'] = "Appointment booked successfully!";
                    } else {
                        $_SESSION['error'] = "Failed to book appointment";
                    }
                } else {
                    $_SESSION['errors'] = $errors;
                }
                
                header("Location: ../views/patient/browseDoctorsS.php");
                break;
                
            case 'cancel_appointment':
                $appointment_id = $_POST['appointment_id'];
                $patient_id = $_SESSION['user_id'];
                
                if (cancelAppointmentS($conn, $appointment_id, $patient_id)) {
                    $_SESSION['success'] = "Appointment cancelled successfully";
                } else {
                    $_SESSION['error'] = "Failed to cancel appointment";
                }
                
                header("Location: ../views/patient/myAppointmentsS.php");
                break;

            case 'review_doctor':
                $appointment_id = intval($_POST['appointment_id']);
                $doctor_id = intval($_POST['doctor_id']);
                $rating = intval($_POST['rating']);
                $review_text = trim($_POST['review_text']);
                $patient_id = $_SESSION['user_id'];

                $errors = [];
                if ($rating < 1 || $rating > 5) {
                    $errors[] = "Rating must be between 1 and 5";
                }
                if (empty($review_text) || strlen($review_text) < 10) {
                    $errors[] = "Review must be at least 10 characters";
                }

                // Verify this appointment belongs to the patient and is completed
                $sql = "SELECT doctor_id FROM appointments WHERE id = ? AND patient_id = ? AND status = 'completed'";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $patient_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $appointment = mysqli_fetch_assoc($result);

                if (!$appointment || $appointment['doctor_id'] != $doctor_id) {
                    $errors[] = "Invalid appointment or doctor";
                }

                if (empty($errors)) {
                    if (addDoctorReviewS($conn, $appointment_id, $patient_id, $doctor_id, $rating, $review_text)) {
                        $_SESSION['success'] = "Review submitted successfully";
                    } else {
                        $_SESSION['error'] = "Failed to submit review";
                    }
                } else {
                    $_SESSION['errors'] = $errors;
                }

                header("Location: ../views/patient/myAppointmentsS.php");
                break;
                
            case 'update_profile':
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
                $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
                $gender = mysqli_real_escape_string($conn, $_POST['gender']);
                
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
                if (empty($date_of_birth)) {
                    $errors[] = "Date of birth is required";
                }
                if (empty($blood_group)) {
                    $errors[] = "Blood group is required";
                } elseif (!in_array($blood_group, ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])) {
                    $errors[] = "Invalid blood group";
                }
                if (empty($gender)) {
                    $errors[] = "Gender is required";
                } elseif (!in_array($gender, ['Male', 'Female', 'Other'])) {
                    $errors[] = "Invalid gender";
                }
                
                if (empty($errors)) {
                    if (updatePatientProfileS($conn, $_SESSION['user_id'], $name, $phone, $date_of_birth, $blood_group, $gender)) {
                        $_SESSION['user_name'] = $name;
                        $_SESSION['success'] = "Profile updated successfully";
                    } else {
                        $_SESSION['error'] = "Failed to update profile";
                    }
                } else {
                    $_SESSION['errors'] = $errors;
                }
                
                header("Location: ../views/patient/profileS.php");
                break;
        }
    }
}
?>