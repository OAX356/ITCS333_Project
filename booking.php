<?php
require_once 'db_connection.php';
$error = '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$selected = 0;
$user_id = $_SESSION["user_id"];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $bookings = [];
    if ($_GET['filter_selector'] == 'all') {
        $selected = 0;
        try {
            $stmt = $pdo->prepare("
                SELECT r.name AS room_name, rs.timeslot_start, rs.timeslot_end, b.status, b.room_id
                FROM Bookings b
                INNER JOIN Rooms r ON b.room_id = r.id
                INNER JOIN Room_Schedule rs ON b.schedule_id = rs.id
                WHERE b.user_id = :user_id
                ORDER BY rs.timeslot_start ASC
            ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $bookings = $stmt->fetchAll();
        } catch (PDOException $e) {
            $error = "Error fetching bookings.";
        }

    } elseif ($_GET['filter_selector'] == 'up') {
        $selected = 1;
        try {
            $stmt = $pdo->prepare("
                SELECT r.name AS room_name, rs.timeslot_start, rs.timeslot_end, b.status, b.room_id
                FROM Bookings b
                INNER JOIN Rooms r ON b.room_id = r.id
                INNER JOIN Room_Schedule rs ON b.schedule_id = rs.id
                WHERE b.user_id = :user_id AND rs.timeslot_start > NOW()
                ORDER BY rs.timeslot_start ASC
            ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $bookings = $stmt->fetchAll();
        } catch (PDOException $e) {
            $error = "Error fetching bookings.";
        }

    } elseif ($_GET['filter_selector'] == 'now') {
        $selected = 2;
        try {
            $stmt = $pdo->prepare("
                SELECT r.name AS room_name, rs.timeslot_start, rs.timeslot_end, b.status, b.room_id
                FROM Bookings b
                INNER JOIN Rooms r ON b.room_id = r.id
                INNER JOIN Room_Schedule rs ON b.schedule_id = rs.id
                WHERE b.user_id = :user_id AND rs.timeslot_start <= NOW() AND rs.timeslot_end >= NOW()
                ORDER BY rs.timeslot_start ASC
            ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $bookings = $stmt->fetchAll();
        } catch (PDOException $e) {
            $error = "Error fetching bookings.";
        }
        
    } elseif ($_GET['filter_selector'] == 'ex') {
        $selected = 3;
        try {
            $stmt = $pdo->prepare("
                SELECT r.name AS room_name, rs.timeslot_start, rs.timeslot_end, b.status, b.room_id
                FROM Bookings b
                INNER JOIN Rooms r ON b.room_id = r.id
                INNER JOIN Room_Schedule rs ON b.schedule_id = rs.id
                WHERE b.user_id = :user_id AND rs.timeslot_end < NOW()
                ORDER BY rs.timeslot_start ASC
            ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $bookings = $stmt->fetchAll();
        } catch (PDOException $e) {
            $error = "Error fetching bookings.";
        }
        
    } else {
        $selected = 4;
        $error = 'Invalid filer value.';
    }
} else {
    $error =  "Invalid request method.";
}

include 'booking_form.php';