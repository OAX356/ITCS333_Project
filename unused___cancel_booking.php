<?php
// Start session and include database connection
session_start();
require_once 'db_connection.php'; // Replace with your database connection file

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $bookingId = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : null;
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

    if (!$bookingId || !$userId) {
        echo "Invalid request. Booking ID or user session missing.";
        exit;
    }

    // Check if the booking exists and belongs to the user
    $query = "SELECT schedule_id FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $bookingId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Booking not found or you are not authorized to cancel this booking.";
        exit;
    }

    $row = $result->fetch_assoc();
    $scheduleId = $row['schedule_id'];

    // Delete the booking
    $deleteQuery = "DELETE FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $bookingId);

    if ($stmt->execute()) {
        // Update the schedule to mark it as available again
        $updateQuery = "UPDATE room_schedule SET is_available = 1 WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('i', $scheduleId);
        $stmt->execute();

        echo "Booking successfully canceled.";
    } else {
        echo "Failed to cancel booking. Please try again.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}