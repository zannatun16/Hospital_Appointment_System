<?php

// Location: HospitalAppointmentSystem/views/admin/manageSpecializationsZ.php

session_start();
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../shared/login.php");
    exit();
}

require_once '../../config/database.php';
require_once '../../models/adminModelZ.php';

$specializations = getAllSpecializationsM($conn);
?>

<div>
    <h1>Manage Specializations</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Add New Specialization</h3>
        </div>
        <form method="POST" action="../../controllers/adminControllerZ.php" onsubmit="return validateSpecializationForm()">
            <input type="hidden" name="action" value="add_specialization">
            
            <div class="form-group">
                <label>Specialization Name:</label>
                <input type="text" id="spec_name" name="name" required>
            </div>
            
            <div style="text-align:center;">
                <button type="submit">Add Specialization</button>
            </div>
        </form>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Specializations</h3>
        </div>
        <div class="table-container">
            <table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#667eea; color:white;">
                        <th>ID</th>
                        <th>Specialization Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($specializations as $spec): ?>
                        <tr>
                            <td><?php echo $spec['id']; ?></td>
                            <td id="spec-name-<?php echo $spec['id']; ?>"><?php echo htmlspecialchars($spec['name']); ?></td>
                            <td>
                                <button onclick="editSpecialization(<?php echo $spec['id']; ?>)" class="btn btn-sm">Edit</button>
                                <form method="POST" action="../../controllers/adminControllerZ.php" style="display:inline;" onsubmit="return confirm('Are you sure? Deleting a specialization will also remove associated doctors.')">
                                    <input type="hidden" name="action" value="delete_specialization">
                                    <input type="hidden" name="id" value="<?php echo $spec['id']; ?>">
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

<!-- Edit Specialization Modal -->
<div id="editSpecModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:400px; margin:50px auto; padding:30px; border-radius:10px;">
        <h2>Edit Specialization</h2>
        <form method="POST" action="../../controllers/adminControllerZ.php">
            <input type="hidden" name="action" value="update_specialization">
            <input type="hidden" id="edit_spec_id" name="id">
            
            <div class="form-group">
                <label>Specialization Name:</label>
                <input type="text" id="edit_spec_name" name="name" required>
            </div>
            
            <div style="text-align:center;">
                <button type="submit">Update Specialization</button>
                <button type="button" onclick="closeEditSpecModal()" style="background:#dc3545;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function validateSpecializationForm() {
    const name = document.getElementById('spec_name').value;
    
    if (name.length < 3) {
        alert('Specialization name must be at least 3 characters');
        return false;
    }
    
    return true;
}

function editSpecialization(id) {
    const name = document.getElementById('spec-name-' + id).innerText;
    document.getElementById('edit_spec_id').value = id;
    document.getElementById('edit_spec_name').value = name;
    document.getElementById('editSpecModal').style.display = 'block';
}

function closeEditSpecModal() {
    document.getElementById('editSpecModal').style.display = 'none';
}
</script>

<?php require_once '../../includes/footer.php'; ?>