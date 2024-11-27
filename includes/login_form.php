<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Login</h1>
        
        <?php if (isset($_GET['logout'])): ?>
            <article class="alert success" role="alert">You have successfully logged out.</article>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <article class="alert success" role="alert">Registration successful! You can now log in.</article>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <article class="alert error" role="alert"><?php echo htmlspecialchars($error); ?></article>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit" class="primary">Login</button>
        </form>
        <footer>
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </footer>
    </main>
</body>
</html>
