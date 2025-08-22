<?php
session_start();
if (!isset($_SESSION["un"])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);

    $conn = mysqli_connect("localhost", "root", "", "event_booking");
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Get image filename before deleting event
    $stmt = $conn->prepare("SELECT image FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    // Delete event
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Delete image file from folder
        if ($image && file_exists("uploads/" . $image)) {
            unlink("uploads/" . $image);
        }
    }

    $stmt->close();
    $conn->close();
}

header("Location: edit_event.php");
exit();
