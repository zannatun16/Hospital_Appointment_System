<?php

// Location: HospitalAppointmentSystem/views/admin/manageDoctorsZ.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/adminModelZ.php';

$doctors = getAllDoctorsM($conn);
$specializations = getAllSpecializationsM($conn);
?>

<div>
    <h1>Manage Doctors</h1>
    
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
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Add New Doctor</h3>
        </div>
        <form method="POST" action="../../controllers/adminControllerZ.php" onsubmit="return validateDoctorForm()">
            <input type="hidden" name="action" value="add_doctor">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Specialization:</label>
                    <select id="specialization_id" name="specialization_id" required>
                        <option value="">Select Specialization</option>
                        <?php foreach($specializations as $spec): ?>
                            <option value="<?php echo $spec['id']; ?>"><?php echo htmlspecialchars($spec['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Consultation Fee ($):</label>
                    <input type="number" id="consultation_fee" name="consultation_fee" step="0.01" min="400.01" required>
                </div>
                
                <div class="form-group">
                    <label>Experience (Years):</label>
                    <input type="number" id="experience_years" name="experience_years" step="1" min="3" required>
                </div>
            </div>
            
            <div style="text-align:center;">
                <button type="submit">Add Doctor</button>
            </div>
        </form>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Doctors</h3>
        </div>
        
        <div class="search-box">
            <input type="text" id="searchDoctors" placeholder="Search doctors by name, specialization, or email...">
        </div>
        
        <div id="doctorsList">
            <div class="table-container">
                <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#667eea; color:white;">
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Specialization</th>
                            <th>Fee</th>
                            <th>Experience</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($doctors as $doctor): ?>
                            <tr id="doctor-row-<?php echo $doctor['user_id']; ?>">
                                <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['phone']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                                <td>$<?php echo $doctor['consultation_fee']; ?></td>
                                <td><?php echo $doctor['experience_years']; ?> years</td>
                                <td>
                                    <button
                                        onclick="editDoctor(this)"
                                        class="btn btn-sm"
                                        data-user-id="<?php echo $doctor['user_id']; ?>"
                                        data-doctor-id="<?php echo $doctor['doctor_id']; ?>"
                                        data-name="<?php echo htmlspecialchars($doctor['name'], ENT_QUOTES); ?>"
                                        data-phone="<?php echo htmlspecialchars($doctor['phone'], ENT_QUOTES); ?>"
                                        data-specialization-id="<?php echo $doctor['specialization_id']; ?>"
                                        data-consultation-fee="<?php echo $doctor['consultation_fee']; ?>"
                                        data-experience-years="<?php echo $doctor['experience_years']; ?>"
                                    >Edit</button>
                                    <form method="POST" action="../../controllers/adminControllerZ.php" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                        <input type="hidden" name="action" value="delete_doctor">
                                        <input type="hidden" name="user_id" value="<?php echo $doctor['user_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Doctor Modal -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:600px; margin:50px auto; padding:30px; border-radius:10px;">
        <h2>Edit Doctor</h2>
        <form method="POST" action="../../controllers/adminControllerZ.php" id="editForm" onsubmit="return validateEditDoctorForm()">
            <input type="hidden" name="action" value="update_doctor">
            <input type="hidden" id="edit_user_id" name="user_id">
            <input type="hidden" id="edit_doctor_id" name="doctor_id">
            
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Phone:</label>
                <input type="tel" id="edit_phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label>Specialization:</label>
                <select id="edit_specialization_id" name="specialization_id" required>
                    <?php foreach($specializations as $spec): ?>
                        <option value="<?php echo $spec['id']; ?>"><?php echo htmlspecialchars($spec['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Consultation Fee ($):</label>
                <input type="number" id="edit_consultation_fee" name="consultation_fee" step="0.01" min="400.01" required>
            </div>
            
            <div class="form-group">
                <label>Experience (Years):</label>
                <input type="number" id="edit_experience_years" name="experience_years" step="1" min="3" required>
            </div>
            
            <div style="text-align:center;">
                <button type="submit">Update Doctor</button>
                <button type="button" onclick="closeEditModal()" style="background:#dc3545;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function validateDoctorForm() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const password = document.getElementById('password').value;
    const fee = document.getElementById('consultation_fee').value;
    const experience = document.getElementById('experience_years').value;
    
    if (name.length < 3) {
        alert('Name must be at least 3 characters');
        return false;
    }

    if (!/^[A-Za-z ]+$/.test(name)) {
        alert('Name must contain only letters and spaces');
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
    
    if (fee <= 400) {
        alert('Consultation fee must be more than $400');
        return false;
    }
    
    if (experience <= 2) {
        alert('Experience must be more than 2 years');
        return false;
    }
    
    return true;
}

function validateEditDoctorForm() {
    const name = document.getElementById('edit_name').value;
    const phone = document.getElementById('edit_phone').value;
    const fee = document.getElementById('edit_consultation_fee').value;
    const experience = document.getElementById('edit_experience_years').value;

    if (name.length < 3) {
        alert('Name must be at least 3 characters');
        return false;
    }

    if (!/^[A-Za-z ]+$/.test(name)) {
        alert('Name must contain only letters and spaces');
        return false;
    }

    if (!/^[0-9]{10,11}$/.test(phone)) {
        alert('Phone number must be 10-11 digits');
        return false;
    }

    if (fee <= 400) {
        alert('Consultation fee must be more than $400');
        return false;
    }

    if (experience <= 2) {
        alert('Experience must be more than 2 years');
        return false;
    }

    return true;
}

function editDoctor(button) {
    const userId = button.dataset.userId;
    const doctorId = button.dataset.doctorId;
    const name = button.dataset.name;
    const phone = button.dataset.phone;
    const specializationId = button.dataset.specializationId;
    const fee = button.dataset.consultationFee;
    const experienceYears = button.dataset.experienceYears;

    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_doctor_id').value = doctorId;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_specialization_id').value = specializationId;
    document.getElementById('edit_consultation_fee').value = fee;
    document.getElementById('edit_experience_years').value = experienceYears;

    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// AJAX search for doctors
const searchInput = document.getElementById('searchDoctors');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value;
        
        if (searchTerm.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../ajax/adminAjaxZ.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    document.getElementById('doctorsList').innerHTML = response.html;
                }
            };
            
            xhr.send('action=search_doctors&search_term=' + encodeURIComponent(searchTerm));
        } else {
            location.reload();
        }
    });
}
</script>

<?php require_once '../../includes/footer.php'; ?>