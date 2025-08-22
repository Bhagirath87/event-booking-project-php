<?php
include "connect.php";
session_start();

if (!isset($_SESSION['email'])) {
  header("Location: loging.php");
  exit();
}

if (isset($_GET['id'])) {
  $bookingId = intval($_GET['id']);

  // Get the booking details
  $bookingQuery = mysqli_query($con, "SELECT event_id, seats_booked, status FROM booking WHERE id = $bookingId");
  if (mysqli_num_rows($bookingQuery) == 1) {
    $booking = mysqli_fetch_assoc($bookingQuery);

    if ($booking['status'] === 'Cancelled') {
      $_SESSION['message'] = "Booking already cancelled.";
    } else {
      $eventId = $booking['event_id'];
      $seats = $booking['seats_booked'];

      // Start transaction
      mysqli_begin_transaction($con);

      try {
        // 1. Update booking status to Cancelled
        mysqli_query($con, "UPDATE booking SET status = 'Cancelled' WHERE id = $bookingId");

        // 2. Increase available seats in the event
        mysqli_query($con, "UPDATE events SET available_seats = available_seats + $seats WHERE id = $eventId");

        // Commit transaction
        mysqli_commit($con);

        $_SESSION['message'] = "Booking cancelled successfully!";
      } catch (Exception $e) {
        mysqli_rollback($con);
        $_SESSION['message'] = "Error cancelling booking.";
      }
    }
  } else {
    $_SESSION['message'] = "Booking not found.";
  }
}

header("Location: booking.php");
exit();
?>
