<?php
require 'db_connection.php'; 
$error = '';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['db_error'])) {
    $error = $_SESSION['db_error'];
}

// Process GET request for room search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$rooms = [];

if ($error == '') {
    try {
        // Query to fetch rooms based on search input
        $query = "SELECT * FROM Rooms WHERE name LIKE :search";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $stmt->execute();
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "An error occurred while fetching rooms: " . $e->getMessage();
    }
}
include 'includes/browse_form.php'; 


?>




