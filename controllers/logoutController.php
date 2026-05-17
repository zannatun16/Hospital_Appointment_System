<?php
// Logout Controller
// Location: HospitalAppointmentSystem/controllers/logoutController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy all session data
session_destroy();

// Redirect to login page
header("Location: ../views/shared/login.php?logout=success");
exit();
?>