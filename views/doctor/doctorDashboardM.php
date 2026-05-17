<?php

// Location: HospitalAppointmentSystem/views/doctor/doctorDashboardM.php

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

$total_appointments = count($appointments);
$pending_appointments = 0;
$accepted_appointments = 0;
$completed_appointments = 0;

foreach ($appointments as $appointment) {
    if ($appointment['status'] == 'pending') $pending_appointments++;
    if ($appointment['status'] == 'accepted') $accepted_appointments++;
    if ($appointment['status'] == 'completed') $completed_appointments++;
}
?>

<div>
    <h1>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p><strong>Specialization:</strong> <?php echo $doctor_info['specialization']; ?></p>
    <p><strong>Experience:</strong> <?php echo $doctor_info['experience_years']; ?> years</p>
    <p><strong>Consultation Fee:</strong> $<?php echo $doctor_info['consultation_fee']; ?></p>
    
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
            <div class="stat-number"><?php echo $accepted_appointments; ?></div>
            <div>Accepted Appointments</div>
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
                        <th>Patient Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($appointments) > 0): ?>
                        <?php foreach(array_slice($appointments, 0, 10) as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td><?php echo htmlspecialchars(substr($appointment['reason'], 0, 50)) . '...'; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="myAppointmentsM.php" class="btn">Manage</a>
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
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>