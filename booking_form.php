<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="container">
        <div class="container mt-5">
            <!-- Display Profile Image -->
            <section style="text-align: center;">
                <img src="<?php echo "uploads/" . htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
            </section>
            
            <h2 class="text-center">My Bookings</h2>

            <?php if (!empty($error)): ?>
                <article class="alert error"><?php echo htmlspecialchars($error); ?></article>
            <?php endif; ?>

            <!-- Booking Form -->
            <form action="booking.php" method="GET" class="mt-4">
                <!-- filter Selection -->
                <div class="mb-3">
                    <label for="filter_selector" class="form-label">Select status of bookings</label>
                    <select name="filter_selector" id="filter_selector" class="form-select" value="up" required>
                        <option value="all" <?=$selected==0?'selected="selected"':'';?> >All</option>
                        <option value="up" <?=$selected==1?'selected="selected"':'';?>>Upcoming</option>
                        <option value="now" <?=$selected==2?'selected="selected"':'';?>>Now</option>
                        <option value="ex" <?=$selected==3?'selected="selected"':'';?>>Expired</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100">Book Room</button>
            </form>
        </div>

        <p class="text-center"><a href="dashboard.php">Return to Dashboard</a></p>

        <br>
        <section class="container">
            <h2>Bookings</h2>
            <?php if (empty($bookings)): ?>
                <p>No bookings.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($bookings as $booking): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($booking['room_name']); ?></strong><br>
                            <?php echo date("D, M j, Y h:i A", strtotime($booking['timeslot_start'])); ?> - 
                            <?php echo date("h:i A", strtotime($booking['timeslot_end'])); ?>
                            <br>
                            <?php echo $booking["status"]; ?>
                            <br>
                            <a href="roomDetails.php?room_id=<?php echo $booking['room_id']; ?>" class="button">View Details</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
    
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>