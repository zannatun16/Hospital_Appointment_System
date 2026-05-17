<?php

session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: views/admin/adminDashboardZ.php');
        exit();
    } elseif ($_SESSION['role'] == 'doctor') {
        header('Location: views/doctor/doctorDashboardM.php');
        exit();
    } elseif ($_SESSION['role'] == 'patient') {
        header('Location: views/patient/patientDashboardS.php');
        exit();
    }
}

header('Location: views/shared/login.php');
exit();