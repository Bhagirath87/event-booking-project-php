<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid event ID");
}

$conn = mysqli_connect("localhost", "root", "", "event_booking");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("
    SELECT e.*, c.city_name, c.state
    FROM events e
    JOIN city c ON e.city_id = c.id
    WHERE e.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
  die("Event not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($event['title']) ?> - NextEventPro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .brand-name span.blue {
      color: #007BFF;
    }

    .brand-name span.green {
      color: #28a745;
    }

    .navbar-dark .nav-link.active,
    .navbar-dark .nav-link:hover {
      color: #ff3c00;
      /* BMS-like orange */
      font-weight: 600;
    }

    .event-header img {
      width: 100%;
      max-height: 400px;
      object-fit: cover;
      border-radius: 8px;
    }

    .book-box {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .info .item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      font-size: 1rem;
    }

    .info .item i {
      margin-right: 10px;
      font-size: 1.2rem;
      color: #007BFF;
    }

    /* ========== New: Limit description lines on mobile ========== */
    @media (max-width: 767px) {
      .event-description {
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Show only 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
      }
      .event-description.expanded {
        -webkit-line-clamp: unset;
        cursor: default;
      }

      .read-more-btn {
        display: block;
        color: #007BFF;
        cursor: pointer;
        margin-top: 0.25rem;
        font-weight: 600;
      }
    }
  </style>
</head>

<body>

  <!-- ✅ Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
      <a class="navbar-brand brand-name" href="index.php">
        <span class="blue">Next</span><span class="green">EventPro</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="event.php">Events</a></li>
          <li class="nav-item"><a class="nav-link" href="booking.php">Bookings</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Contact Us</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <?php if (isset($_SESSION["email"])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> Profile
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
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

  <!-- ✅ Event Section -->
  <div class="container my-5">
    <div class="row">
      <div class="col-lg-8">
        <div class="event-header mb-4">
          <img src="admin/uploads/<?= htmlspecialchars($event['image']) ?>"
            alt="<?= htmlspecialchars($event['title']) ?>">
        </div>

        <h2 class="mb-3 text-primary"><?= htmlspecialchars($event['title']) ?></h2>

        <!-- ✅ Category from DB -->
        <div class="mb-3">
          <span class="badge bg-primary"><?= htmlspecialchars($event['category']) ?></span>
        </div>

        <hr>

        <h4>About The Event</h4>
        <!-- ===== Updated description with truncation and read more ===== -->
        <p class="event-description" id="eventDescription">
          <?= nl2br(htmlspecialchars($event['description'])) ?>
        </p>
        <span class="read-more-btn" id="readMoreBtn" style="display:none;">Read Full</span>
      </div>

      <div class="col-lg-4">
        <div class="book-box">
          <div class="info mb-3">
            <div class="item">
              <i class="bi bi-calendar-event"></i>
              <?= date("D, d M Y", strtotime($event['event_date'])) ?>
            </div>
            <div class="item">
              <i class="bi bi-geo-alt-fill"></i>
              <?= htmlspecialchars($event['city_name'] . ", " . $event['state']) ?>
            </div>
            <div class="item">
              <i class="bi bi-geo-alt"></i>
              <?= htmlspecialchars($event['address']) ?>
            </div>
          </div>
          <hr>
          <h3>₹<?= htmlspecialchars($event['price']) ?> onwards</h3>
          <p class="text-danger small">Filling Fast</p>
          <?php if ($event['available_seats'] > 0):
            // <!-- ✅ Smart Book Now Button -->
            if (isset($_SESSION['email'])): ?>
              <a href="checkout.php?id=<?= $event['id'] ?>" class="btn btn-success w-100">
                <i class="bi bi-ticket-fill"></i> Book Now
              </a>
            <?php else: ?>
              <a href="loging.php?redirect=checkout.php?id=<?= $event['id'] ?>" class="btn btn-warning w-100">
                <i class="bi bi-box-arrow-in-right"></i> Login to Book
              </a>
            <?php endif;

          else:
            ?><button class="btn btn-secondary  w-100" disabled>
              <i class="bi bi-cart-x"></i> Sold Out
            </button><?php
                    endif;
                      ?>


        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Footer -->
  <footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
      <div class="row">
        <div class="col-md-6 text-md-start mb-3 mb-md-0">
          &copy; <?= date('Y') ?> NextEventPro. All rights reserved.
        </div>
        <div class="col-md-6 text-md-end">
          <a href="#" class="text-white me-3">Privacy Policy</a>
          <a href="#" class="text-white">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- ===== New script for Read Full toggle ===== -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const desc = document.getElementById('eventDescription');
      const btn = document.getElementById('readMoreBtn');

      // Helper to check if text is truncated
      function checkOverflow(el) {
        return el.scrollHeight > el.clientHeight;
      }

      function toggleDescription() {
        if (desc.classList.contains('expanded')) {
          desc.classList.remove('expanded');
          btn.textContent = 'Read Full';
        } else {
          desc.classList.add('expanded');
          btn.textContent = 'Read Less';
        }
      }

      function init() {
        if (window.innerWidth <= 767) {
          if (checkOverflow(desc)) {
            btn.style.display = 'inline';
            btn.addEventListener('click', toggleDescription);
          }
        }
      }

      init();

      window.addEventListener('resize', () => {
        if (window.innerWidth > 767) {
          desc.classList.remove('expanded');
          btn.style.display = 'none';
        } else {
          init();
        }
      });
    });
  </script>
</body>

</html>
