<?php
require 'db_connection.php';
$error = '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : null;
    $scheduleId = isset($_POST['schedule_id']) ? intval($_POST['schedule_id']) : null;
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

    if (!$roomId || !$scheduleId || !$userId) {
        echo "Invalid input. Please ensure all fields are filled out.";
        exit;
    }

    // Check if the time slot is still available
    $checkQuery = "SELECT is_available FROM room_schedule WHERE id = ? AND is_available = 1";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('i', $scheduleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "The selected time slot is no longer available.";
        exit;
    }

    // Check for double booking (if the user has already booked the same slot)
    $conflictQuery = "SELECT * FROM bookings WHERE user_id = ? AND schedule_id = ?";
    $stmt = $conn->prepare($conflictQuery);
    $stmt->bind_param('ii', $userId, $scheduleId);
    $stmt->execute();
    $conflictResult = $stmt->get_result();

    if ($conflictResult->num_rows > 0) {
        echo "You have already booked this time slot.";
        exit;
    }

    // Insert the booking into the database
    $insertQuery = "INSERT INTO bookings (user_id, room_id, schedule_id, booking_time) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param('iii', $userId, $roomId, $scheduleId);

    if ($stmt->execute()) {
        // Update the schedule to mark it as unavailable
        $updateQuery = "UPDATE room_schedule SET is_available = 0 WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('i', $scheduleId);
        $stmt->execute();

        echo "Booking successful! Thank you for your reservation.";
    } else {
        echo "An error occurred while processing your booking. Please try again.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}