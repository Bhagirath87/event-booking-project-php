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

$result = $conn->query("SELECT events.*, city.city_name, city.state FROM events 
                        JOIN city ON events.city_id = city.id 
                        ORDER BY events.created_at DESC");
?>

<div class="content">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h3 class="mb-0">Manage Events</h3>
    </div>
    <div class="card-body">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>City</th>
            <th>Date</th>
            <th>Price (₹)</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php $i = 1; while ($event = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($event['title']) ?></td>
                <td><?= htmlspecialchars($event['city_name'] . ", " . $event['state']) ?></td>
                <td><?= htmlspecialchars($event['event_date']) ?></td>
                <td><?= htmlspecialchars($event['price']) ?></td>
                <td><?= htmlspecialchars($event['status']) ?></td>
                <td class="d-flex gap-2">
                  <a href="update_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>
                  <form method="POST" action="delete_event.php" onsubmit="return confirm('Are you sure you want to delete this event?');">
                    <input type="hidden" name="id" value="<?= $event['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center">No events found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
