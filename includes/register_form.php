<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Register</h1>
        <?php if (!empty($error)): ?>
            <article class="alert error" role="alert"><?php echo htmlspecialchars($error); ?></article>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your UoB email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>

            <button type="submit" class="primary">Register</button>
        </form>
        <footer>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </footer>
    </main>
</body>
</html>
