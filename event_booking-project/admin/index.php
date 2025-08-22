<?php
session_start();
if (!isset($_SESSION["un"])) {
    header("Location: admin_login.php");
    exit();
}

// Include sidebar + (nav.php) which already creates a $conn connection at the top
include "nav.php";

// If nav.php didn't provide a working connection for some reason, create one here as a fallback
if (!isset($conn) || !$conn) {
    $conn = mysqli_connect("localhost", "root", "", "event_booking");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
}
// Mark past events as completed
mysqli_query($conn, "
    UPDATE events 
    SET status = 'completed' 
    WHERE event_date < CURDATE() 
      AND status != 'completed'
");


// Summary counts
$totalEventsQuery = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM events");
$totalEvents = $totalEventsQuery ? (int) mysqli_fetch_assoc($totalEventsQuery)['cnt'] : 0;

$totalBookingsQuery = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM booking");
$totalBookings = $totalBookingsQuery ? (int) mysqli_fetch_assoc($totalBookingsQuery)['cnt'] : 0;

$totalUsersQuery = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM user_data");
$totalUsers = $totalUsersQuery ? (int) mysqli_fetch_assoc($totalUsersQuery)['cnt'] : 0;

$totalCitiesQuery = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM city");
$totalCities = $totalCitiesQuery ? (int) mysqli_fetch_assoc($totalCitiesQuery)['cnt'] : 0;

// Collections (only consider confirmed bookings)
$today = date('Y-m-d');
$thisMonth = date('Y-m');

$collectionTodayRes = mysqli_query($conn,
    "SELECT COALESCE(SUM(total_price),0) AS total FROM booking WHERE DATE(booking_date) = '$today' AND LOWER(status) = 'confirmed'");
$collectionToday = $collectionTodayRes ? (float) mysqli_fetch_assoc($collectionTodayRes)['total'] : 0.0;

$collectionThisMonthRes = mysqli_query($conn,
    "SELECT COALESCE(SUM(total_price),0) AS total FROM booking WHERE DATE_FORMAT(booking_date, '%Y-%m') = '$thisMonth' AND LOWER(status) = 'confirmed'");
$collectionThisMonth = $collectionThisMonthRes ? (float) mysqli_fetch_assoc($collectionThisMonthRes)['total'] : 0.0;

$collectionTotalRes = mysqli_query($conn,
    "SELECT COALESCE(SUM(total_price),0) AS total FROM booking WHERE LOWER(status) = 'confirmed'");
$collectionTotal = $collectionTotalRes ? (float) mysqli_fetch_assoc($collectionTotalRes)['total'] : 0.0;

// Recent Bookings (latest 5)
$recentBookings = [];
$rbSql = "SELECT b.booking_date, u.name, e.title AS event
          FROM booking b
          JOIN user_data u ON b.user_id = u.id
          JOIN events e ON b.event_id = e.id
          ORDER BY b.booking_date DESC
          LIMIT 5";
$rbRes = mysqli_query($conn, $rbSql);
if ($rbRes) {
    while ($row = mysqli_fetch_assoc($rbRes)) {
        $recentBookings[] = $row;
    }
}

// Upcoming Events (next 5)
$recentEvents = [];
$reSql = "SELECT id, title, event_date FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5";
$reRes = mysqli_query($conn, $reSql);
if ($reRes) {
    while ($row = mysqli_fetch_assoc($reRes)) {
        $recentEvents[] = $row;
    }
}
?>

<div class="content">
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION["un"]); ?>!</h2>

  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card text-bg-primary">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-calendar-event fs-1 me-3"></i>
          <div>
            <h5 class="card-title">Total Events</h5>
            <h3><?php echo $totalEvents; ?></h3>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-bg-success">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-bookmark-check fs-1 me-3"></i>
          <div>
            <h5 class="card-title">Total Bookings</h5>
            <h3><?php echo $totalBookings; ?></h3>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-bg-warning">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-people fs-1 me-3"></i>
          <div>
            <h5 class="card-title">Total Users</h5>
            <h3><?php echo $totalUsers; ?></h3>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-bg-info">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-geo-alt fs-1 me-3"></i>
          <div>
            <h5 class="card-title">Cities</h5>
            <h3><?php echo $totalCities; ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Collection Cards -->
  <div class="row mb-5">
    <div class="col-md-4 mb-3">
      <div class="card border-success">
        <div class="card-body text-success">
          <h5 class="card-title">Collection Today</h5>
          <h3>₹ <?php echo number_format($collectionToday, 2); ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card border-primary">
        <div class="card-body text-primary">
          <h5 class="card-title">Collection This Month</h5>
          <h3>₹ <?php echo number_format($collectionThisMonth, 2); ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card border-dark">
        <div class="card-body text-dark">
          <h5 class="card-title">Total Collection</h5>
          <h3>₹ <?php echo number_format($collectionTotal, 2); ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="mb-5">
    <h4>Quick Actions</h4>
    <div class="d-flex gap-3 flex-wrap">
      <a href="add_event.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i> Add Event</a>
      <a href="city.php" class="btn btn-secondary"><i class="bi bi-building me-2"></i> City</a>
      <a href="users.php" class="btn btn-warning text-white"><i class="bi bi-person me-2"></i> User</a>
    </div>
  </div>

  <!-- Recent Bookings & Upcoming Events Tables -->
  <div class="container-fluid">
    <div class="row">
      <!-- Recent Bookings -->
      <div class="col-md-6 mb-4">
        <h4>Recent Bookings</h4>
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
              <tr>
                <th>Name</th>
                <th>Event</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recentBookings): ?>
                <?php foreach ($recentBookings as $booking): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($booking['name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['event']); ?></td>
                    <td><?php echo date("d M Y", strtotime($booking['booking_date'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="3" class="text-center text-muted">No bookings found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Upcoming Events -->
      <div class="col-md-6 mb-4">
        <h4>Upcoming Events</h4>
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
              <tr>
                <th>Event</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recentEvents): ?>
                <?php foreach ($recentEvents as $event): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                    <td><?php echo date("d M Y", strtotime($event['event_date'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="2" class="text-center text-muted">No upcoming events</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
