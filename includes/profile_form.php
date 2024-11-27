<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Your Profile</h1>
        <?php if (!empty($error)): ?>
            <article class="alert error" role="alert"><?php echo htmlspecialchars($error); ?></article>
        <?php elseif (!empty($success)): ?>
            <article class="alert success" role="alert"><?php echo htmlspecialchars($success); ?></article>
        <?php endif; ?>

        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name ?? ''); ?>" placeholder="Enter your first name" required>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name ?? ''); ?>" placeholder="Enter your last name" required>

            <label for="profile_picture">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

            <?php if (!empty($profile_picture)): ?>
                <p>Current Picture:</p>
                <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
            <?php endif; ?>

            <button type="submit" class="primary">Save Changes</button>
        </form>
        <footer>
            <p><a href="dashboard.php">Return to Dashboard</a></p>
        </footer>
    </main>
</body>
</html>
