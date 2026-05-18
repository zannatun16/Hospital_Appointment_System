<?php


session_start();
require_once '../config/database.php';
require_once '../models/adminModelZ.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../views/shared/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'add_doctor':
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $password = $_POST['password'];
                $specialization_id = $_POST['specialization_id'];
                $consultation_fee = $_POST['consultation_fee'];
                $experience_years = $_POST['experience_years'];
                
                $errors = [];
                if (empty($name)) $errors[] = "Name is required";
                if (!preg_match('/^[A-Za-z ]+$/', $name)) $errors[] = "Name must contain only letters and spaces";
                if (empty($email)) $errors[] = "Email is required";
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
                if (empty($phone)) $errors[] = "Phone is required";
                if (empty($password) || strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
                if (empty($specialization_id)) $errors[] = "Specialization is required";
                if (empty($consultation_fee) || $consultation_fee <= 400) $errors[] = "Consultation fee must be more than $400";
                if (empty($experience_years) || $experience_years <= 2) $errors[] = "Experience must be more than 2 years";
                
                if (empty($errors)) {
                    if (addDoctorM($conn, $name, $email, $phone, $password, $specialization_id, $consultation_fee, $experience_years)) {
                        $_SESSION['success'] = "Doctor added successfully";
                    } else {
                        $_SESSION['error'] = "Failed to add doctor. Email might already exist.";
                    }
                } else {
                    $_SESSION['errors'] = $errors;
                }
                
                header("Location: ../views/admin/manageDoctorsZ.php");
                break;
                
            case 'update_doctor':
                $user_id = $_POST['user_id'];
                $doctor_id = $_POST['doctor_id'];
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $specialization_id = $_POST['specialization_id'];
                $consultation_fee = $_POST['consultation_fee'];
                $experience_years = $_POST['experience_years'];
                
                $errors = [];
                if (empty($name)) $errors[] = "Name is required";
                if (!preg_match('/^[A-Za-z ]+$/', $name)) $errors[] = "Name must contain only letters and spaces";
                if (empty($phone)) $errors[] = "Phone is required";
                if (empty($specialization_id)) $errors[] = "Specialization is required";
                if (empty($consultation_fee) || $consultation_fee <= 400) $errors[] = "Consultation fee must be more than $400";
                if (empty($experience_years) || $experience_years <= 2) $errors[] = "Experience must be more than 2 years";
                
                if (empty($errors)) {
                    if (updateDoctorM($conn, $user_id, $doctor_id, $name, $phone, $specialization_id, $consultation_fee, $experience_years)) {
                        $_SESSION['success'] = "Doctor updated successfully";
                    } else {
                        $_SESSION['error'] = "Failed to update doctor";
                    }
                } else {
                    $_SESSION['errors'] = $errors;
                }
                
                header("Location: ../views/admin/manageDoctorsZ.php");
                break;
                
            case 'delete_doctor':
                $user_id = $_POST['user_id'];
                
                if (deleteDoctorM($conn, $user_id)) {
                    $_SESSION['success'] = "Doctor deleted successfully";
                } else {
                    $_SESSION['error'] = "Failed to delete doctor";
                }
                
                header("Location: ../views/admin/manageDoctorsZ.php");
                break;
                
            case 'add_specialization':
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                
                if (empty($name)) {
                    $_SESSION['error'] = "Specialization name is required";
                } else {
                    if (addSpecializationM($conn, $name)) {
                        $_SESSION['success'] = "Specialization added successfully";
                    } else {
                        $_SESSION['error'] = "Failed to add specialization";
                    }
                }
                
                header("Location: ../views/admin/manageSpecializationsZ.php");
                break;
                
            case 'update_specialization':
                $id = $_POST['id'];
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                
                if (empty($name)) {
                    $_SESSION['error'] = "Specialization name is required";
                } else {
                    if (updateSpecializationM($conn, $id, $name)) {
                        $_SESSION['success'] = "Specialization updated successfully";
                    } else {
                        $_SESSION['error'] = "Failed to update specialization";
                    }
                }
                
                header("Location: ../views/admin/manageSpecializationsZ.php");
                break;
                
            case 'delete_specialization':
                $id = $_POST['id'];
                
                if (deleteSpecializationM($conn, $id)) {
                    $_SESSION['success'] = "Specialization deleted successfully";
                } else {
                    $_SESSION['error'] = "Failed to delete specialization";
                }
                
                header("Location: ../views/admin/manageSpecializationsZ.php");
                break;
                
            case 'delete_patient':
                $user_id = $_POST['user_id'];
                
                if (deletePatientM($conn, $user_id)) {
                    $_SESSION['success'] = "Patient deleted successfully";
                } else {
                    $_SESSION['error'] = "Failed to delete patient";
                }
                
                header("Location: ../views/admin/managePatientsZ.php");
                break;
        }
    }
}
?>