<?php
session_start();
if (!isset($_SESSION["un"])) {
    header("Location: admin_login.php");
    exit();
}

include "nav.php";

if (!isset($_GET['id'])) {
    echo "<div class='content'><div class='alert alert-danger'>Event ID not specified.</div></div>";
    exit();
}

$event_id = intval($_GET['id']);
$conn = mysqli_connect("localhost", "root", "", "event_booking");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get event title
$event_result = $conn->query("SELECT title FROM events WHERE id = $event_id");
$event = $event_result->fetch_assoc();

if (!$event) {
    echo "<div class='content'><div class='alert alert-danger'>Event not found.</div></div>";
    exit();
}

// Get all bookings for the event
$booking_sql = "SELECT b.seats_booked, b.total_price, b.booking_date, b.status,
                       u.name, u.email, u.number,
                       c.city_name, c.state
                FROM booking b
                JOIN user_data u ON b.user_id = u.id
                JOIN city c ON u.city_id = c.id
                WHERE b.event_id = $event_id";

$booking_result = $conn->query($booking_sql);

// Initialize totals
$total_seats = 0;
$total_revenue = 0;

$confirmed = [];
$cancelled = [];

if ($booking_result && $booking_result->num_rows > 0) {
    while ($row = $booking_result->fetch_assoc()) {
        if (strtolower($row['status']) === 'cancelled') {
            $cancelled[] = $row;
        } else {
            $confirmed[] = $row;
            $total_seats += $row['seats_booked'];
            $total_revenue += $row['total_price'];
        }
    }
}
?>

<div class="content">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="bi bi-ticket-detailed"></i> Bookings for: <?= htmlspecialchars($event['title']) ?></h4>
      <div class="d-flex gap-2">
        <a href="booking.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left-circle"></i> Back</a>
        <button onclick="printBookings()" class="btn btn-light btn-sm"><i class="bi bi-printer"></i> Print</button>
      </div>
    </div>
    <div class="card-body" id="printArea">
      <?php if (count($confirmed) + count($cancelled) > 0): ?>
        
        <!-- Confirmed Bookings -->
        <h5 class="text-success mb-3"><i class="bi bi-check-circle-fill"></i> Confirmed Bookings</h5>
        <?php if (count($confirmed) > 0): ?>
        <table class="table table-bordered">
          <thead class="table-success">
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Contact</th>
              <th>City</th>
              <th>Seats Booked</th>
              <th>Total Price</th>
              <th>Status</th>
              <th>Booking Date</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; foreach ($confirmed as $row): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?><br><?= htmlspecialchars($row['number']) ?></td>
              <td><?= htmlspecialchars($row['city_name']) ?>, <?= htmlspecialchars($row['state']) ?></td>
              <td><?= $row['seats_booked'] ?></td>
              <td>₹<?= number_format($row['total_price'], 2) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
              <td><?= date("d M Y, h:i A", strtotime($row['booking_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="fw-bold table-secondary">
              <td colspan="4" class="text-end">Total Confirmed:</td>
              <td><?= $total_seats ?></td>
              <td>₹<?= number_format($total_revenue, 2) ?></td>
              <td colspan="2"></td>
            </tr>
          </tbody>
        </table>
        <?php else: ?>
          <div class="alert alert-info">No confirmed bookings found.</div>
        <?php endif; ?>

        <!-- Cancelled Bookings -->
        <h5 class="text-danger mt-4"><i class="bi bi-x-circle-fill"></i> Cancelled Bookings</h5>
        <?php if (count($cancelled) > 0): ?>
        <table class="table table-bordered">
          <thead class="table-danger">
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Contact</th>
              <th>City</th>
              <th>Seats Booked</th>
              <th>Total Price</th>
              <th>Status</th>
              <th>Booking Date</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; foreach ($cancelled as $row): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?><br><?= htmlspecialchars($row['number']) ?></td>
              <td><?= htmlspecialchars($row['city_name']) ?>, <?= htmlspecialchars($row['state']) ?></td>
              <td><?= $row['seats_booked'] ?></td>
              <td>₹<?= number_format($row['total_price'], 2) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
              <td><?= date("d M Y, h:i A", strtotime($row['booking_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
          <div class="alert alert-warning">No cancelled bookings found.</div>
        <?php endif; ?>

      <?php else: ?>
        <div class="alert alert-warning"><i class="bi bi-exclamation-circle"></i> No bookings found for this event.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Print Script -->
<script>
function printBookings() {
  const printContents = document.getElementById('printArea').innerHTML;
  const eventId = <?= json_encode($event_id) ?>;

  const printWindow = window.open('', '_blank');
  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Print Bookings</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
        body { padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f8f9fa; }
      </style>
    </head>
    <body>
      <h3>Booking Details</h3>
      ${printContents}
      <script>
        window.onload = function () {
          window.print();
        };
        window.onafterprint = function () {
          window.location.href = 'event_booking.php?id=' + eventId;
        };
      <\/script>
    </body>
    </html>
  `);
  printWindow.document.close();
}
</script>
