<?php
require_once 'db_connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user';
$first_name = $_SESSION['first_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? '';

$reportType = $_GET['reportType'] ?? 'room_usage';
$roomFilter = $_GET['roomFilter'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';

try {
    // Common date range condition
    $dateCondition = "";
    if ($startDate && $endDate) {
        $dateCondition = " AND b.created_at BETWEEN :startDate AND :endDate";
    } elseif ($startDate) {
        $dateCondition = " AND b.created_at >= :startDate";
    } elseif ($endDate) {
        $dateCondition = " AND b.created_at <= :endDate";
    }

    // Room filter condition
    $roomCondition = $roomFilter ? " AND r.name = :roomFilter" : "";

    if ($reportType === 'room_usage') {
        $query = "SELECT r.name AS room_name, COUNT(b.id) AS total_bookings, 
                         SUM(TIMESTAMPDIFF(HOUR, rs.timeslot_start, rs.timeslot_end)) AS total_hours
                  FROM Rooms r
                  LEFT JOIN Bookings b ON r.id = b.room_id
                  LEFT JOIN Room_Schedule rs ON b.schedule_id = rs.id
                  WHERE 1 $roomCondition $dateCondition
                  GROUP BY r.name
                  ORDER BY total_bookings DESC";
    } elseif ($reportType === 'popularity') {
        $query = "SELECT r.name AS room_name, COUNT(b.id) AS total_bookings
                  FROM Rooms r
                  LEFT JOIN Bookings b ON r.id = b.room_id
                  WHERE 1 $roomCondition $dateCondition
                  GROUP BY r.name
                  ORDER BY total_bookings DESC";
    }

    $stmt = $pdo->prepare($query);
    if ($roomFilter) $stmt->bindparam(':roomFilter', $roomFilter);
    if ($startDate) $stmt->bindparam(':startDate', $startDate);
    if ($endDate) $stmt->bindparam(':endDate', $endDate);
    $stmt->execute();
    $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch upcoming bookings for the current user
    $upcomingBookingsQuery = "SELECT r.name AS room_name, rs.timeslot_start, rs.timeslot_end
                              FROM Bookings b
                              JOIN Rooms r ON b.room_id = r.id
                              JOIN Room_Schedule rs ON b.schedule_id = rs.id
                              WHERE b.user_id = :user_id AND rs.timeslot_start > NOW()
                              ORDER BY rs.timeslot_start
                              LIMIT 5";
    $stmt = $pdo->prepare($upcomingBookingsQuery);
    $stmt->execute(['user_id' => $user_id]);
    $upcomingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching report data: " . $e->getMessage());
}

// Function to generate chart data
function generateChartData($reportData, $reportType) {
    $labels = [];
    $datasets = [];

    foreach ($reportData as $row) {
        if ($reportType === 'room_usage') {
            $labels[] = $row['room_name'];
            $datasets['bookings'][] = $row['total_bookings'];
            $datasets['hours'][] = $row['total_hours'];
        }elseif ($reportType === 'popularity') {
            $labels[] = $row['room_name'];
            $datasets['bookings'][] = $row['total_bookings'];
        }
    }

    return [
        'labels' => json_encode($labels),
        'datasets' => json_encode($datasets)
    ];
}

$chartData = generateChartData($reportData, $reportType);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        @media (max-width: 800px) {
            .chart-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <h1>Analytics Report</h1>
        <nav>
            <ul>
                <li><a href="Report_form.php">Back to Report Filter</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
            </ul>
        </nav>

        <section>
            <h2><?= ucfirst(str_replace('_', ' ', $reportType)) ?> Report</h2>
            <div class="chart-container">
                <canvas id="reportChart"></canvas>
            </div>
            <table>
                <thead>
                    <tr>
                        <?php if ($reportType === 'room_usage'): ?>
                            <th>Room Name</th>
                            <th>Total Bookings</th>
                            <th>Total Hours</th>
                        <?php elseif ($reportType === 'popularity'): ?>
                            <th>Room Name</th>
                            <th>Total Bookings</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportData as $row): ?>
                        <tr>
                            <?php if ($reportType === 'room_usage'): ?>
                                <td><?= htmlspecialchars($row['room_name']) ?></td>
                                <td><?= htmlspecialchars($row['total_bookings']) ?></td>
                                <td><?= htmlspecialchars($row['total_hours']) ?></td>
                          
                            <?php elseif ($reportType === 'popularity'): ?>
                                <td><?= htmlspecialchars($row['room_name']) ?></td>
                                <td><?= htmlspecialchars($row['total_bookings']) ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <?php if ($user_role !== 'admin'): ?>
        <section>
            <h2>Your Upcoming Bookings</h2>
            <?php if (empty($upcomingBookings)): ?>
                <p>You have no upcoming bookings.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($upcomingBookings as $booking): ?>
                        <li>
                            <?= htmlspecialchars($booking['room_name']) ?>: 
                            <?= htmlspecialchars($booking['timeslot_start']) ?> - 
                            <?= htmlspecialchars($booking['timeslot_end']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </main>

    <script>
        const ctx = document.getElementById('reportChart').getContext('2d');
        const labels = <?= $chartData['labels'] ?>;
        const datasets = <?= $chartData['datasets'] ?>;

        const chartConfig = {
            type: '<?= $reportType === 'user_bookings' ? 'bar' : 'line' ?>',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Bookings',
                        data: datasets.bookings,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        <?php if ($reportType === 'room_usage'): ?>
            chartConfig.data.datasets.push({
                label: 'Total Hours',
                data: datasets.hours,
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
            });
        <?php endif; ?>

        new Chart(ctx, chartConfig);
    </script>
</body>
</html>
   
