<?php
$dsn = 'mysql:host=localhost;dbname=room_booking;charset=utf8mb4';
$username = 'root';
$password = 'mysql';

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    session_start();
    $_SESSION['db_error'] = "Error: Could not connect to the database. "; // . $e->getMessage();
    //header("Location: login.php");
    //exit();
}

session_start();
if (isset($_SESSION['db_error'])) {
    unset($_SESSION['db_error']);
}

?>