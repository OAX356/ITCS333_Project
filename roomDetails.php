<?php
require_once 'db_connection.php';
$error = '';
$success_message = '';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION["user_id"];

// Get the room ID from the query string
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$room = [];
$timeslots = [];

if ($error == '') {
    try {
        // Fetch room details
        $query = "SELECT * FROM rooms WHERE id = :room_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindparam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            $error = "Room not found.";
        } else {
            // Fetch available timeslots for the room
            $timeslot_query = "SELECT * FROM Room_Schedule WHERE room_id = :room_id AND is_available = TRUE AND timeslot_end > NOW() ORDER BY timeslot_start";
            $stmt = $pdo->prepare($timeslot_query);
            $stmt->bindparam(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->execute();
            $timeslots = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if the user already has a booking for this room
            $check_booking = "SELECT  b.id, rs.timeslot_start, rs.timeslot_end
                                FROM Bookings AS b
                                LEFT JOIN Room_Schedule AS rs on b.schedule_id = rs.id 
                                WHERE b.user_id = :user_id AND b.room_id = :room_id
                                ORDER BY timeslot_start";
            $stmt = $pdo->prepare($check_booking);
            $stmt->bindparam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindparam(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->execute();
            $user_booking = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $timeslot_query = "SELECT * FROM Room_Schedule WHERE room_id = :room_id ORDER BY timeslot_start";
            $stmt = $pdo->prepare($timeslot_query);
            $stmt->bindparam(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->execute();
            $all_timeslots = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // booking 
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_timeslot'])) {
            $timeslot_id = $_POST['timeslot_id'] ?? 0;

            if ($timeslot_id > 0) {
                // Check if the user already has a booking for this room at the selected timeslot
                $check_booking = "SELECT * FROM Bookings WHERE user_id = :user_id AND room_id = :room_id AND schedule_id = :timeslot_id";
                $stmt = $pdo->prepare($check_booking);
                $stmt->bindparam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindparam(':room_id', $room_id, PDO::PARAM_INT);
                $stmt->bindparam(':timeslot_id', $timeslot_id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $error = "You have already booked this room for the selected timeslot.";
                } else {
                    try {

                        $pdo->beginTransaction();
                        // Insert the booking into the Bookings table
                        $book_query = "INSERT INTO Bookings (user_id, room_id, schedule_id) VALUES (:user_id, :room_id, :timeslot_id)";
                        $stmt = $pdo->prepare($book_query);
                        $stmt->bindparam(':user_id', $user_id, PDO::PARAM_INT);
                        $stmt->bindparam(':room_id', $room_id, PDO::PARAM_INT);
                        $stmt->bindparam(':timeslot_id', $timeslot_id, PDO::PARAM_INT);
                        $stmt->execute();

                        // Mark the timeslot as unavailable
                        $update_schedule_query = "UPDATE Room_Schedule SET is_available = FALSE WHERE id = :timeslot_id";
                        $stmt = $pdo->prepare($update_schedule_query);
                        $stmt->bindparam(':timeslot_id', $timeslot_id, PDO::PARAM_INT);
                        $stmt->execute();

                        $pdo->commit();
                        $success_message = "Your booking has been confirmed!";

                        header("location: roomDetails.php?room_id=$room_id");
                        exit();
                    } catch(PDOException $e) {
                        $pdo->rollBack();
                        $error = "The booking insertion is failed: " . $e->getMessage();
                    }
                }
            } else {
                $error = "Please select a valid timeslot.";
            }
        }

        // cancellation 
        if (isset($_GET['cancel_booking_id'])) {
            $booking_id = intval($_GET['cancel_booking_id']);
            try {
                // Fetch the booking details
                $cancel_query = "SELECT * FROM Bookings WHERE id = :booking_id AND user_id = :user_id";
                $stmt = $pdo->prepare($cancel_query);
                $stmt->bindparam(':booking_id', $booking_id, PDO::PARAM_INT);
                $stmt->bindparam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $booking = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($booking) {
                    try {
                        $pdo->beginTransaction();
                        // Cancel the booking (delete from Bookings table)
                        $delete_query = "DELETE FROM Bookings WHERE id = :booking_id";
                        $stmt = $pdo->prepare($delete_query);
                        $stmt->bindparam(':booking_id', $booking_id, PDO::PARAM_INT);
                        $stmt->execute();

                        // Make the timeslot available again
                        $update_schedule_query = "UPDATE Room_Schedule SET is_available = TRUE WHERE id = :timeslot_id";
                        $stmt = $pdo->prepare($update_schedule_query);
                        $stmt->bindparam(':timeslot_id', $booking['schedule_id'], PDO::PARAM_INT);
                        $stmt->execute();

                        $pdo->commit();
                        $success_message = "Your booking has been cancelled successfully.";

                        header("location: roomDetails.php?room_id=$room_id");
                        exit();
                    } catch(PDOException $e) {
                        $error = "The Cancellation is failed: " . $e->getMessage();
                    }
                } else {
                    $error = "Booking not found or you cannot cancel this booking.";
                }
            } catch (PDOException $e) {
                $error = "The Cancellation is failed: " . $e->getMessage();
            }
        }

    } catch (PDOException $e) {
        $error = "room details is not found: " . $e->getMessage();
    }
}

include 'includes/roomDetails_form.php';
?>
