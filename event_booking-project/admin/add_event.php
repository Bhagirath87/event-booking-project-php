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

$errors = [];
$success = "";
$categories = ["Music", "Tech", "Art", "Sports", "Education"];

// Fetch cities
$cities = [];
$city_result = mysqli_query($conn, "SELECT * FROM city ORDER BY city_name ASC");
while ($row = mysqli_fetch_assoc($city_result)) {
    $cities[] = $row;
}
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Event added successfully!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $city_id = $_POST['city_id'] ?? '';
    $address = $_POST['address'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $total_seats = $_POST['total_seats'] ?? '';
    $price = $_POST['price'] ?? '';
    $status = $_POST['status'] ?? '';
    $available_seats = $total_seats; // Automatically match available_seats to total_seats

    // Image handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = basename($_FILES['image']['name']);
        $target_path = "uploads/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
    } else {
        $errors[] = "Image upload failed.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO events (title, category, description, city_id, address, event_date, total_seats, available_seats, price, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisssidss", $title, $category, $description, $city_id, $address, $event_date, $total_seats, $available_seats, $price, $status, $image_name);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}
?>

<div class="content">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center">
      <h3 class="mb-0"><i class="bi bi-calendar-plus"></i> Add New Event</h3>
    </div>
    <div class="card-body bg-light">

      <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>
          <?= $success ?>
        </div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
              <li><i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="row g-4">

          <div class="col-md-6">
            <label for="title" class="form-label"><i class="bi bi-type"></i> Title *</label>
            <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label for="category" class="form-label"><i class="bi bi-tags"></i> Category *</label>
            <select class="form-select" id="category" name="category" required>
              <option value="">Choose category...</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat ?>" <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12">
            <label for="description" class="form-label"><i class="bi bi-card-text"></i> Description *</label>
            <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          </div>

          <div class="col-md-6">
            <label for="city_id" class="form-label"><i class="bi bi-geo-alt"></i> City *</label>
            <select class="form-select" id="city_id" name="city_id" required>
              <option value="">Choose city...</option>
              <?php foreach ($cities as $city): ?>
                <option value="<?= $city['id'] ?>" <?= (isset($_POST['city_id']) && $_POST['city_id'] == $city['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($city['city_name'] . ", " . $city['state']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label for="address" class="form-label"><i class="bi bi-house"></i> Address *</label>
            <textarea class="form-control" id="address" name="address" rows="2" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
          </div>

          <div class="col-md-4">
            <label for="event_date" class="form-label"><i class="bi bi-calendar-event"></i> Event Date *</label>
            <input type="date" class="form-control" id="event_date" name="event_date" required value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>">
          </div>

          <div class="col-md-4">
            <label for="total_seats" class="form-label"><i class="bi bi-person-lines-fill"></i> Total Seats *</label>
            <input type="number" class="form-control" id="total_seats" name="total_seats" min="1" required value="<?= htmlspecialchars($_POST['total_seats'] ?? '') ?>">
          </div>

          <div class="col-md-4">
            <label for="available_seats" class="form-label"><i class="bi bi-person-check"></i> Available Seats</label>
            <input type="number" class="form-control" id="available_seats" name="available_seats" readonly value="<?= htmlspecialchars($_POST['available_seats'] ?? $_POST['total_seats'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label for="price" class="form-label"><i class="bi bi-currency-rupee"></i> Price (₹) *</label>
            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label for="status" class="form-label"><i class="bi bi-flag-fill"></i> Status *</label>
            <select class="form-select" id="status" name="status" required>
              <option value="">Choose status...</option>
              <?php foreach (["Active", "Inactive", "Cancelled"] as $statusOption): ?>
                <option value="<?= $statusOption ?>" <?= ($_POST['status'] ?? '') == $statusOption ? 'selected' : '' ?>>
                  <?= $statusOption ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12">
            <label for="image" class="form-label"><i class="bi bi-image"></i> Event Image *</label>
            <input class="form-control" type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif" required>
          </div>

          <div class="col-12 text-end">
            <button class="btn btn-success px-4 mt-3" type="submit">
              <i class="bi bi-cloud-arrow-up"></i> Submit Event
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('total_seats').addEventListener('input', function () {
    document.getElementById('available_seats').value = this.value;
  });
</script>
