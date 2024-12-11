<?php
require_once 'db_connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all room names for the dropdown
$stmt = $pdo->query("SELECT DISTINCT name FROM Rooms ORDER BY name");
$rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Filter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Custom styles for responsiveness */
        @media (max-width: 800px) {
            form {
                flex-direction: column;
            }
            form > * {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <h1>Analytics Dashboard</h1>
        <form method="GET" action="report.php">
            <label for="reportType">Report Type:</label>
            <select name="reportType" id="reportType" required>
                <option value="room_usage">Room Usage</option>
                <option value="popularity">Room Popularity</option>
            </select>

            <label for="roomFilter">Room:</label>
            <select name="roomFilter" id="roomFilter">
                <option value="">All Rooms</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= htmlspecialchars($room) ?>"><?= htmlspecialchars($room) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate">

            <button type="submit">Generate Report</button>
        </form>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="admin.php">Admin Panel</a></li>
            </ul>
        </nav>
    </main>
</body>
</html>
            
