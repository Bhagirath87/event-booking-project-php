<?php 
include "connect.php";
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>NextEventPro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="style.css" rel="stylesheet">
  <style>
    .navbar-dark .nav-link.active,
.navbar-dark .nav-link:hover {
    color: #ff3c00;
    /* BMS-like orange */
    font-weight: 600;
}

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid px-4">
    <a class="navbar-brand brand-name" href="index.php">
      <span class="blue">Next</span><span class="green">EventPro</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#about-section">About</a></li>
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

<!-- Dynamic Slider -->
<div id="sdk-AD_HOME_CAROUSEL" class="container-fluid px-0 mt-4">
  <div class="position-relative w-100">
    <div id="eventSlider">
      <?php
      $sliderQuery = "SELECT * FROM events WHERE status = 'Active' ORDER BY created_at DESC LIMIT 5";
      $sliderResult = mysqli_query($con, $sliderQuery);
      while ($row = mysqli_fetch_assoc($sliderResult)):
      ?>
        <div class="slide">
          <div class="left">
            <img src="admin/uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" />
          </div>
          <div class="right">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p><?= substr(htmlspecialchars($row['description']), 0, 180) . '...' ?></p>
            <a href="book_event.php?id=<?= $row['id'] ?>" class="btn btn-primary">Book Now</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <button class="btn-prev" onclick="scrollPrev()">
      <i class="bi bi-arrow-left-circle-fill"></i>
    </button>
    <button class="btn-next" onclick="scrollNext()">
      <i class="bi bi-arrow-right-circle-fill"></i>
    </button>
  </div>
</div>


<!-- About -->
<section class="container mt-5" id="about-section">
  <div class="text-center mb-4">
    <h2>About <span class="text-primary">NextEventPro</span></h2>
    <p class="lead">
      NextEventPro is your one-stop platform for discovering, booking, and managing event experiences across India.
      Whether you're into music concerts, tech expos, stand-up comedy, or educational workshops — we have something for everyone.
    </p>
    <p>
      With our seamless booking process, real-time event updates, and curated recommendations, you’ll never miss out on what’s happening around you.
      Our goal is to empower users with a smart, reliable, and simple event management experience.
    </p>
  </div>
</section>

<!-- Contact CTA -->
<section class="text-white text-center py-5" style="background-color: #007BFF;">
  <div class="container">
    <h3 class="mb-3">Got Questions or Need Help?</h3>
    <p class="lead">Reach out to our team anytime. We’re happy to help you plan your perfect event experience.</p>
    <a href="#" class="btn btn-light">Contact Us</a>
  </div>
</section>

<!-- Testimonials -->
<section class="bg-light mt-5 py-5">
  <div class="container">
    <h3 class="text-center text-primary mb-4">What Our Users Say</h3>
    <div class="row text-center">
      <div class="col-md-4">
        <blockquote class="blockquote">
          <p>"Amazing platform to discover and book events easily!"</p>
          <footer class="blockquote-footer">Amit Shah</footer>
        </blockquote>
      </div>
      <div class="col-md-4">
        <blockquote class="blockquote">
          <p>"The booking experience was smooth and quick. Loved it!"</p>
          <footer class="blockquote-footer">Priya Desai</footer>
        </blockquote>
      </div>
      <div class="col-md-4">
        <blockquote class="blockquote">
          <p>"Perfect for last-minute event plans. Highly recommend!"</p>
          <footer class="blockquote-footer">Rahul Mehta</footer>
        </blockquote>
      </div>
    </div>
  </div>
</section>

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

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const slider = document.getElementById('eventSlider');
  const slides = slider.children;
  let currentIndex = 0;

  function updateSliderPosition() {
    const slideWidth = slides[0].offsetWidth;
    slider.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
  }

  function scrollNext() {
    currentIndex = (currentIndex + 1) % slides.length;
    updateSliderPosition();
  }

  function scrollPrev() {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    updateSliderPosition();
  }

  window.addEventListener('resize', updateSliderPosition);
</script>
</body>
</html>
