<?php

session_start();
require_once '../config/database.php';
require_once '../models/adminModelZ.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'search_doctors') {
        $search_term = $_POST['search_term'];
        $doctors = getAllDoctorsM($conn);
        
        $filtered = array_filter($doctors, function($doctor) use ($search_term) {
            return stripos($doctor['name'], $search_term) !== false || 
                   stripos($doctor['specialization'], $search_term) !== false ||
                   stripos($doctor['email'], $search_term) !== false;
        });
        
        $html = '<table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">';
        $html .= '<thead><tr style="background:#667eea; color:white;"><th>Name</th><th>Email</th><th>Phone</th><th>Specialization</th><th>Fee</th><th>Experience</th><th>Actions</th></tr></thead><tbody>';
        
        foreach ($filtered as $doctor) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($doctor['name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($doctor['email']) . '</td>';
            $html .= '<td>' . htmlspecialchars($doctor['phone']) . '</td>';
            $html .= '<td>' . htmlspecialchars($doctor['specialization']) . '</td>';
            $html .= '<td>$' . $doctor['consultation_fee'] . '</td>';
            $html .= '<td>' . $doctor['experience_years'] . ' years</td>';
            $html .= '<td><button onclick="editDoctor(' . $doctor['user_id'] . ',' . $doctor['doctor_id'] . ')" class="btn">Edit</button> ';
            $html .= '<button onclick="deleteDoctor(' . $doctor['user_id'] . ')" class="btn" style="background:#dc3545;">Delete</button></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        
        echo json_encode(['html' => $html]);
        exit();
    }
}
?>