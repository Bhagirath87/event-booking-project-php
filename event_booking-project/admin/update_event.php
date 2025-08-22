<?php
session_start();
if (!isset($_SESSION["un"])) {
    header("Location: admin_login.php");
    exit();
}

include "nav.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='content'><div class='alert alert-danger'>Invalid event ID.</div></div>";
    exit();
}

$event_id = intval($_GET['id']);
$errors = [];

$conn = mysqli_connect("localhost", "root", "", "event_booking");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch event
$event = null;
$result = $conn->query("SELECT * FROM events WHERE id = $event_id");
if ($result && $result->num_rows === 1) {
    $event = $result->fetch_assoc();
} else {
    echo "<div class='content'><div class='alert alert-danger'>Event not found.</div></div>";
    exit();
}

// Fetch cities
$cities = [];
$res = $conn->query("SELECT id, city_name, state FROM city ORDER BY city_name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $cities[] = $row;
    }
}

// Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $city_id = intval($_POST['city_id']);
    $address = trim($_POST['address']);
    $event_date = $_POST['event_date'];
    $total_seats = intval($_POST['total_seats']);
    $available_seats = intval($_POST['available_seats']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $status = trim($_POST['status']);
    $image = $event['image'];

    if (empty($title)) $errors[] = "Title is required.";
    if (empty($description)) $errors[] = "Description is required.";
    if ($city_id <= 0) $errors[] = "Please select a city.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($event_date)) $errors[] = "Event date is required.";
    if ($total_seats <= 0) $errors[] = "Total seats must be greater than 0.";
    if ($available_seats < 0 || $available_seats > $total_seats) $errors[] = "Available seats must be between 0 and total.";
    if ($price < 0) $errors[] = "Price cannot be negative.";
    if (empty($category)) $errors[] = "Category is required.";
    if (empty($status)) $errors[] = "Status is required.";

    // Image upload if new image is selected
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            $newFileName = uniqid('event_', true) . '.' . $fileExt;
            $uploadFileDir = './uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $destPath = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $image = $newFileName;
            } else {
                $errors[] = "Failed to upload new image.";
            }
        } else {
            $errors[] = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    // Update if valid
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE events SET title=?, description=?, city_id=?, address=?, event_date=?, total_seats=?, available_seats=?, price=?, category=?, status=?, image=? WHERE id=?");
        $stmt->bind_param("ssissiiisssi", $title, $description, $city_id, $address, $event_date, $total_seats, $available_seats, $price, $category, $status, $image, $event_id);

        if ($stmt->execute()) {
            // ✅ Redirect after successful update
            header("Location: edit_event.php");
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!-- HTML Form UI -->
<div class="content">
  <div class="card shadow-sm">
    <div class="card-header bg-warning">
      <h3>Edit Event</h3>
    </div>
    <div class="card-body">
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
              <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Title *</label>
            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Category *</label>
            <select class="form-select" name="category" required>
              <?php
              $categories = ["Music", "Sports", "Art", "Technology", "Education", "Food", "Other"];
              foreach ($categories as $cat) {
                  $sel = $event['category'] === $cat ? 'selected' : '';
                  echo "<option value=\"$cat\" $sel>$cat</option>";
              }
              ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Description *</label>
            <textarea class="form-control" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">City *</label>
            <select class="form-select" name="city_id" required>
              <?php foreach ($cities as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php if ($c['id'] == $event['city_id']) echo "selected"; ?>>
                  <?php echo htmlspecialchars($c['city_name'] . ', ' . $c['state']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Address *</label>
            <textarea class="form-control" name="address" required><?php echo htmlspecialchars($event['address']); ?></textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label">Event Date *</label>
            <input type="date" class="form-control" name="event_date" value="<?php echo $event['event_date']; ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Total Seats *</label>
            <input type="number" class="form-control" name="total_seats" min="1" value="<?php echo $event['total_seats']; ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Available Seats *</label>
            <input type="number" class="form-control" name="available_seats" min="0" value="<?php echo $event['available_seats']; ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Price (₹) *</label>
            <input type="number" step="0.01" min="0" class="form-control" name="price" value="<?php echo $event['price']; ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Status *</label>
            <select class="form-select" name="status" required>
              <?php
              $statuses = ["Active", "Inactive", "Cancelled"];
              foreach ($statuses as $s) {
                  $sel = $event['status'] === $s ? 'selected' : '';
                  echo "<option value=\"$s\" $sel>$s</option>";
              }
              ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Change Image (optional)</label>
            <input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.gif">
            <?php if ($event['image']): ?>
              <div class="mt-2">
                <img src="uploads/<?php echo $event['image']; ?>" width="100" style="object-fit:cover;" alt="Current Image">
              </div>
            <?php endif; ?>
          </div>

          <div class="col-12 text-end mt-4">
            <button class="btn btn-primary px-5" type="submit">Update Event</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
