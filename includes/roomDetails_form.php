<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $room['room_name'] ?? 'Room Details'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <!-- if there error display message if not display the room name -->
        <?php if (!empty($error)): ?>
            <article class="alert error" role="alert">
                <?php echo $error; ?>
            </article>
        <?php else: ?>
            <h1><?php echo $room['room_name']; ?></h1>

            <!-- Display room details -->
            <div class="room-details">
                <p>Capacity: <?php echo $room['capacity']; ?> people</p>
                <p>Equipment: <?php echo $room['equipment'] ?? 'No equipment listed'; ?></p>
                <p>Description: <?php echo $room['description'] ?? 'No description available'; ?></p>
            </div>

            <h2>Available Timeslots</h2>
            <?php if (!empty($timeslots)): ?>
                <ul class="timeslots">
                    <?php foreach ($timeslots as $timeslot): ?>
                        <li>
                            <?php echo date('Y-m-d H:i', strtotime($timeslot['timeslot_start'])); ?> 
                            to 
                            <?php echo date('Y-m-d H:i', strtotime($timeslot['timeslot_end'])); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                
                <p>No available timeslots for this room.</p>
            <?php endif; ?>

            <!-- Booking Interface -->
            <h2>Select a Timeslot to Book</h2>
            <?php if (!empty($timeslots)): ?>
                <form action="roomDetails.php?room_id=<?php echo $room_id; ?>" method="POST">
                    <ul class="timeslots">
                        <?php foreach ($timeslots as $timeslot): ?>
                            <li>
                                <input type="radio" id="timeslot_<?php echo $timeslot['id']; ?>" name="timeslot_id" value="<?php echo $timeslot['id']; ?>" required>
                                <label for="timeslot_<?php echo $timeslot['id']; ?>">
                                    <?php echo date('Y-m-d H:i', strtotime($timeslot['timeslot_start'])); ?> to <?php echo date('Y-m-d H:i', strtotime($timeslot['timeslot_end'])); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="submit" name="book_timeslot">Book this room</button>
                </form>
            <?php else: ?>
                <p>No available timeslots for this room.</p>
            <?php endif; ?>

            <!-- Check if the user already has a booking and display a cancel button -->
            <?php if (!empty($user_booking)): ?>
                <h3>Your current booking:</h3>
                <p>You have already booked this room for the timeslot: 
                    <?php echo date('Y-m-d H:i', strtotime($user_booking['timeslot_start'])); ?> to 
                    <?php echo date('Y-m-d H:i', strtotime($user_booking['timeslot_end'])); ?>
                </p>
                <a href="roomDetails.php?room_id=<?php echo $room_id; ?>&cancel_booking_id=<?php echo $user_booking['id']; ?>" class="button">Cancel Booking</a>
            <?php endif; ?>

            <a href="browse.php" class="button">Back to Browse</a>
        <?php endif; ?>
    </main>
</body>
</html>
