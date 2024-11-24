<?php
$dsn = 'mysql:host=localhost;dbname=RoomBookingSystem;charset=utf8mb4';
$username = 'root';
$password = 'mysql';

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['db_error'])) {
        unset($_SESSION['db_error']);
    }

} catch (PDOException $e) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['db_error'] = "Error: Could not connect to the database. " . $e->getMessage();
    //header("Location: login.php");
    //exit();
    //die($e->getMessage());
}


?>