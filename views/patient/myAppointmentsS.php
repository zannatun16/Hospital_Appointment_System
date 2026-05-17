<?php
// My Appointments Page - Sadman
// Location: HospitalAppointmentSystem/views/patient/myAppointmentsS.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/patientModelS.php';

$patient_id = $_SESSION['user_id'];
$appointments = getPatientAppointmentsS($conn, $patient_id);
?>

<div>
    <h1>My Appointments</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Specialization</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($appointments) > 0): ?>
                    <?php foreach($appointments as $appointment): ?>
                        <tr>
                            <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['specialization']); ?></td>
                            <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars(substr($appointment['reason'], 0, 50)) . '...'; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                    <?php echo ucfirst($appointment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($appointment['status'] == 'pending'): ?>
                                    <form method="POST" action="../../controllers/patientControllerS.php" onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                        <input type="hidden" name="action" value="cancel_appointment">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                        <button type="submit" class="btn" style="background:#dc3545;">Cancel</button>
                                    </form>
                                <?php elseif ($appointment['status'] == 'completed'): ?>
                                    <?php if ($appointment['review_id']): ?>
                                        <button class="btn" style="background:#28a745;" onclick='viewReview(<?php echo json_encode("Dr. " . $appointment['doctor_name']); ?>, <?php echo json_encode($appointment['review_text']); ?>, <?php echo $appointment['rating'] ?: 0; ?>)'>View Review</button>
                                    <?php else: ?>
                                        <button onclick="openReviewForm(<?php echo $appointment['id']; ?>, '<?php echo addslashes($appointment['doctor_name']); ?>', <?php echo $appointment['doctor_id']; ?>)" class="btn">Review Doctor</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No appointments found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px; text-align:center;">
        <a href="browseDoctorsS.php" class="btn">Book New Appointment</a>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:500px; margin:50px auto; padding:30px; border-radius:10px;">
        <h2>Review Doctor</h2>
        <form method="POST" action="../../controllers/patientControllerS.php" onsubmit="return validateReviewForm()">
            <input type="hidden" name="action" value="review_doctor">
            <input type="hidden" id="review_appointment_id" name="appointment_id">
            <input type="hidden" id="review_doctor_id" name="doctor_id">

            <p id="reviewDoctorName" style="font-weight:600; margin-bottom:15px;"></p>

            <div class="form-group">
                <label>Rating</label>
                <select id="rating" name="rating" required>
                    <option value="">Select rating</option>
                    <option value="1">1 - Poor</option>
                    <option value="2">2 - Fair</option>
                    <option value="3">3 - Good</option>
                    <option value="4">4 - Very Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
            </div>
            <div class="form-group">
                <label>Review</label>
                <textarea id="review_text" name="review_text" rows="4" placeholder="Write at least 10 characters" required></textarea>
            </div>
            <div style="text-align:center;">
                <button type="submit">Submit Review</button>
                <button type="button" onclick="closeReviewForm()" style="background:#dc3545;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Review Detail Modal -->
<div id="reviewDetailModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:500px; margin:50px auto; padding:30px; border-radius:10px;">
        <h2>Review Details</h2>
        <p id="reviewDetailDoctor" style="font-weight:600; margin-bottom:10px;"></p>
        <p id="reviewDetailRating" style="margin-bottom:10px;"></p>
        <p id="reviewDetailText"></p>
        <div style="text-align:center; margin-top:20px;">
            <button type="button" onclick="closeReviewDetail()">Close</button>
        </div>
    </div>
</div>

<script>
function viewConsultationNote(appointmentId) {
    alert('Consultation notes feature coming soon');
}

function openReviewForm(appointmentId, doctorName, doctorId) {
    document.getElementById('review_appointment_id').value = appointmentId;
    document.getElementById('review_doctor_id').value = doctorId;
    document.getElementById('reviewDoctorName').innerText = 'Doctor: Dr. ' + doctorName;
    document.getElementById('rating').value = '';
    document.getElementById('review_text').value = '';
    document.getElementById('reviewModal').style.display = 'block';
}

function closeReviewForm() {
    document.getElementById('reviewModal').style.display = 'none';
}

function viewReview(doctorName, reviewText, rating) {
    document.getElementById('reviewDetailDoctor').innerText = 'Doctor: Dr. ' + doctorName;
    document.getElementById('reviewDetailRating').innerText = 'Rating: ' + rating + '/5';
    document.getElementById('reviewDetailText').innerText = reviewText;
    document.getElementById('reviewDetailModal').style.display = 'block';
}

function closeReviewDetail() {
    document.getElementById('reviewDetailModal').style.display = 'none';
}

function validateReviewForm() {
    const rating = document.getElementById('rating').value;
    const reviewText = document.getElementById('review_text').value.trim();

    if (!rating) {
        alert('Please select a rating');
        return false;
    }

    if (reviewText.length < 10) {
        alert('Review must be at least 10 characters');
        return false;
    }

    return true;
}
</script>

<?php require_once '../../includes/footer.php'; ?>