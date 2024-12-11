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
        <!-- Display Profile Image -->
        <section style="text-align: center;">
            <?php 
            $profile_picture_path = isset($user['profile_picture']) && !empty($user['profile_picture']) 
                                    ? "uploads" . htmlspecialchars($user['profile_picture']) 
                                    : 'uploads/default_1.png';
            ?>
            <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
        </section>

        <h1>Welcome, <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>!</h1>

        <?php if (!empty($error)): ?>
            <article class="alert error"><?php echo htmlspecialchars($error); ?></article>
        <?php endif; ?>

        <nav>
            <ul>
                <li><a href="profile.php">Manage Profile</a></li>
                <?php if ($user_role === 'user'): ?>
                    <li><a href="browse.php">Browse Rooms</a></li>
                    <li><a href="booking.php?filter_selector=all">My Bookings</a></li>
                <?php endif; ?>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="admin.php">Admin Panel</a></li>
                    <li><a href="Report_form.php">Report</a></li>
                <?php endif; ?>
                <li id="logout"><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <br>

        <?php if ($user_role === "user"): ?>
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
        <?php endif; ?>
    </main>
</body>
</html>
