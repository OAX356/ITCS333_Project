<?php
require 'db_connection.php';
$error = '';
$success = '';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile data
try {
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_picture FROM User_Profile WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $profile = $stmt->fetch();

    $first_name = $profile['first_name'] ?? '';
    $last_name = $profile['last_name'] ?? '';
    $profile_picture = $profile['profile_picture'] ?? '';
} catch (PDOException $e) {
    $error = "Could not load profile data.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    
    $file_upload = isset($_FILES['profile_picture']);
    $file_found = $file_upload ? is_uploaded_file($_FILES['profile_picture']['tmp_name']) : false;

    // Handle profile picture upload
    if ($file_found) {
        $error = $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK ? "No file was uploaded." : $error;
    }
    
    if ($file_found && empty($error)) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_picture']['type'];

        if (!in_array($file_type, $allowed_types)) {
            $error = "Only JPG, PNG, and GIF files are allowed.";
        }
    }

    if ($file_found && empty($error)) {
        $max_size = 5 * 1024 * 1024; // 5 MB in bytes
        if ($_FILES['profile_picture']['size'] > $max_size) {
            $error = "File size cannot exceed 5 MB.";
        }
    }

    if ($file_found && empty($error)) {
        $target_dir = 'uploads/';

        // Sanitize filename
        $original_filename = htmlspecialchars(basename($_FILES["profile_picture"]["name"]), ENT_QUOTES, 'UTF-8');
        $file_ext = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

        // Generate a unique name and check if it already exists
        do {
            $unique_name = uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $unique_name;
        } while (file_exists($target_file));

        // Attempt to upload file
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            // Set appropriate permissions
            chmod($target_file, 0644);

            $profile_picture = $unique_name;
        } else {
            $error = "Failed to upload profile picture.";
        }
    }

    if (empty($error)) {
        try {
            // Update profile or insert new one
            $stmt = $pdo->prepare("INSERT INTO User_Profile (user_id, first_name, last_name, profile_picture) 
                VALUES (:user_id, :first_name, :last_name, :profile_picture)
                ON DUPLICATE KEY UPDATE first_name = :first_name, last_name = :last_name, profile_picture = :profile_picture");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':profile_picture', $profile_picture);
            $stmt->execute();
            $success = "Profile updated successfully.";
        } catch (PDOException $e) {
            $error = "Failed to update profile.";
        }
    }
}

include 'includes/profile_form.php';
?>