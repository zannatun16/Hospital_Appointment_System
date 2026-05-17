<?php
// Registration Page
// Location: HospitalAppointmentSystem/views/shared/register.php

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>

<div class="form-container">
    <h2>Create Your Account</h2>
    
    <?php if (isset($_SESSION['errors'])): ?>
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    <form action="../../controllers/authController.php" method="POST" onsubmit="return validateRegisterForm()">
        <input type="hidden" name="action" value="register">
        
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Your full name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="your@email.com" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="01700000000" required>
        </div>

        <div class="form-group">
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required>
        </div>

        <div class="form-group">
            <label for="blood_group">Blood Group</label>
            <select id="blood_group" name="blood_group" required>
                <option value="">Select your blood group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Select your gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="password">Password (minimum 6 characters)</label>
            <input type="password" id="password" name="password" placeholder="Enter a strong password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
        </div>
        
        <button type="submit">Register</button>
    </form>
    
    <div class="form-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<script>
function validateRegisterForm() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const password = document.getElementById('password').value;
    const confirm_password = document.getElementById('confirm_password').value;
    
    const date_of_birth = document.getElementById('date_of_birth').value;
    const blood_group = document.getElementById('blood_group').value;
    const gender = document.getElementById('gender').value;

    if (!name || !email || !phone || !date_of_birth || !blood_group || !gender || !password || !confirm_password) {
        alert('Please fill in all fields');
        return false;
    }
    
    if (name.length < 3) {
        alert('Name must be at least 3 characters');
        return false;
    }
    
    if (!email.includes('@') || !email.includes('.')) {
        alert('Please enter a valid email address');
        return false;
    }
    
    if (!/^\d{10,11}$/.test(phone)) {
        alert('Phone number must be 10-11 digits');
        return false;
    }
    
    if (password.length < 6) {
        alert('Password must be at least 6 characters');
        return false;
    }
    
    if (password !== confirm_password) {
        alert('Passwords do not match');
        return false;
    }
    
    return true;
}
</script>

<?php require_once '../../includes/footer.php'; ?>