<?php
    require_once 'db_connection.php'; 

    
    $roomFilter = isset($_GET['roomFilter']) ? $_GET['roomFilter'] : '';
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

    
    $query = "SELECT r.name AS room_name, COUNT(b.id) AS total_bookings
              FROM Rooms r
              LEFT JOIN Bookings b ON r.id = b.room_id
              WHERE 1"; 

   
    if ($roomFilter != '') {
        $query .= " AND r.name = :roomFilter";
    }
    if ($startDate != '') {
        $query .= " AND b.booking_date >= :startDate";
    }
    if ($endDate != '') {
        $query .= " AND b.booking_date <= :endDate";
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    if ($roomFilter != '') {
        $stmt->bindparam(':roomFilter', $roomFilter); //"%" . $roomFilter . "%");
    }
    if ($startDate != '') {
        $stmt->bindparam(':startDate', $startDate);
    }
    if ($endDate != '') {
        $stmt->bindparam(':endDate', $endDate);
    }
    $stmt->execute();
    $roomPopularity = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    $user_id = $_SESSION['user_id']; 
    $query = "SELECT b.id AS booking_id, r.name AS room_name, s.timeslot_start, s.timeslot_end
              FROM Bookings b
              JOIN Rooms r ON b.room_id = r.id
              JOIN Room_Schedule s ON b.schedule_id = s.id
              WHERE b.user_id = :user_id AND s.timeslot_start > NOW()
              ORDER BY s.timeslot_start";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $upcomingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link rel="stylesheet" href="1-css/analytics.css"> <!-- Custom styles -->
</head>
<body>
    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="Dashboard.php">Dashboard</a></li>
            <li><a href="Analytics.php">Analytics</a></li> <!-- Link to Analytics Page -->
        </ul>
    </nav>

    <div class="container">
        <h1>Analytics Dashboard</h1>

        <!-- Room Popularity Chart -->
        <h2>Room Popularity</h2>
        <canvas id="roomStatsChart"></canvas>

        <!-- Upcoming Bookings -->
        <h2>Your Upcoming Bookings</h2>
        <ul>
            <?php foreach ($upcomingBookings as $booking): ?>
                <li>
                    <strong>Room:</strong> <?php echo $booking['room_name']; ?><br>
                    <strong>Start:</strong> <?php echo $booking['timeslot_start']; ?><br>
                    <strong>End:</strong> <?php echo $booking['timeslot_end']; ?><br>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Room Popularity Chart (Chart.js)
        const roomNames = <?php echo json_encode(array_column($roomPopularity, 'room_name')); ?>;
        const totalBookings = <?php echo json_encode(array_column($roomPopularity, 'total_bookings')); ?>;
        const ctx = document.getElementById('roomStatsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: roomNames,
                datasets: [{
                    label: 'Total Bookings',
                    data: totalBookings,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>
