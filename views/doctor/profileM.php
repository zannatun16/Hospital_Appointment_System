<?php
// Doctor Profile Page - Zoita
// Location: HospitalAppointmentSystem/views/doctor/profileZ.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/doctorModelM.php';

$doctor_info = getDoctorInfoZ($conn, $_SESSION['user_id']);
?>

<div class="form-container">
    <h2>My Profile</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['errors'])): ?>
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    <form method="POST" action="../../controllers/doctorControllerM.php" onsubmit="return validateProfileForm()">
        <input type="hidden" name="action" value="update_profile">
        
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" disabled>
            <small style="color:#666;">Email cannot be changed</small>
        </div>
        
        <div class="form-group">
            <label>Phone Number:</label>
            <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($doctor_info['phone']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Specialization:</label>
            <input type="text" value="<?php echo $doctor_info['specialization']; ?>" disabled>
        </div>
        
        <div class="form-group">
            <label>Experience (Years):</label>
            <input type="text" value="<?php echo $doctor_info['experience_years']; ?>" disabled>
        </div>
        
        <div class="form-group">
            <label>Consultation Fee ($):</label>
            <input type="number" name="consultation_fee" id="consultation_fee" value="<?php echo $doctor_info['consultation_fee']; ?>" step="0.01" required>
        </div>
        
        <div style="text-align:center;">
            <button type="submit">Update Profile</button>
        </div>
    </form>
</div>

<script>
function validateProfileForm() {
    const name = document.getElementById('name').value;
    const phone = document.getElementById('phone').value;
    const fee = document.getElementById('consultation_fee').value;
    
    if (name.length < 3) {
        alert('Name must be at least 3 characters');
        return false;
    }

    if (!/^[A-Za-z ]+$/.test(name)) {
        alert('Name must contain only letters and spaces');
        return false;
    }
    
    if (!/^\d{10,11}$/.test(phone)) {
        alert('Phone number must be 10-11 digits');
        return false;
    }
    
    if (fee <= 0) {
        alert('Consultation fee must be greater than 0');
        return false;
    }
    
    return true;
}
</script>

<?php require_once '../../includes/footer.php'; ?>