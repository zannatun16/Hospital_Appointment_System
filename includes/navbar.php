<?php


$baseUrl = '/Hospital_Appointment_System';

if (!isset($_SESSION['user_id'])) {
    
?>
<nav class="navbar">
    <div class="nav-brand">
        <h2>Hospital Appointment System</h2>
    </div>
    <div class="nav-links">
        <a href="<?php echo $baseUrl; ?>/views/home.php">Home</a>
        <a href="<?php echo $baseUrl; ?>/views/shared/login.php">Login</a>
        <a href="<?php echo $baseUrl; ?>/views/shared/register.php">Register</a>
    </div>
</nav>
<?php
} else {
    
    $role = $_SESSION['role'];
?>
<nav class="navbar">
    <div class="nav-brand">
        <h2>Hospital Appointment System</h2>
    </div>
    <div class="nav-links">
        <?php if ($role == 'patient') { ?>
            <a href="<?php echo $baseUrl; ?>/views/patient/patientDashboardS.php">Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/views/patient/browseDoctorsS.php">Browse Doctors</a>
            <a href="<?php echo $baseUrl; ?>/views/patient/myAppointmentsS.php">My Appointments</a>
            <a href="<?php echo $baseUrl; ?>/views/patient/profileS.php">Profile</a>
        <?php } elseif ($role == 'doctor') { ?>
            <a href="<?php echo $baseUrl; ?>/views/doctor/doctorDashboardM.php">Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/views/doctor/myAppointmentsM.php">Appointments</a>
            <a href="<?php echo $baseUrl; ?>/views/doctor/profileM.php">Profile</a>
        <?php } elseif ($role == 'admin') { ?>
            <a href="<?php echo $baseUrl; ?>/views/admin/adminDashboardZ.php">Dashboard</a>
            <a href="<?php echo $baseUrl; ?>/views/admin/manageDoctorsZ.php">Manage Doctors</a>
            <a href="<?php echo $baseUrl; ?>/views/admin/manageSpecializationsZ.php">Specializations</a>
            <a href="<?php echo $baseUrl; ?>/views/admin/allAppointmentsZ.php">All Appointments</a>
            <a href="<?php echo $baseUrl; ?>/views/admin/managePatientsZ.php">Manage Patients</a>
        <?php } ?>
        <a href="<?php echo $baseUrl; ?>/controllers/logoutController.php">Logout</a>
    </div>
    <div class="user-info">
        Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
    </div>
</nav>
<?php } ?>