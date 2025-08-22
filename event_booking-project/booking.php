<?php
include "connect.php";
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: loging.php");
  exit();
}

$userEmail = $_SESSION['email'];
$userQuery = mysqli_query($con, "SELECT id FROM user_data WHERE email = '$userEmail'");
$userData = mysqli_fetch_assoc($userQuery);
$userId = $userData['id'];

$bookingQuery = "
  SELECT b.*, e.title, e.image, e.event_date, e.address, e.city_id, c.city_name
  FROM booking b
  JOIN events e ON b.event_id = e.id
  JOIN city c ON e.city_id = c.id
  WHERE b.user_id = $userId
  ORDER BY b.booking_date DESC
";
$bookingResult = mysqli_query($con, $bookingQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>My Bookings - NextEventPro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    body {
      background-color: #f5f6fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }

    .brand-name span.blue {
      color: #007BFF;
    }

    .brand-name span.green {
      color: #28a745;
    }

    .navbar-dark {
      background-color: #222;
    }

    .navbar-dark .nav-link {
      color: rgba(255, 255, 255, 0.9);
    }

    .navbar-dark .nav-link.active,
    .navbar-dark .nav-link:hover {
      color: #ff3c00; /* BMS-like orange */
      font-weight: 600;
    }

    /* Booking card container */
    .booking-container {
      max-width: 900px;
      margin: auto;
    }

    /* Card */
    .booking-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgb(0 0 0 / 0.1);
      display: flex;
      overflow: hidden;
      margin-bottom: 24px;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      cursor: default;
    }

    .booking-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 28px rgb(0 0 0 / 0.15);
    }

    /* Image section */
    .booking-image {
      width: 160px;
      min-width: 160px;
      height: 160px;
      overflow: hidden;
      border-top-left-radius: 12px;
      border-bottom-left-radius: 12px;
      position: relative;
    }

    .booking-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .booking-card:hover .booking-image img {
      transform: scale(1.1);
    }

    /* Content section */
    .booking-content {
      flex-grow: 1;
      padding: 20px 24px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .booking-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #ff3c00;
      margin-bottom: 8px;
      letter-spacing: 0.03em;
    }

    .booking-details p {
      margin: 6px 0;
      font-size: 0.95rem;
      color: #555;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 500;
    }

    .booking-details p i {
      color: #ff3c00;
      font-size: 1.2rem;
      min-width: 24px;
      text-align: center;
    }

    /* Status badge */
    .booking-status {
      font-weight: 600;
      font-size: 0.9rem;
      padding: 6px 18px;
      border-radius: 20px;
      color: #fff;
      width: fit-content;
      user-select: none;
      box-shadow: 0 2px 8px rgb(0 0 0 / 0.15);
      letter-spacing: 0.05em;
    }

    .booking-status.confirmed {
      background: #28a745;
      box-shadow: 0 2px 12px #28a745aa;
    }

    .booking-status.cancelled {
      background: #dc3545;
      box-shadow: 0 2px 12px #dc3545aa;
    }

    .booking-status.pending {
      background: #6c757d;
      box-shadow: 0 2px 12px #6c757daa;
    }

    /* Bottom row with date and cancel button */
    .booking-bottom {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 14px;
    }

    .booking-date {
      font-size: 0.85rem;
      color: #888;
      font-style: italic;
    }

    /* Cancel button */
    .btn-cancel {
      background-color: #ff3c00;
      color: white;
      font-weight: 600;
      padding: 8px 20px;
      border-radius: 30px;
      border: none;
      box-shadow: 0 4px 12px rgb(255 60 0 / 0.4);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.9rem;
      user-select: none;
    }

    .btn-cancel i {
      font-size: 1.2rem;
    }

    .btn-cancel:hover {
      background-color: #e03600;
      box-shadow: 0 6px 18px rgb(224 54 0 / 0.6);
    }

    /* Responsive */
    @media (max-width: 767px) {
      .booking-card {
        flex-direction: column;
        height: auto;
      }

      .booking-image {
        width: 100%;
        height: 220px;
        border-radius: 12px 12px 0 0;
      }

      .booking-content {
        padding: 16px 20px;
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
          <li class="nav-item"><a class="nav-link active" href="booking.php">Bookings</a></li>
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
              <a class="btn btn-outline-light" href="loging.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info m-4"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
  <?php endif; ?>

  <!-- Bookings Section -->
  <div class="container booking-container mt-5">
    <h3 class="mb-4 text-primary fw-bold">My Bookings</h3>

    <?php if (mysqli_num_rows($bookingResult) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($bookingResult)): ?>
        <div class="booking-card">
          <div class="booking-image">
            <img src="admin/uploads/<?= htmlspecialchars($row['image']) ?>" alt="Event Image" />
          </div>
          <div class="booking-content">
            <div>
              <h4 class="booking-title"><?= htmlspecialchars($row['title']) ?></h4>
              <div class="booking-details">
                <p><i class="bi bi-calendar-event"></i> <?= date("d M, l Y", strtotime($row['event_date'])) ?></p>
                <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($row['city_name']) ?>, <?= htmlspecialchars($row['address']) ?></p>
                <p><i class="bi bi-people-fill"></i> Seats Booked: <?= $row['seats_booked'] ?></p>
                <p><i class="bi bi-currency-rupee"></i> Total Paid: ₹<?= number_format($row['total_price'], 2) ?></p>
              </div>
            </div>

            <div class="booking-bottom">
              <span class="booking-status
                <?= $row['status'] === 'Confirmed' ? 'confirmed' : ($row['status'] === 'Cancelled' ? 'cancelled' : 'pending') ?>">
                <?= $row['status'] ?>
              </span>
              <?php if ($row['status'] != 'Cancelled'): ?>
                <a href="cancel_booking.php?id=<?= $row['id'] ?>" 
                   class="btn-cancel"
                   onclick="return confirm('Are you sure you want to cancel this booking?');">
                  <i class="bi bi-x-circle"></i> Cancel Booking
                </a>
              <?php endif; ?>
            </div>

            <div class="booking-date mt-2">
              Booked on <?= date("d M Y, h:i A", strtotime($row['booking_date'])) ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="alert alert-info">You haven't booked any events yet.</div>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <footer class="py-4 mt-5">
    <div class="container text-center">
      <div class="row">
        <div class="col-md-6 text-md-start mb-3 mb-md-0">
          &copy; <?= date('Y') ?> NextEventPro. All rights reserved.
        </div>
        <div class="col-md-6 text-md-end">
          <a href="#" class="me-3">Privacy Policy</a>
          <a href="#">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
