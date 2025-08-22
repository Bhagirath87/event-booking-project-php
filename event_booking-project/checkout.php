<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: loging.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event ID");
}

$conn = mysqli_connect("localhost", "root", "", "event_booking");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = intval($_SESSION['user_id']);
$id = intval($_GET['id']);

// Fetch user data
$stmtUser = $conn->prepare("SELECT u.name, u.email, u.number, c.city_name, c.state 
    FROM user_data u 
    LEFT JOIN city c ON u.city_id = c.id 
    WHERE u.id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();
if (!$user) {
    die("User not found.");
}

// Fetch event data
$stmtEvent = $conn->prepare("
    SELECT e.*, c.city_name, c.state 
    FROM events e 
    JOIN city c ON e.city_id = c.id 
    WHERE e.id = ?");
$stmtEvent->bind_param("i", $id);
$stmtEvent->execute();
$event = $stmtEvent->get_result()->fetch_assoc();
if (!$event) {
    die("Event not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Checkout - <?= htmlspecialchars($event['title']) ?> | NextEventPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .brand-name span.blue { color: #007BFF; }
        .brand-name span.green { color: #28a745; }
        .navbar-dark .nav-link.active,
.navbar-dark .nav-link:hover {
    color: #ff3c00;
    /* BMS-like orange */
    font-weight: 600;
}

        .checkout-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 600px;
            margin: 40px auto;
        }
        .info-label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .checkout-box {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 35px 40px;
    max-width: 480px;
    margin: 50px auto;
    font-family: 'Roboto', sans-serif;
    color: #2b2b2b;
    letter-spacing: 0.02em;
}

.checkout-box h2 {
    font-weight: 700;
    font-size: 1.8rem;
    color: #e50914; /* BMS red */
    margin-bottom: 30px;
    text-align: center;
    letter-spacing: 0.05em;
}

.checkout-box h5 {
    font-weight: 600;
    font-size: 1.2rem;
    margin-bottom: 8px;
    color: #212121;
}

.checkout-box p {
    font-size: 0.95rem;
    margin-bottom: 6px;
    color: #555555;
}

.checkout-box hr {
    border: none;
    border-top: 1px solid #eeeeee;
    margin: 25px 0;
}

.info-label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #444444;
    margin-bottom: 6px;
    display: block;
}

input[disabled] {
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    color: #666666;
}

input[type="number"] {
    border: 1.5px solid #ccc;
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 1rem;
    width: 100%;
    transition: border-color 0.3s ease;
}

input[type="number"]:focus {
    border-color: #e50914;
    outline: none;
}

.btn-danger {
    background-color: #e50914;
    border: none;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 14px 0;
    border-radius: 6px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s ease;
    letter-spacing: 0.05em;
}

.btn-danger:hover {
    background-color: #b20710;
}

/* Responsive */
@media (max-width: 576px) {
    .checkout-box {
        padding: 25px 20px;
        max-width: 90%;
    }
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
                <li class="nav-item"><a class="nav-link" href="event.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Bookings</a></li>
                <li class="nav-item"><a class="nav-link" href="./contact.php">Contact Us</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['name']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Checkout Form -->
<div class="checkout-box">
    <h2 class="mb-4 text-primary">Confirm Your Booking</h2>

    <h5>Event: <?= htmlspecialchars($event['title']) ?></h5>
    <p><strong>Date:</strong> <?= date("D, d M Y", strtotime($event['event_date'])) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($event['city_name'] . ", " . $event['state']) ?></p>
    <p><strong>Price per ticket:</strong> ₹<?= htmlspecialchars($event['price']) ?></p>

    <hr>

    <form method="POST" action="process_booking.php">
        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <div class="mb-3">
            <label class="form-label info-label" for="name">Name</label>
            <input type="text" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" disabled />
        </div>

        <div class="mb-3">
            <label class="form-label info-label" for="email">Email</label>
            <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled />
        </div>

        <div class="mb-3">
            <label class="form-label info-label" for="number">Phone Number</label>
            <input type="text" id="number" class="form-control" value="<?= htmlspecialchars($user['number']) ?>" disabled />
        </div>

        <div class="mb-3">
            <label class="form-label info-label" for="city">City</label>
            <input type="text" id="city" class="form-control" value="<?= htmlspecialchars($user['city_name'] . ", " . $user['state']) ?>" disabled />
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label info-label">Quantity</label>
            <input
                type="number"
                id="quantity"
                name="quantity"
                min="1"
                max="3"
                value="1"
                class="form-control"
                required
            />
        </div>

        <button type="submit" id="confirmBtn" class="btn btn-danger w-100">
            Confirm Booking - ₹<?= htmlspecialchars($event['price']) ?>
        </button>
    </form>
</div>

<!-- Footer -->
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
<script>
    const pricePerTicket = <?= (int)$event['price'] ?>;
    const quantityInput = document.getElementById('quantity');
    const confirmBtn = document.getElementById('confirmBtn');

    function updateTotal() {
        let qty = parseInt(quantityInput.value);
        if (isNaN(qty) || qty < 1) qty = 1;
        const total = pricePerTicket * qty;
        confirmBtn.textContent = `Confirm Booking - ₹${total}`;
    }

    quantityInput.addEventListener('input', updateTotal);

    // Initialize total on page load
    updateTotal();
</script>

</body>
</html>
