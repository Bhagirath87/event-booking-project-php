<?php
session_start();
include "connect.php";

if (!isset($_SESSION['email'])) {
    header("Location: loging.php");
    exit;
}

$uid = $_SESSION['user_id'];

// Fetch current user data
$sql = "SELECT u.name, u.email, u.number, c.city_name, c.id AS city_id 
        FROM user_data u 
        JOIN city c ON u.city_id = c.id 
        WHERE u.id = $uid";
$res = mysqli_query($con, $sql);
if (!$res) {
    die("Query failed: " . mysqli_error($con));
}
$user = mysqli_fetch_assoc($res);

// Fetch all cities
$cities = [];
$cities_res = mysqli_query($con, "SELECT id, city_name FROM city ORDER BY city_name ASC");
while ($row = mysqli_fetch_assoc($cities_res)) {
    $cities[] = $row;
}

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = mysqli_real_escape_string($con, $_POST['name']);
    $email  = mysqli_real_escape_string($con, $_POST['email']);
    $phone  = mysqli_real_escape_string($con, $_POST['phone']);
    $cityId = (int) $_POST['city'];

    $update_sql = "UPDATE user_data 
                   SET name='$name', email='$email', number='$phone', city_id='$cityId' 
                   WHERE id=$uid";

    if (mysqli_query($con, $update_sql)) {
        header("Location: profile.php");
        exit;
    } else {
        echo "Error updating profile: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Profile | EventPro</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

<style>
    body {
    background: linear-gradient(135deg, #e3f2fd, #f0f5ff);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.profile-wrapper {
    max-width: 800px;
    margin: 60px auto;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 99, 255, 0.15);
    overflow: hidden;
}

.profile-header {
    background: linear-gradient(90deg, #007bff, #0056b3);
    color: white;
    padding: 30px 40px;
    display: flex;
    align-items: center;
    gap: 25px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid rgba(255, 255, 255, 0.7);
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info h1 {
    margin: 0;
    font-weight: 700;
    font-size: 1.8rem;
}

.membership {
    margin-top: 5px;
    font-size: 1.1rem;
    opacity: 0.9;
}

.profile-content {
    padding: 30px 40px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.info-block {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.info-block:hover {
    background: #eaf3ff;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
}

.info-block strong {
    display: block;
    color: #007bff;
    font-weight: 600;
    margin-bottom: 8px;
}

.btn-group-custom {
    margin-top: 30px;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-group-custom a {
    flex: 1;
    font-weight: 600;
    padding: 12px;
    font-size: 1.05rem;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-edit {
    background: #007bff;
    color: white;
}

.btn-edit:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

.btn-logout {
    background: #dc3545;
    color: white;
}

.btn-logout:hover {
    background: #a71d2a;
    transform: translateY(-2px);
}

.navbar-dark .nav-link.active,
.navbar-dark .nav-link:hover {
    color: #ff3c00;
    font-weight: 600;
}
body {
    background: linear-gradient(135deg, #e3f2fd, #f0f5ff);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.form-wrapper {
    max-width: 600px;
    margin: 60px auto;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 99, 255, 0.15);
    padding: 30px 40px;
}
.form-title {
    text-align: center;
    font-weight: 700;
    margin-bottom: 20px;
    color: #004085;
}
.btn-primary {
    background: #007bff;
    border: none;
}
.btn-primary:hover {
    background: #0056b3;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand brand-name" href="#">
      <span class="text-primary">Next</span><span class="text-success">EventPro</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
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
              <li><a class="dropdown-item active" href="profile.php">My Profile</a></li>
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

<!-- Edit Profile Form -->
<div class="form-wrapper">
    <h2 class="form-title"><i class="bi bi-pencil"></i> Edit Profile</h2>
    <form method="POST">
        
        <!-- Name -->
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="form-control" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" required>
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['number']); ?>" class="form-control" required>
        </div>

        <!-- City -->
        <div class="mb-3">
            <label class="form-label">City</label>
            <select name="city" class="form-select" required>
                <option value="">-- Select City --</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo $city['id']; ?>" 
                        <?php echo ($city['id'] == $user['city_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($city['city_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <a href="profile.php" class="btn btn-secondary"><i class="bi bi-x"></i> Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
