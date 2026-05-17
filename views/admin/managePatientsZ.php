<?php

// Location: HospitalAppointmentSystem/views/admin/managePatientsZ.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/adminModelZ.php';

$patients = getAllPatientsM($conn);
?>

<div>
    <h1>Manage Patients</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="table-container">
        <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#667eea; color:white;">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($patients as $patient): ?>
                    <tr>
                        <td><?php echo $patient['id']; ?></td>
                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                        <td><?php echo htmlspecialchars($patient['email']); ?></td>
                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                        <td><?php echo date('d M Y', strtotime($patient['created_at'])); ?></td>
                        <td>
                            <form method="POST" action="../../controllers/adminControllerZ.php" onsubmit="return confirm('Are you sure you want to delete this patient? This will also delete all their appointments.');">
                                <input type="hidden" name="action" value="delete_patient">
                                <input type="hidden" name="user_id" value="<?php echo $patient['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>