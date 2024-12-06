<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Book a Room</h2>

        <!-- Booking Form -->
        <form action="booking.php" method="POST" class="mt-4">
            <!-- Room Selection -->
            <div class="mb-3">
                <label for="room_id" class="form-label">Select Room</label>
                <select name="room_id" id="room_id" class="form-select" required>
                    <option value="" disabled selected>Choose a room</option>
                    <?php
                    if ($roomsResult->num_rows > 0) {
                        while ($room = $roomsResult->fetch_assoc()) {
                            echo "<option value='" . $room['id'] . "'>" . htmlspecialchars($room['name']) . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No rooms available</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Time Slot Selection -->
            <div class="mb-3">
                <label for="schedule_id" class="form-label">Select Time Slot</label>
                <select name="schedule_id" id="schedule_id" class="form-select" required>
                    <option value="" disabled selected>Choose a time slot</option>
                    <?php
                    if ($scheduleResult->num_rows > 0) {
                        while ($schedule = $scheduleResult->fetch_assoc()) {
                            echo "<option value='" . $schedule['id'] . "'>" . htmlspecialchars($schedule['time_slot']) . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No time slots available</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Book Room</button>
        </form>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>