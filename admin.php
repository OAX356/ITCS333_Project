<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'db_connection.php';

// Handle room management actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add_room') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $capacity = (int)$_POST['capacity'];
            $equipment = $_POST['equipment'];

            $stmt = $pdo->prepare("INSERT INTO Rooms (name, description, capacity, equipment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $description, $capacity, $equipment]);

        } elseif ($action === 'edit_room') {
            $id = (int)$_POST['room_id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $capacity = (int)$_POST['capacity'];
            $equipment = $_POST['equipment'];

            $stmt = $pdo->prepare("UPDATE Rooms SET name = ?, description = ?, capacity = ?, equipment = ? WHERE id = ?");
            $stmt->execute([$name, $description, $capacity, $equipment, $id]);

        } elseif ($action === 'delete_room') {
            $id = (int)$_POST['room_id'];

            $stmt = $pdo->prepare("DELETE FROM Rooms WHERE id = ?");
            $stmt->execute([$id]);
        }
    }
}

// Fetch all rooms
$stmt = $pdo->prepare("SELECT * FROM Rooms");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Admin Panel</h1>
    <h2>Room Management</h2>

    <form method="POST">
        <input type="hidden" name="action" value="add_room">
        <label for="name">Room Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" required>
        <label for="equipment">Equipment:</label>
        <textarea id="equipment" name="equipment" required></textarea>
        <button type="submit">Add Room</button>
    </form>

    <h3>Existing Rooms</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Capacity</th>
                <th>Equipment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['id']) ?></td>
                    <td><?= htmlspecialchars($room['name']) ?></td>
                    <td><?= htmlspecialchars($room['description']) ?></td>
                    <td><?= htmlspecialchars($room['capacity']) ?></td>
                    <td><?= htmlspecialchars($room['equipment']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="edit_room">
                            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                            <button type="submit">Edit</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_room">
                            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
