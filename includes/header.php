<?php
// Header include file
// Location: HospitalAppointmentSystem/includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Appointment System</title>
    <link rel="stylesheet" href="/HospitalAppointmentSystem/assets/css/style.css">
</head>
<body>
    <div class="container">