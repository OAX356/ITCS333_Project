<?php
require_once 'db_connection.php'; // Include PDO connection
$error = '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['db_error'])) {
    $error = $_SESSION['db_error'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $error == '') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate UOB email
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@stu\.uob\.edu\.bh$/', $email) &&
        !preg_match('/^[a-zA-Z0-9._%+-]+@uob\.edu\.bh$/', $email)) {
        $error = "Please use a valid UoB email address.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)");
            $stmt->bindparam(':email', $email);
            $stmt->bindparam(':password_hash', $password_hash);
            $stmt->execute();
            header("Location: login.php?success=1");
            exit();
        } catch (PDOException $e) {
            $error = ($e->getCode() == 23000) ? "Email already exists." : "An error occurred. Please try again.";
        }
    }
}
include 'includes/register_form.php';
?>