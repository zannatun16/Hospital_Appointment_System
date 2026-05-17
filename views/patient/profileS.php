<?php
// Patient Profile Page - Sadman
// Location: HospitalAppointmentSystem/views/patient/profileS.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
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
    
    <form method="POST" action="../../controllers/patientControllerS.php" onsubmit="return validateProfileForm()">
        <input type="hidden" name="action" value="update_profile">
        
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" id="name" placeholder="Your full name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" disabled>
            <small style="color:#666;">Email cannot be changed</small>
        </div>
        
        <?php
            $sql = "SELECT u.phone, p.date_of_birth, p.blood_group, p.gender
                    FROM users u
                    LEFT JOIN patients p ON u.id = p.user_id
                    WHERE u.id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
        ?>

        <div class="form-group">
            <label>Phone Number:</label>
            <input type="tel" name="phone" id="phone" placeholder="01700000000" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>

        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" placeholder="mm/dd/yyyy" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
        </div>

        <div class="form-group">
            <label>Blood Group</label>
            <select name="blood_group" id="blood_group" required>
                <option value="">Select your blood group</option>
                <?php
                $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                foreach ($blood_groups as $bg) {
                    $selected = ($user['blood_group'] == $bg) ? 'selected' : '';
                    echo "<option value=\"$bg\" $selected>$bg</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Gender</label>
            <select name="gender" id="gender" required>
                <option value="">Select your gender</option>
                <?php
                $genders = ['Male', 'Female', 'Other'];
                foreach ($genders as $genderOption) {
                    $selected = ($user['gender'] == $genderOption) ? 'selected' : '';
                    echo "<option value=\"$genderOption\" $selected>$genderOption</option>";
                }
                ?>
            </select>
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
    const date_of_birth = document.getElementById('date_of_birth').value;
    const blood_group = document.getElementById('blood_group').value;
    const gender = document.getElementById('gender').value;
    
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

    if (!date_of_birth) {
        alert('Date of birth is required');
        return false;
    }

    if (!blood_group) {
        alert('Blood group is required');
        return false;
    }

    if (!gender) {
        alert('Gender is required');
        return false;
    }
    
    return true;
}
</script>

<?php require_once '../../includes/footer.php'; ?>