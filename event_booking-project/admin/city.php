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

// Get all cities with event counts
$cityQuery = "
    SELECT city.id, city.city_name, city.state, COUNT(events.id) AS event_count
    FROM city
    LEFT JOIN events ON city.id = events.city_id
    GROUP BY city.id
    ORDER BY event_count DESC
";
$cityResult = mysqli_query($conn, $cityQuery);

// Fetch all events once
$eventResult = mysqli_query($conn, "SELECT * FROM events");
$eventsByCity = [];
while ($event = mysqli_fetch_assoc($eventResult)) {
    $eventsByCity[$event['city_id']][] = $event;
}
?>

<div class="content">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h3 class="mb-0">Cities Sorted by Number of Events</h3>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>City</th>
            <th>State</th>
            <th>No. of Events</th>
            <th>View Events</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1;
          while ($city = mysqli_fetch_assoc($cityResult)):
            $cid = $city['id'];
            $events = $eventsByCity[$cid] ?? [];
          ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($city['city_name']) ?></td>
            <td><?= htmlspecialchars($city['state']) ?></td>
            <td><span class="badge bg-info"><?= $city['event_count'] ?></span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#city<?= $cid ?>">
                Show Events
              </button>
            </td>
          </tr>
          <tr class="collapse" id="city<?= $cid ?>">
            <td colspan="5">
              <?php if (count($events) > 0): ?>
                <table class="table table-sm table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Title</th>
                      <th>Date</th>
                      <th>Price (₹)</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($events as $event): ?>
                      <tr>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                        <td><?= htmlspecialchars($event['event_date']) ?></td>
                        <td>₹<?= htmlspecialchars($event['price']) ?></td>
                        <td><?= htmlspecialchars($event['status']) ?></td>
                        <td>
                          <a href="update_event.php?id=<?= $event['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                          <form action="delete_event.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="id" value="<?= $event['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div class="text-muted">No events in this city.</div>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Bootstrap JS for collapsible -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
