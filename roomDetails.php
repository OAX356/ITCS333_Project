<?php
require 'db_connection.php'; 
$error = '';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the room ID from the query string
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$room = [];
$timeslots = [];

if ($error == '') {
    // Fetch room details
    try {
        
        $query = "SELECT * FROM rooms WHERE id = :room_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            $error = "Room not found.";
        } else {
            // Fetch available timeslots for the room
            $timeslot_query = "SELECT * FROM Room_Schedule WHERE room_id = :room_id AND is_available = TRUE ORDER BY timeslot_start";
            $stmt = $pdo->prepare($timeslot_query);
            $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->execute();
            $timeslots = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error = "An error occurred while fetching room details: " . $e->getMessage();
    }
}

include 'includes/roomDetails_form.php'; 
?>
