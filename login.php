<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'logs');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Check if the user exists
    $sql = "SELECT * FROM users WHERE username = '$name'";
    $result = $conn->query($sql);

    if (!$result) {
        // Output any SQL errors
        echo "Error: " . $conn->error;
        exit;
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Save user ID in session after successful login
            $_SESSION['id'] = $row['id'];

            // Redirect to the homepage or quiz page after login
            header("Location: index.html");
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User does not exist!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="log.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="">
            <label for="name">Username</label>
            <input type="text" id="name" name="name" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
