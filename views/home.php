<?php
// Public Home Page
// Location: HospitalAppointmentSystem/views/home.php

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="home-page">
    <div class="home-hero">
        <h1>Welcome to Hospital Appointment System</h1>
        <p>Book your medical appointments easily and stay connected with our hospitals and doctors.</p>
    </div>

    <div class="contact-card">
        <h2>Our Location & Contact</h2>
        <p><strong>Address:</strong> 123 Health Avenue, Care City, State 45678</p>
        <p><strong>Phone:</strong> +1 (555) 123-4567</p>
        <p><strong>Email:</strong> contact@hospitalappointment.com</p>
        <p><strong>Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM</p>
    </div>

    <div class="map-embed">
        <h3>Find Us</h3>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.1234567890123!2d-122.41941508468362!3d37.77492927975914!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085809c464df3b3%3A0x123456789abcdef0!2sHospital!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus" width="100%" height="320" style="border:0; border-radius: 15px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
