<?php
   
    // Default values 
    $roomFilter = '';
    $startDate = '';
    $endDate = '';

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $roomFilter = $_GET['roomFilter'] ?? '';
        $startDate = $_GET['startDate'] ?? '';
        $endDate = $_GET['endDate'] ?? '';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Filter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Analytics Filter</h1>

        <!-- Filter Form -->
        <form method="GET" action="Analytics.php"> <!-- Submit form to Analytics.php -->
            <label for="roomFilter">Room Name:</label>
            <input type="text" id="roomFilter" name="roomFilter" placeholder="Enter room name" value="<?php echo htmlspecialchars($roomFilter); ?>">

            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate" value="<?php echo htmlspecialchars($startDate); ?>">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate" value="<?php echo htmlspecialchars($endDate); ?>">

            <button type="submit">Apply Filters</button>
        </form>

        <nav>
            <ul>
                <li><a href="Dashboard.php">Dashboard</a></li>
                <li><a href="Analytics.php">Analytics</a></li> <!-- Link to Analytics Page -->
            </ul>
        </nav>
    </main>
</body>
</html>
