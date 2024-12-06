<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Project Homepage</title>
    <style>
    body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
    }

    h1 {
        color: #007bff;
        font-size: 2.5rem;
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    }

    nav ul {
        display: flex;
        gap: 1rem;
        list-style: none;
        padding: 0;
    }

    nav ul li {
        margin: 0;
    }

    nav ul li a {
        text-decoration: none;
        padding: 0.5rem 1rem;
        font-size: 1.2rem;
        color: #fff;
        background-color: #007bff;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    nav ul li a:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <h1>Welcome to the Booking System</h1>
    <nav>
        <ul>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
</body>

</html>