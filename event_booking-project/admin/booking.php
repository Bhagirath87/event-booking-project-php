<?php
session_start();
if (!isset($_SESSION["un"])) {
    header("Location: admin_login.php");
    exit();
}

include "nav.php";

$conn = mysqli_connect("localhost", "root", "", "event_booking");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get event and booking stats
$query = "SELECT e.*, 
          (e.total_seats - e.available_seats) AS booked_seats,
          c.city_name, c.state
          FROM events e
          JOIN city c ON e.city_id = c.id
          ORDER BY e.created_at DESC";

$result = $conn->query($query);
?>

<div class="content">
  <h3 class="mb-4">All Bookings</h3>
  <div class="row">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($event = $result->fetch_assoc()): ?>
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card shadow-sm h-100">
            <img src="uploads/<?= htmlspecialchars($event['image']) ?>" class="card-img-top" alt="Event Image" style="height: 200px; object-fit: cover;">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
              <p class="card-text"><strong>City:</strong> <?= htmlspecialchars($event['city_name'] . ', ' . $event['state']) ?></p>
              <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
              <p class="card-text mb-1"><strong>Price:</strong> ₹<?= htmlspecialchars($event['price']) ?></p>
              <p class="card-text mb-1"><strong>Total Seats:</strong> <?= htmlspecialchars($event['total_seats']) ?></p>
              <p class="card-text mb-1 text-warning"><strong>Booked Seats:</strong> <?= htmlspecialchars($event['booked_seats']) ?></p>
              <p class="card-text mb-2 text-success"><strong>Available Seats:</strong> <?= htmlspecialchars($event['available_seats']) ?></p>
              <a href="event_booking.php?id=<?= $event['id'] ?>" class="btn btn-primary w-100">
                <i class="bi bi-eye"></i> Show Booking Info
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted">No events found.</p>
    <?php endif; ?>
  </div>
</div>
