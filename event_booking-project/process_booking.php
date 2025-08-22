<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loging.php");
    exit;
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "event_booking");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$st = $conn->prepare("SELECT * FROM events WHERE id = ?");
$st->bind_param("i", $_POST['event_id']);
$st->execute();
$resu = $st->get_result();
$ro = $resu->fetch_assoc();
if($ro['status']!="Active")
{
   echo "<h1>Somthing is wrog please go to the home page</h1>
        <h1><a href='index.php'>Home page</a></h1>
   ";
    exit();
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $event_id = intval($_POST['event_id']);
    $seats_booked = intval($_POST['quantity']);
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
         $a=$row['available_seats'];
    }

// Close statement
    $stmt->close();
 
    
    if ($seats_booked < 1 || $seats_booked > 3 || $a<=0 || $seats_booked>$a) {
        die("Invalid number of seats selected.");
    }

    // Get event price
    $stmt = $conn->prepare("SELECT price FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result || $result->num_rows === 0) {
        die("Event not found.");
    }
    $event = $result->fetch_assoc();
    $price = floatval($event['price']);

    // Calculate total price
    $total_price = $price * $seats_booked;
    $se=$a-$seats_booked;
    // Insert booking into table
    $status = 'Confirmed';
    $insert = $conn->prepare("INSERT INTO booking (user_id, event_id, seats_booked, total_price, status) VALUES (?, ?, ?, ?, ?)");
    $update= $conn->prepare("UPDATE EVENTS SET available_seats=? where id=?");
    $update->bind_param("ii",$se,$event_id);
    $update->execute();
    $insert->bind_param("iiids", $user_id, $event_id, $seats_booked, $total_price, $status);

    if ($insert->execute()) {
        // Redirect or success message
        header("Location: success.php?booking_id=" . $insert->insert_id);
        exit();
    } else {
        echo "Booking failed: " . $conn->error;
    }
}
?>
