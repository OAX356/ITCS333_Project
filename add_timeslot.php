<?php
require_once 'db_connection.php';

$error = '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Get the room ID
$room_id = $_GET['room_id'] ?? null;
if (!$room_id) {
    header('Location: admin.php');
    exit;
}

// Fetch room details
$stmt = $pdo->prepare("SELECT * FROM Rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    $error = "Room not found.";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_timeslot') {
            // Add a new time slot
            $start_time = $_POST['start_time'] ?? null;
            $end_time = $_POST['end_time'] ?? null;

            // Validate inputs
            if (!$start_time || !$end_time) {
                $error = "Both start and end times are required.";
            } elseif (strtotime($start_time) >= strtotime($end_time)) {
                $error = "Start time must be earlier than end time.";
            } else {
                // Check for overlapping time slots
                $stmt = $pdo->prepare(
                    "SELECT * FROM Room_Schedule 
                     WHERE room_id = ? 
                     AND ((timeslot_start < ? AND timeslot_end > ?) OR 
                          (timeslot_start < ? AND timeslot_end > ?) OR 
                          (timeslot_start >= ? AND timeslot_end <= ?))"
                );
                $stmt->execute([$room_id, $end_time, $end_time, $start_time, $start_time, $start_time, $end_time]);
                $overlap = $stmt->fetch();

                if ($overlap) {
                    $error = "The new time slot overlaps with an existing time slot.";
                } else {
                    // Insert the new time slot
                    $stmt = $pdo->prepare(
                        "INSERT INTO Room_Schedule (room_id, timeslot_start, timeslot_end, is_available) 
                         VALUES (?, ?, ?, 1)"
                    );
                    $stmt->execute([$room_id, $start_time, $end_time]);

                    header("Location: add_timeslot.php?room_id=$room_id");
                    exit;
                }
            }
        } elseif ($_POST['action'] === 'delete_timeslot') {
            // Delete the time slot
            $timeslot_id = $_POST['timeslot_id'] ?? null;

            if ($timeslot_id) {
                $stmt = $pdo->prepare("DELETE FROM Room_Schedule WHERE id = ?");
                $stmt->execute([$timeslot_id]);

                header("Location: add_timeslot.php?room_id=$room_id");
                exit;
            } else {
                $error = "Invalid timeslot ID.";
            }
        }
    }
}

// Fetch all time slots for this room
$stmt = $pdo->prepare("SELECT * FROM Room_Schedule WHERE room_id = ? ORDER BY timeslot_start");
$stmt->execute([$room_id]);
$time_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Time Slot</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Add Time Slot for Room: <?= htmlspecialchars($room['name']) ?></h1>

        <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="add_timeslot">
            <label for="start_time">Start Time:</label>
            <input type="datetime-local" id="start_time" name="start_time" required>
            <br>
            <label for="end_time">End Time:</label>
            <input type="datetime-local" id="end_time" name="end_time" required>
            <br>
            <button type="submit">Add Time Slot</button>
        </form>

        <h2>Existing Time Slots for Room: <?= htmlspecialchars($room['name']) ?></h2>
        <?php if ($time_slots): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($time_slots as $slot): ?>
                        <tr>
                            <td><?= htmlspecialchars($slot['id']) ?></td>
                            <td><?= htmlspecialchars($slot['timeslot_start']) ?></td>
                            <td><?= htmlspecialchars($slot['timeslot_end']) ?></td>
                            <td><?= $slot['is_available'] ? 'Available' : 'Unavailable' ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_timeslot">
                                    <input type="hidden" name="timeslot_id" value="<?= $slot['id'] ?>">
                                    <button type="submit" style="background-color: red; color: white;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No time slots found for this room.</p>
        <?php endif; ?>

        <p style="text-align: center;"><a href="admin.php">Back to Admin Panel</a></p>
    </main>
</body>
</html>

