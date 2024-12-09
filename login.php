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

    try {
        $stmt = $pdo->prepare("SELECT id, email, password_hash FROM users WHERE email = :email");
        $stmt->bindparam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error = "An error occurred. Please try again later.";
    }
}


include 'includes/login_form.php';
?>
