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

// Get users and join with city table
$sql = "SELECT user_data.id, user_data.name, user_data.email, user_data.number, city.city_name, city.state 
        FROM user_data 
        LEFT JOIN city ON user_data.city_id = city.id 
        ORDER BY user_data.id DESC";
$result = $conn->query($sql);
?>

<div class="content">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h3 class="mb-0">Registered Users</h3>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Number</th>
            <th>City</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php $i = 1; while ($user = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['number']) ?></td>
                <td><?= htmlspecialchars($user['city_name'] ?? 'N/A') . 
                     (isset($user['state']) ? ', ' . $user['state'] : '') ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" class="text-center">No users found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
