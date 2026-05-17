<?php
// Doctor Appointments Management - Zoita
// Location: HospitalAppointmentSystem/views/doctor/myAppointmentsZ.php

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
$doctor_id = $doctor_info['id'];
$appointments = getDoctorAppointmentsZ($conn, $doctor_id);
?>

<div>
    <h1>My Appointments</h1>
    
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
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Patient Email</th>
                    <th>Patient Phone</th>
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
                        <tr id="appointment-<?php echo $appointment['id']; ?>">
                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patient_email']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patient_phone']); ?></td>
                            <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars(substr($appointment['reason'], 0, 50)); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $appointment['status']; ?>" id="status-<?php echo $appointment['id']; ?>">
                                    <?php echo ucfirst($appointment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($appointment['status'] == 'pending'): ?>
                                    <button onclick="updateAppointmentStatus(<?php echo $appointment['id']; ?>, 'accepted')" class="btn" style="background:#28a745;">Accept</button>
                                    <button onclick="updateAppointmentStatus(<?php echo $appointment['id']; ?>, 'rejected')" class="btn" style="background:#dc3545;">Reject</button>
                                <?php elseif ($appointment['status'] == 'accepted'): ?>
                                    <button onclick="showConsultationForm(<?php echo $appointment['id']; ?>, <?php echo $appointment['patient_id']; ?>)" class="btn">Add Consultation</button>
                                    <button onclick="viewPatientHistory(<?php echo $appointment['patient_id']; ?>)" class="btn">View History</button>
                                <?php elseif ($appointment['status'] == 'completed'): ?>
                                    <button onclick="viewConsultationNote(<?php echo $appointment['id']; ?>)" class="btn">View Notes</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">No appointments found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Consultation Note Modal -->
<div id="consultationModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:600px; margin:50px auto; padding:30px; border-radius:10px;">
        <h2>Add Consultation Note</h2>
        <form method="POST" action="../../controllers/doctorControllerM.php" onsubmit="return validateConsultationForm()">
            <input type="hidden" name="action" value="add_consultation_note">
            <input type="hidden" id="appointment_id" name="appointment_id">
            <input type="hidden" id="patient_id" name="patient_id">
            
            <div class="form-group">
                <label>Diagnosis:</label>
                <textarea id="diagnosis" name="diagnosis" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Prescription:</label>
                <textarea id="prescription" name="prescription" rows="4" required></textarea>
            </div>
            
            <div style="text-align:center;">
                <button type="submit">Save Note</button>
                <button type="button" onclick="closeConsultationForm()" style="background:#dc3545;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Patient History Modal -->
<div id="historyModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:700px; margin:50px auto; padding:30px; border-radius:10px;">
        <div id="historyContent"></div>
        <div style="text-align:center; margin-top:20px;">
            <button onclick="closeHistoryModal()" class="btn">Close</button>
        </div>
    </div>
</div>

<!-- Consultation Note Detail Modal -->
<div id="consultationNoteModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:600px; margin:50px auto; padding:30px; border-radius:10px;">
        <h2>Consultation Note</h2>
        <p id="consultationNoteDate" style="font-weight:600; margin-bottom:10px;"></p>
        <div class="form-group">
            <label>Diagnosis</label>
            <p id="consultationNoteDiagnosis" style="white-space:pre-wrap; margin-top:8px;"></p>
        </div>
        <div class="form-group">
            <label>Prescription</label>
            <p id="consultationNotePrescription" style="white-space:pre-wrap; margin-top:8px;"></p>
        </div>
        <div style="text-align:center; margin-top:20px;">
            <button type="button" onclick="closeConsultationNoteModal()" class="btn">Close</button>
        </div>
    </div>
</div>

<script>
function updateAppointmentStatus(appointmentId, status) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../../ajax/doctorAjaxM.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Update status badge
                const statusSpan = document.getElementById('status-' + appointmentId);
                statusSpan.className = 'status-badge status-' + status;
                statusSpan.innerHTML = status.charAt(0).toUpperCase() + status.slice(1);
                
                // Reload page after short delay
                setTimeout(() => {
                    location.reload();
                }, 1000);
                
                alert(response.message);
            } else {
                alert('Failed to update status');
            }
        }
    };
    
    xhr.send('action=update_status&appointment_id=' + appointmentId + '&status=' + status);
}

function showConsultationForm(appointmentId, patientId) {
    document.getElementById('appointment_id').value = appointmentId;
    document.getElementById('patient_id').value = patientId;
    document.getElementById('consultationModal').style.display = 'block';
}

function closeConsultationForm() {
    document.getElementById('consultationModal').style.display = 'none';
}

function validateConsultationForm() {
    const diagnosis = document.getElementById('diagnosis').value;
    const prescription = document.getElementById('prescription').value;
    
    if (!diagnosis || !prescription) {
        alert('Please fill in both diagnosis and prescription');
        return false;
    }
    
    if (diagnosis.length < 10) {
        alert('Please provide a detailed diagnosis (minimum 10 characters)');
        return false;
    }
    
    if (prescription.length < 10) {
        alert('Please provide a detailed prescription (minimum 10 characters)');
        return false;
    }
    
    return true;
}

function viewPatientHistory(patientId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../../ajax/doctorAjaxM.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            document.getElementById('historyContent').innerHTML = response.html;
            document.getElementById('historyModal').style.display = 'block';
        }
    };
    
    xhr.send('action=get_patient_history&patient_id=' + patientId);
}

function closeHistoryModal() {
    document.getElementById('historyModal').style.display = 'none';
}

function viewConsultationNote(appointmentId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../../ajax/doctorAjaxM.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                const dateText = response.created_at ? response.created_at : '';
                document.getElementById('consultationNoteDate').innerText = dateText ? 'Saved on: ' + dateText : '';
                document.getElementById('consultationNoteDiagnosis').innerText = response.diagnosis;
                document.getElementById('consultationNotePrescription').innerText = response.prescription;
                document.getElementById('consultationNoteModal').style.display = 'block';
            } else {
                alert(response.message || 'No consultation note available.');
            }
        }
    };

    xhr.send('action=get_consultation_note&appointment_id=' + encodeURIComponent(appointmentId));
}

function closeConsultationNoteModal() {
    document.getElementById('consultationNoteModal').style.display = 'none';
}
</script>

<?php require_once '../../includes/footer.php'; ?>