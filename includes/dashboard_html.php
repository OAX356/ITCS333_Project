<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Welcome, <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>!</h1>

        <?php if (!empty($error)): ?>
            <article class="alert error"><?php echo htmlspecialchars($error); ?></article>
        <?php endif; ?>

        <nav>
            <ul>
                <li><a href="profile.php">Manage Profile</a></li>
                <li><a href="browse.php">Browse Rooms</a></li>
                <li><a href="booking.php?filter_selector=all">My Bookings</a></li>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="admin.php">Admin Panel</a></li>
                <?php endif; ?>
                <li id="logout"><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <br>

        <section>
            <h2>Upcoming Bookings</h2>
            <?php if (empty($upcoming_bookings)): ?>
                <p>No upcoming bookings.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($upcoming_bookings as $booking): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($booking['room_name']); ?></strong><br>
                            <?php echo date("D, M j, Y h:i A", strtotime($booking['timeslot_start'])); ?> - 
                            <?php echo date("h:i A", strtotime($booking['timeslot_end'])); ?>
                            <?php echo $booking["status"]; ?>
                            <a href="roomDetails.php?room_id=<?php echo $booking['room_id']; ?>" class="button">View Details</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
