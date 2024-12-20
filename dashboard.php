<?php
require_once 'db_connection.php';
$error = '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = 'user';

// Fetch user details and role
try {
    $stmt = $pdo->prepare("SELECT u.id, u.email, u.role, p.first_name, p.last_name, p.profile_picture
                           FROM Users u
                           LEFT JOIN User_Profile p ON u.id = p.user_id 
                           WHERE u.id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception("User not found.");
    }
    
    if ($user['role'] == "user") $first_name = $user['first_name'] ?? 'User';
    else $first_name = $user['first_name'] ?? 'Admin';
    $last_name = $user['last_name'] ?? '';
    $email = $user['email'];
    $user_role = $user['role'];
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['role'] = $user_role;
    $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'default_1.png';
} catch (Exception $e) {
    $error = "Error fetching user details.";
}

// Fetch upcoming bookings for the user
$upcoming_bookings = [];
if (true) {
    try {
        $stmt = $pdo->prepare("
            SELECT r.name AS room_name, rs.timeslot_start, rs.timeslot_end, b.status, b.room_id
            FROM Bookings b
            INNER JOIN Rooms r ON b.room_id = r.id
            INNER JOIN Room_Schedule rs ON b.schedule_id = rs.id
            WHERE b.user_id = :user_id AND rs.timeslot_end > NOW()
            ORDER BY rs.timeslot_start ASC
            LIMIT 3
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $upcoming_bookings = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Error fetching bookings.";
    }
}

include 'includes/dashboard_html.php';
?>
