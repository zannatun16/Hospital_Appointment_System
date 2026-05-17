<?php

session_start();
require_once '../config/database.php';
require_once '../models/patientModelS.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'search_doctors') {
        $search_term = $_POST['search_term'];
        $doctors = searchDoctorsS($conn, $search_term);
        
        $html = '';
        foreach ($doctors as $doctor) {
            $availability = getDoctorAvailabilityS($conn, $doctor['doctor_id']);
            $html .= '<div class="card">';
            $html .= '<div class="card-header">';
            $html .= '<h3 class="card-title">Dr. ' . htmlspecialchars($doctor['name']) . '</h3>';
            $html .= '</div>';
            $html .= '<p><strong>Specialization:</strong> ' . htmlspecialchars($doctor['specialization']) . '</p>';
            $html .= '<p><strong>Experience:</strong> ' . $doctor['experience_years'] . ' years</p>';
            $html .= '<p><strong>Consultation Fee:</strong> $' . $doctor['consultation_fee'] . '</p>';
            $html .= '<p><strong>Email:</strong> ' . htmlspecialchars($doctor['email']) . '</p>';
            $html .= '<p><strong>Phone:</strong> ' . htmlspecialchars($doctor['phone']) . '</p>';
            $html .= '<div><strong>Availability:</strong>';
            if (!empty($availability)) {
                $html .= '<ul style="margin: 8px 0 0 18px; padding:0; list-style-type: disc;">';
                foreach ($availability as $slot) {
                    $html .= '<li style="margin-bottom:4px;">' . htmlspecialchars($slot['day_of_week']) . ': ' . htmlspecialchars(date('h:i A', strtotime($slot['start_time']))) . ' - ' . htmlspecialchars(date('h:i A', strtotime($slot['end_time']))) . '</li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '<p style="margin:6px 0 0;">No availability set yet.</p>';
            }
            $html .= '</div>';
            $html .= '<button onclick="showBookingForm(' . $doctor['doctor_id'] . ')" class="btn">Book Appointment</button>';
            $html .= '</div>';
        }
        
        if (empty($doctors)) {
            $html = '<p>No doctors found</p>';
        }
        
        echo json_encode(['html' => $html]);
        exit();
    }
}
?>