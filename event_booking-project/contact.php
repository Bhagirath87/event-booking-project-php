<?php 
include "connect.php";
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>NextEventPro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="style.css" rel="stylesheet">
  <style>
    .navbar-dark .nav-link.active,
.navbar-dark .nav-link:hover {
    color: #ff3c00;
    /* BMS-like orange */
    font-weight: 600;
}

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid px-4">
    <a class="navbar-brand brand-name" href="index.php">
      <span class="blue">Next</span><span class="green">EventPro</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link " href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#about-section">About</a></li>
        <li class="nav-item"><a class="nav-link" href="event.php">Events</a></li>
        <li class="nav-item"><a class="nav-link" href="booking.php">Bookings</a></li>
        <li class="nav-item"><a class="nav-link active" href="./contact.php">Contact Us</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION["email"])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle"></i> Profile
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn btn-outline-light" href="loging.php">
              <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Contact Info Section -->
<section class="container py-5">
  <div class="row text-center mb-5">
    <h2>Contact <span class="text-primary">NextEventPro</span></h2>
    <p class="text-muted">We’d love to hear from you. Reach out through any of the ways below.</p>
  </div>

  <div class="row text-center">
    <div class="col-md-4 mb-4">
      <div class="p-4 border rounded h-100 shadow-sm">
        <i class="bi bi-geo-alt-fill fs-1 text-primary"></i>
        <h5 class="mt-3">Our Office</h5>
        <p>Mumbai, Maharashtra, India</p>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="p-4 border rounded h-100 shadow-sm">
        <i class="bi bi-envelope-fill fs-1 text-primary"></i>
        <h5 class="mt-3">Email Us</h5>
        <p>support@nexteventpro.com</p>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="p-4 border rounded h-100 shadow-sm">
        <i class="bi bi-telephone-fill fs-1 text-primary"></i>
        <h5 class="mt-3">Call Us</h5>
        <p>+91 98765 43210</p>
      </div>
    </div>
  </div>
</section>

<!-- Google Map -->
<section class="container mb-5">
  <div class="ratio ratio-16x9 shadow-sm rounded">
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241317.11610192862!2d72.7411!3d19.082197!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7b63b93f5f3ad%3A0x8a1a1c7e8b9e5f5!2sMumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1691234567890" 
      width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy">
    </iframe>
  </div>
</section>



<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
  <div class="container text-center">
    <div class="row">
      <div class="col-md-6 text-md-start mb-3 mb-md-0">
        &copy; <?= date('Y') ?> NextEventPro. All rights reserved.
      </div>
      <div class="col-md-6 text-md-end">
        <a href="#" class="text-white me-3">Privacy Policy</a>
        <a href="#" class="text-white">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
