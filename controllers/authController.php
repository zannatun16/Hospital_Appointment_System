<?php

require_once '../config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'login') {
            
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = $_POST['password'];
            
            
            $errors = [];
            if (empty($email)) {
                $errors[] = "Email is required";
            }
            if (empty($password)) {
                $errors[] = "Password is required";
            }
            
            if (empty($errors)) {
                
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);
                
                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    
                    if ($user['role'] == 'patient') {
                        header("Location: ../views/patient/patientDashboardS.php");
                    } elseif ($user['role'] == 'doctor') {
                        header("Location: ../views/doctor/doctorDashboardM.php");
                    } elseif ($user['role'] == 'admin') {
                        header("Location: ../views/admin/adminDashboardZ.php");
                    }
                    exit();
                } else {
                    $_SESSION['error'] = "Invalid email or password";
                    header("Location: ../views/shared/login.php");
                    exit();
                }
            } else {
                $_SESSION['errors'] = $errors;
                header("Location: ../views/shared/login.php");
                exit();
            }
        } elseif ($action == 'register') {
            
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
            $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
            $gender = mysqli_real_escape_string($conn, $_POST['gender']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            
            $errors = [];
            if (empty($name)) {
                $errors[] = "Name is required";
            }
            if (empty($email)) {
                $errors[] = "Email is required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
            if (empty($phone)) {
                $errors[] = "Phone is required";
            } elseif (!preg_match("/^[0-9]{10,11}$/", $phone)) {
                $errors[] = "Phone must be 10-11 digits";
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
            if (empty($password)) {
                $errors[] = "Password is required";
            } elseif (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }
            if ($password != $confirm_password) {
                $errors[] = "Passwords do not match";
            }
            
            if (empty($errors)) {
                
                $sql = "SELECT id FROM users WHERE email = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $errors[] = "Email already exists";
                    $_SESSION['errors'] = $errors;
                    header("Location: ../views/shared/register.php");
                    exit();
                }
                
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                
                $sql = "INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'patient')";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $hashed_password);
                
                if (mysqli_stmt_execute($stmt)) {
                    $user_id = mysqli_insert_id($conn);

                
                    $sql_patient = "INSERT INTO patients (user_id, date_of_birth, blood_group, gender) VALUES (?, ?, ?, ?)";
                    $stmt_patient = mysqli_prepare($conn, $sql_patient);
                    mysqli_stmt_bind_param($stmt_patient, "isss", $user_id, $date_of_birth, $blood_group, $gender);

                    if (mysqli_stmt_execute($stmt_patient)) {
                        $_SESSION['success'] = "Registration successful! Please login.";
                        header("Location: ../views/shared/login.php");
                        exit();
                    } else {
                        mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
                        $errors[] = "Registration failed while saving patient profile";
                        $_SESSION['errors'] = $errors;
                        header("Location: ../views/shared/register.php");
                        exit();
                    }
                } else {
                    $errors[] = "Registration failed";
                    $_SESSION['errors'] = $errors;
                    header("Location: ../views/shared/register.php");
                    exit();
                }
            } else {
                $_SESSION['errors'] = $errors;
                header("Location: ../views/shared/register.php");
                exit();
            }
        }
    }
}
?>