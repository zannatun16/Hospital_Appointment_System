<?php

// Location: HospitalAppointmentSystem/views/admin/allAppointmentsZ.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/adminModelZ.php';

$appointments = getAllAppointmentsM($conn);
?>

<div>
    <h1>All Appointments</h1>
    
    <div class="table-container">
        <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#667eea; color:white;">
                    <th>Patient Name</th>
                    <th>Patient Email</th>
                    <th>Doctor Name</th>
                    <th>Specialization</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['patient_email']); ?></td>
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>