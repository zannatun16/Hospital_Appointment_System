<?php


session_start();
require_once '../config/database.php';
require_once '../models/doctorModelM.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$doctor_info = getDoctorInfoZ($conn, $_SESSION['user_id']);
$doctor_id = $doctor_info['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_status') {
        $appointment_id = $_POST['appointment_id'];
        $status = $_POST['status'];
        
        if (updateAppointmentStatusZ($conn, $appointment_id, $status)) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        exit();
    }
    
    if ($_POST['action'] == 'get_patient_history') {
        $patient_id = $_POST['patient_id'];
        $history = getPatientHistoryZ($conn, $patient_id, $doctor_id);
        
        $html = '<h3>Patient Appointment History</h3>';
        $html .= '<table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">';
        $html .= '<tr><th>Date</th><th>Reason</th><th>Status</th></tr>';
        
        foreach ($history as $appointment) {
            $html .= '<tr>';
            $html .= '<td>' . date('d M Y', strtotime($appointment['appointment_date'])) . '</td>';
            $html .= '<td>' . htmlspecialchars(substr($appointment['reason'], 0, 50)) . '</td>';
            $html .= '<td>' . ucfirst($appointment['status']) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        echo json_encode(['html' => $html]);
        exit();
    }
    
    if ($_POST['action'] == 'get_consultation_note') {
        $appointment_id = $_POST['appointment_id'];
        $note = getConsultationNoteByAppointmentZ($conn, $appointment_id, $doctor_id);
        
        if ($note) {
            echo json_encode([
                'success' => true,
                'diagnosis' => $note['diagnosis'],
                'prescription' => $note['prescription'],
                'created_at' => $note['created_at'] ?? ''
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No consultation note found for this appointment.']);
        }
        exit();
    }
}
?>