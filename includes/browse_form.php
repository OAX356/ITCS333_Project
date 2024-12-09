<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Rooms</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <!-- Display Profile Image -->
        <section style="text-align: center;">
            <img src="<?php echo "uploads/" . htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
        </section>

        <h1>Rooms</h1>

        <?php if (!empty($error)): ?>
            <article class="alert error"><?php echo $error; ?></article>
        <?php endif; ?>

        <nav>
            <ul>
                <li><a href="profile.php">Manage Profile</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="booking.php">My Bookings</a></li>
                <?php if ($_SESSION["role"] === 'admin'): ?>
                    <li><a href="admin.php">Admin Panel</a></li>
                <?php endif; ?>
                <li id="logout"><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Search Bar -->
        <form action="browse.php" method="GET">
            <label for="search">Search for Rooms:</label>
            <input type="text" id="search" name="search" placeholder="Enter the name of the room" value="<?php echo $search; ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Rooms List -->
        <section class="room-list">
            <?php if (!empty($rooms)): ?>
                <?php foreach ($rooms as $room): ?>
                    <div class="room-card">
                        <h2><?php echo $room['name']; ?></h2>
                        <p>Capacity: <?php echo $room['capacity']; ?> people</p>
                        <p><?php echo $room['description']; ?></p>
                        <a href="roomDetails.php?room_id=<?php echo $room['id']; ?>" class="button">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No rooms found. Please try a different search term.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
