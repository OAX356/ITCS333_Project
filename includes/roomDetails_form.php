<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $room['room_name'] ?? 'Room Details'; ?> <!-- output the room name if the room is null it defult to Room Details -->
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

            <!-- display the room details -->
            <div class="room-details">
                <img src="<?php echo $room['image_url']; ?>" alt="<?php echo $room['room_name']; ?>">
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

            <a href="browse.php" class="button">Back to Browse</a>
        <?php endif; ?>
    </main>
</body>
</html>
