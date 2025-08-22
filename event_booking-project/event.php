<?php
$conn = mysqli_connect("localhost", "root", "", "event_booking");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$result = $conn->query("SELECT events.*, city.city_name, city.state FROM events 
                        JOIN city ON events.city_id = city.id WHERE events.status='Active' 
                        ORDER BY events.created_at DESC");
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Events - NextEventPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        main {
            flex: 1;
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


        /* Event cards */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            background: #fff;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            height: 180px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-body {
            padding: 1rem 1.25rem;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .card-text {
            color: #555;
            font-size: 0.9rem;
        }

        .card-text.fw-bold {
            font-size: 1.1rem;
            color: #007BFF;
        }

        /* Sold Out button styled */
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            font-weight: 600;
            cursor: not-allowed;
        }

        .btn-secondary i {
            margin-right: 6px;
        }

        /* Filter section */
        .filter-section {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .filter-actions {
            gap: 10px;
            margin-bottom: 15px;
        }

        /* Footer fix */
        footer {
            background-color: #212529;
            color: white;
            padding: 1rem 0;
        }

        footer .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        footer .col-md-6 {
            margin-bottom: 10px;
        }

        footer a {
            text-decoration: none;
            font-weight: 500;
            margin-left: 1rem;
            transition: color 0.3s ease;
            color: #fff;
        }

        footer a:first-child {
            margin-left: 0;
        }

        footer a:hover {
            color: #007BFF;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
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
                    <li class="nav-item"><a class="nav-link active" href="event.php">Events</a></li>
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
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
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

    <main>
        <!-- Filter & Sort Buttons -->
        <section class="container mt-4 d-flex flex-wrap align-items-center filter-actions">

            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="sortEventsBtn" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-sort-alpha-down"></i> Sort Events
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortEventsBtn">
                    <li><a class="dropdown-item sort-option" href="#" data-sort="date-asc">Date: Earliest First</a></li>
                    <li><a class="dropdown-item sort-option" href="#" data-sort="date-desc">Date: Latest First</a></li>
                    <li><a class="dropdown-item sort-option" href="#" data-sort="price-asc">Price: Low to High</a></li>
                    <li><a class="dropdown-item sort-option" href="#" data-sort="price-desc">Price: High to Low</a></li>
                </ul>
            </div>
        </section>

        <!-- Events Section -->
        <section class="container mb-5 mt-4">
            <h2 class="mb-4 text-primary">Upcoming Events</h2>
            <div class="row g-4" id="eventsContainer">
                <?php if ($result && $result->num_rows > 0):
                    while ($event = $result->fetch_assoc()): ?>
                    <div class="col-md-4 col-lg-3 event-card" data-date="<?= htmlspecialchars($event['event_date']) ?>"
                        data-price="<?= htmlspecialchars($event['price']) ?>">
                        <div class="card h-100 shadow-sm">
                            <img src="admin/uploads/<?= htmlspecialchars($event['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                <p class="card-text mb-1"><i class="bi bi-geo-alt-fill"></i>
                                    <?= htmlspecialchars($event['city_name'] . ", " . $event['state']) ?></p>
                                <p class="card-text mb-1"><i class="bi bi-calendar-event"></i>
                                    <?= strtotime($event['event_date']) ? date("d M Y", strtotime($event['event_date'])) : "Invalid Date" ?>
                                </p>
                                <p class="card-text fw-bold mb-3">₹<?= htmlspecialchars($event['price']) ?></p>
                                <?php if ($event['available_seats'] > 0): ?>
                                    <a href="book_event.php?id=<?= $event['id'] ?>" class="btn btn-primary w-100">Book Now</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="bi bi-cart-x"></i> Sold Out
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No events found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="col-md-6 text-md-start mb-3 mb-md-0">
                &copy; <?= date('Y') ?> NextEventPro. All rights reserved.
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const container = document.getElementById("eventsContainer");
            const cards = Array.from(container.querySelectorAll(".event-card"));

            document.querySelectorAll(".sort-option").forEach(option => {
                option.addEventListener("click", e => {
                    e.preventDefault();
                    const sortType = e.target.dataset.sort;
                    let sorted = [...cards];

                    if (sortType === "date-asc") {
                        sorted.sort((a, b) => new Date(a.dataset.date) - new Date(b.dataset.date));
                    } else if (sortType === "date-desc") {
                        sorted.sort((a, b) => new Date(b.dataset.date) - new Date(a.dataset.date));
                    } else if (sortType === "price-asc") {
                        sorted.sort((a, b) => a.dataset.price - b.dataset.price);
                    } else if (sortType === "price-desc") {
                        sorted.sort((a, b) => b.dataset.price - a.dataset.price);
                    }

                    container.innerHTML = "";
                    sorted.forEach(card => container.appendChild(card));
                });
            });
        });
    </script>
</body>

</html>
