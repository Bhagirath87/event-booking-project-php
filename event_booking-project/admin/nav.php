<!-- nav.php -->
 <?php
  $conn=mysqli_connect("localhost","root","","event_booking");
if($conn){
}
else{
    die(mysqli_connect_error());
}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    body {
      margin: 0;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #343a40;
      color: white;
      padding-top: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .sidebar .menu-top a,
    .sidebar .menu-bottom a {
      color: white;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .sidebar a .bi {
      margin-right: 10px;
      font-size: 1.2rem;
    }
    .content {
      margin-left: 250px; /* same as sidebar width */
      padding: 20px;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="menu-top">
      <h4 class="text-white text-center mb-4">Admin Panel</h4>
      <a href="index.php"><i class="bi bi-house"></i> Home</a>
      <a href="add_event.php"><i class="bi bi-plus-circle"></i> Add Event</a>
      <a href="edit_event.php"><i class="bi bi-pencil-square"></i> Edit Event</a>
      <a href="booking.php"><i class="bi bi-list-check"></i> Bookings</a>
      <a href="city.php"><i class="bi bi-building"></i> City</a>
      <a href="users.php"><i class="bi bi-person"></i> User</a>
    </div>
    <div class="menu-bottom">
      <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </div>

