<?php
session_start();
if (!isset($_GET['booking_id'])) {
    die("Invalid access.");
}
$booking_id = intval($_GET['booking_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Booking Success | NextEventPro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Google Fonts: Poppins for modern look -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    /* Base reset */
    * {
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #e0f7fa, #f1f8e9);
      color: #333;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem 1rem;
    }

    .success-container, .processing-container {
      background: white;
      border-radius: 20px;
      box-shadow: 0 12px 30px rgba(0,0,0,0.1);
      padding: 3rem 2rem;
      max-width: 500px;
      text-align: center;
      position: relative;
      overflow: visible;
      display: none; /* hidden by default */
    }

    /* Show processing container by default */
    .processing-container.active,
    .success-container.active {
      display: block;
    }

    /* Animated checkmark circle */
    .checkmark {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      display: inline-block;
      background: linear-gradient(45deg, #28a745, #00c851);
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.5);
      position: relative;
      margin-bottom: 1.5rem;
      animation: popin 0.5s ease forwards;
    }

    .checkmark svg {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      stroke: white;
      stroke-width: 6;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
      stroke-dasharray: 48;
      stroke-dashoffset: 48;
      animation: draw 0.6s ease forwards 0.5s;
    }

    @keyframes draw {
      to {
        stroke-dashoffset: 0;
      }
    }

    @keyframes popin {
      from {
        transform: scale(0.8);
        opacity: 0;
      }
      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    h2 {
      font-weight: 600;
      color: #28a745;
      font-size: 2rem;
      margin-bottom: 1rem;
      letter-spacing: 0.05em;
    }

    p.fs-5 {
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
    }

    p.text-muted {
      color: #666;
      margin-bottom: 2rem;
      font-style: italic;
    }

    .btn-primary {
      background: linear-gradient(45deg, #007bff, #0056b3);
      border: none;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      font-size: 1rem;
      border-radius: 50px;
      transition: all 0.3s ease;
      box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
    }

    .btn-primary:hover {
      background: linear-gradient(45deg, #0056b3, #003f7f);
      box-shadow: 0 8px 16px rgba(0, 86, 179, 0.6);
    }

    /* Navbar tweaks */
    .navbar-dark {
      background: #222;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .navbar-dark .nav-link.active,
.navbar-dark .nav-link:hover {
    color: #ff3c00;
    /* BMS-like orange */
    font-weight: 600;
}


    footer {
      background: #222;
      color: #ccc;
      font-size: 0.9rem;
      padding: 1.5rem 0;
    }

    footer a {
      color: #ccc;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    footer a:hover {
      color: #0dcaf0;
    }

    /* Brand colors */
    .brand-name span.blue { color: #0dcaf0; }
    .brand-name span.green { color: #28a745; }

    /* Spinner */
    .spinner-border {
      width: 4rem;
      height: 4rem;
      margin-bottom: 1.5rem;
      color: #007bff;
    }

    /* Responsive */
    @media (max-width: 576px) {
      .success-container, .processing-container {
        padding: 2rem 1rem;
      }
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
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="event.php">Events</a></li>
        <li class="nav-item"><a class="nav-link" href="booking.php">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="./contact.php">Contact Us</a></li>
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

<!-- Main Content -->
<main>
  <!-- Processing message -->
  <div class="processing-container active" id="processing">
    <div class="spinner-border" role="status" aria-hidden="true"></div>
    <h3>Processing your booking...</h3>
    <p>Please wait a moment while we confirm your booking.</p>
  </div>

  <!-- Success message -->
  <div class="success-container" id="success">
    <div class="checkmark">
      <svg viewBox="0 0 24 24">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    </div>
    <h2>Booking Confirmed!</h2>
    <p class="fs-5">Your booking ID is <strong>#<?= $booking_id ?></strong>.</p>
    <p class="text-muted">Thank you for booking with <strong>NextEventPro</strong>. We hope you enjoy the event!</p>
    <a href="event.php" class="btn btn-primary">
      <i class="bi bi-arrow-left-circle"></i> Back to Events
    </a>
  </div>
</main>

<!-- Footer -->
<footer class="text-center">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6 mb-2 mb-md-0">
        &copy; <?= date('Y') ?> <strong>NextEventPro</strong>. All rights reserved.
      </div>
      <div class="col-md-6">
        <a href="#" class="me-3">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS for swapping content -->
<script>
  // After 3 seconds, hide processing and show success
  setTimeout(() => {
    document.getElementById('processing').classList.remove('active');
    document.getElementById('success').classList.add('active');
  }, 1500);
</script>

</body>
</html>
