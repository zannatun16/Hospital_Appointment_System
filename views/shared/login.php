<?php
// Login Page
// Location: HospitalAppointmentSystem/views/shared/login.php

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>

<div class="form-container">
    <h2>Login to Your Account</h2>
    
    <?php if (isset($_GET['logout'])): ?>
        <div class="alert alert-success">Logged out successfully. Welcome back!</div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['errors'])): ?>
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    <form action="../../controllers/authController.php" method="POST" onsubmit="return validateLoginForm()">
        <input type="hidden" name="action" value="login">
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="your@email.com" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <div class="form-link">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
    
    <div class="demo-box">
        <h4>📋 Demo Accounts</h4>
        
        <p><strong>Admin:</strong> Zoita@gmail.com / admin123</p>
        <p><strong>Patient:</strong> Sadman@gmail.com / Patient123</p>
        <p><strong>Doctor:</strong> Mayeesha@gmail.com / Doctor123</p>
    
    </div>
</div>

<script>
function validateLoginForm() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        alert('Please fill in all fields');
        return false;
    }
    
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    return true;
}
</script>

<?php require_once '../../includes/footer.php'; ?>