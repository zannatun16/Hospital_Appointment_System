<?php
// Patient Dashboard - Sadman
// Location: HospitalAppointmentSystem/views/patient/patientDashboardS.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

// Check if user is logged in and is patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/patientModelS.php';

$patient_id = $_SESSION['user_id'];
$appointments = getPatientAppointmentsS($conn, $patient_id);
$consultation_notes = getConsultationNotesS($conn, $patient_id);

$total_appointments = count($appointments);
$pending_appointments = 0;
$completed_appointments = 0;

foreach ($appointments as $appointment) {
    if ($appointment['status'] == 'pending') $pending_appointments++;
    if ($appointment['status'] == 'completed') $completed_appointments++;
}
?>

<div class="dashboard-container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_appointments; ?></div>
            <div>Total Appointments</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?php echo $pending_appointments; ?></div>
            <div>Pending Appointments</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?php echo $completed_appointments; ?></div>
            <div>Completed Appointments</div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Appointments</h3>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($appointments) > 0): ?>
                        <?php foreach(array_slice($appointments, 0, 5) as $appointment): ?>
                            <tr>
                                <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['specialization']); ?></td>
                                <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($appointment['status'] == 'pending'): ?>
                                        <form method="POST" action="../../controllers/patientControllerS.php" style="display:inline;">
                                            <input type="hidden" name="action" value="cancel_appointment">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <button type="submit" onclick="return confirm('Are you sure?')" class="btn" style="background:#dc3545;">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No appointments found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($appointments) > 5): ?>
            <div style="margin-top: 15px; text-align:center;">
                <a href="myAppointmentsS.php" class="btn">View All Appointments</a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Consultation Notes</h3>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Diagnosis</th>
                        <th>Prescription</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($consultation_notes) > 0): ?>
                        <?php foreach(array_slice($consultation_notes, 0, 5) as $note): ?>
                            <tr>
                                <td>Dr. <?php echo htmlspecialchars($note['doctor_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($note['appointment_date'])); ?></td>
                                <td><?php echo htmlspecialchars(substr($note['diagnosis'], 0, 50)) . '...'; ?></td>
                                <td><?php echo htmlspecialchars(substr($note['prescription'], 0, 50)) . '...'; ?></td>
                                <td>
                                    <button class="btn" onclick='viewConsultationNote(<?php echo json_encode($note['doctor_name']); ?>, <?php echo json_encode($note['diagnosis']); ?>, <?php echo json_encode($note['prescription']); ?>, <?php echo json_encode(date('d M Y', strtotime($note['appointment_date']))); ?>)'>View</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">No consultation notes found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="browseDoctorsS.php" class="btn">Book New Appointment</a>
    </div>
</div>

<!-- Consultation Note Detail Modal -->
<div id="consultationNoteModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:520px; max-width:90%; margin:60px auto; padding:25px; border-radius:10px; position:relative;">
        <h2>Consultation Details</h2>
        <p id="consultationNoteDoctor" style="font-weight:600; margin-top:10px;"></p>
        <p id="consultationNoteDate" style="margin-bottom:20px;"></p>
        <div style="margin-bottom:15px;">
            <strong>Diagnosis</strong>
            <p id="consultationNoteDiagnosis" style="white-space:pre-wrap; margin-top:8px;"></p>
        </div>
        <div>
            <strong>Prescription</strong>
            <p id="consultationNotePrescription" style="white-space:pre-wrap; margin-top:8px;"></p>
        </div>
        <div style="text-align:right; margin-top:20px;">
            <button type="button" onclick="closeConsultationNoteModal()" class="btn">Close</button>
        </div>
    </div>
</div>

<script>
function viewConsultationNote(doctorName, diagnosis, prescription, date) {
    document.getElementById('consultationNoteDoctor').innerText = 'Doctor: Dr. ' + doctorName;
    document.getElementById('consultationNoteDate').innerText = 'Date: ' + date;
    document.getElementById('consultationNoteDiagnosis').innerText = diagnosis;
    document.getElementById('consultationNotePrescription').innerText = prescription;
    document.getElementById('consultationNoteModal').style.display = 'block';
}

function closeConsultationNoteModal() {
    document.getElementById('consultationNoteModal').style.display = 'none';
}
</script>

<?php require_once '../../includes/footer.php'; ?>