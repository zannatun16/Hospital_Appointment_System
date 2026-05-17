<?php

// Location: HospitalAppointmentSystem/views/admin/adminDashboardZ.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/adminModelZ.php';

$stats = getDashboardStatsM($conn);
$recent_appointments = array_slice(getAllAppointmentsM($conn), 0, 10);
?>

<div>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Administrator Dashboard</p>
    
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_doctors']; ?></div>
            <div>Total Doctors</div>
            <a href="manageDoctorsZ.php" class="btn" style="margin-top: 10px;">Manage Doctors</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_patients']; ?></div>
            <div>Total Patients</div>
            <a href="managePatientsZ.php" class="btn" style="margin-top: 10px;">Manage Patients</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_appointments']; ?></div>
            <div>Total Appointments</div>
            <a href="allAppointmentsZ.php" class="btn" style="margin-top: 10px;">View All</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['pending_appointments']; ?></div>
            <div>Pending Appointments</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['completed_appointments']; ?></div>
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
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                            <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['specialization']); ?></td>
                            <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
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
    
    <div style="margin-top: 20px; text-align:center;">
        <a href="manageSpecializationsZ.php" class="btn">Manage Specializations</a>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>