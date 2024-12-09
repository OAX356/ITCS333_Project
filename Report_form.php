<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Filter</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Filter Report</h1>
        <form method="GET" action="report.php">
            <label for="roomFilter">Room Name:</label>
            <input type="text" id="roomFilter" name="roomFilter" placeholder="Enter room name">

            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate">

            <button type="submit">Filter</button>
        </form>
        <br>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="report.php">Report</a></li>
            </ul> 
            
        </nav>
    </main>
</body>
</html>
