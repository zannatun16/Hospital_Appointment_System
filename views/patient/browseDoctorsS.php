<?php
// Browse Doctors Page - Sadman
// Location: HospitalAppointmentSystem/views/patient/browseDoctorsS.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/patientModelS.php';

$doctors = getAllDoctorsS($conn);
?>

<div class="browse-doctors">
    <h1>Browse Doctors</h1>
    
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
    
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search by doctor name or specialization..." onkeyup="searchDoctors()">
    </div>
    
    <div id="doctorsList">
        <?php foreach ($doctors as $doctor): ?>
            <?php $availability = getDoctorAvailabilityS($conn, $doctor['doctor_id']); ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dr. <?php echo htmlspecialchars($doctor['name']); ?></h3>
                </div>
                <p><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['specialization']); ?></p>
                <p><strong>Experience:</strong> <?php echo $doctor['experience_years']; ?> years</p>
                <p><strong>Consultation Fee:</strong> $<?php echo $doctor['consultation_fee']; ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($doctor['phone']); ?></p>
                <div>
                    <strong>Availability:</strong>
                    <?php if (!empty($availability)): ?>
                        <ul style="margin: 8px 0 0 18px; padding:0; list-style-type: disc;">
                            <?php foreach ($availability as $slot): ?>
                                <li style="margin-bottom:4px;">
                                    <?php echo htmlspecialchars($slot['day_of_week']); ?>: <?php echo htmlspecialchars(date('h:i A', strtotime($slot['start_time']))); ?> - <?php echo htmlspecialchars(date('h:i A', strtotime($slot['end_time']))); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p style="margin:6px 0 0;">No availability set yet.</p>
                    <?php endif; ?>
                </div>
                <button onclick="showBookingForm(<?php echo $doctor['doctor_id']; ?>)" class="btn">Book Appointment</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Booking Form Modal -->
<div id="bookingModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:500px; margin:50px auto; padding:30px; border-radius:10px;">
        <h2>Book Appointment</h2>
        <form method="POST" action="../../controllers/patientControllerS.php" onsubmit="return validateBookingForm()">
            <input type="hidden" name="action" value="book_appointment">
            <input type="hidden" id="doctor_id" name="doctor_id">
            
            <div class="form-group">
                <label>Appointment Date:</label>
                <input type="date" id="appointment_date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label>Appointment Time:</label>
                <input type="time" id="appointment_time" name="appointment_time" required>
            </div>
            
            <div class="form-group">
                <label>Reason for Appointment:</label>
                <textarea id="reason" name="reason" rows="4" required></textarea>
            </div>
            
            <div style="text-align:center;">
                <button type="submit">Book Appointment</button>
                <button type="button" onclick="closeBookingForm()" style="background:#dc3545;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showBookingForm(doctorId) {
    document.getElementById('doctor_id').value = doctorId;
    document.getElementById('bookingModal').style.display = 'block';
}

function closeBookingForm() {
    document.getElementById('bookingModal').style.display = 'none';
}

function validateBookingForm() {
    const date = document.getElementById('appointment_date').value;
    const time = document.getElementById('appointment_time').value;
    const reason = document.getElementById('reason').value;
    
    if (!date || !time || !reason) {
        alert('Please fill in all fields');
        return false;
    }
    
    if (reason.length < 10) {
        alert('Please provide a detailed reason (minimum 10 characters)');
        return false;
    }
    
    return true;
}

function searchDoctors() {
    const searchTerm = document.getElementById('searchInput').value;
    
    if (searchTerm.length >= 2) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../../ajax/patientAjaxS.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                document.getElementById('doctorsList').innerHTML = response.html;
            }
        };
        
        xhr.send('action=search_doctors&search_term=' + encodeURIComponent(searchTerm));
    } else if (searchTerm.length === 0) {
        location.reload();
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>