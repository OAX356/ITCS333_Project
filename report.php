<?php
require 'db_connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user';
$first_name = $_SESSION['first_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? '';

$roomFilter = $_GET['roomFilter'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

try {
    $query = "SELECT r.name AS room_name, COUNT(b.id) AS total_bookings
              FROM Rooms r
              LEFT JOIN Bookings b ON r.id = b.room_id
              WHERE 1";
    if ($roomFilter) $query .= " AND r.name LIKE :roomFilter";
    if ($startDate) $query .= " AND b.created_at >= :startDate";
    if ($endDate) $query .= " AND b.created_at <= :endDate";
    $query .= " GROUP BY r.name ORDER BY total_bookings DESC";
    $stmt = $pdo->prepare($query);
    if ($roomFilter) $stmt->bindValue(':roomFilter', '%' . $roomFilter . '%');
    if ($startDate) $stmt->bindValue(':startDate', $startDate);
    if ($endDate) $stmt->bindValue(':endDate', $endDate);
    $stmt->execute();
    $roomPopularity = $stmt->fetchAll();

    $bookingQuery = "SELECT r.name AS room_name, rs.timeslot_start, rs.timeslot_end
                     FROM Bookings b
                     JOIN Rooms r ON b.room_id = r.id
                     JOIN Room_Schedule rs ON b.schedule_id = rs.id
                     WHERE b.user_id = :user_id AND rs.timeslot_start > NOW()
                     ORDER BY rs.timeslot_start";
    $stmt = $pdo->prepare($bookingQuery);
    $stmt->execute(['user_id' => $user_id]);
    $upcomingBookings = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching report data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Report</h1>
        <p style="text-align: center;"><a href="Report_form.php">Report Filter</a></p>
        <section>
            <h2>Room Popularity</h2>
            <ul>
                <?php foreach ($roomPopularity as $room): ?>
                    <li><?php echo htmlspecialchars($room['room_name']); ?>: <?php echo htmlspecialchars($room['total_bookings']); ?> bookings</li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section>
            <h2>Your Upcoming Bookings</h2>
            <ul>
                <?php foreach ($upcomingBookings as $booking): ?>
                    <li>
                        <?php echo htmlspecialchars($booking['room_name']); ?>: 
                        <?php echo htmlspecialchars($booking['timeslot_start']); ?> - 
                        <?php echo htmlspecialchars($booking['timeslot_end']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
</body>
</html>
