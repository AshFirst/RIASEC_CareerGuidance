<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'logs');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $grade = $_POST['grade'];

    // Validate username
    if (!preg_match('/^[A-Z][a-zA-Z0-9]*$/', $name)) {
        echo "Username must start with a capital letter and contain only letters and numbers.";
        exit;
    }

    // Validate password
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the user already exists
    $sql = "SELECT * FROM users WHERE username = '$name'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        // Insert new user into the database
        $sql = "INSERT INTO users (username, password, grade) VALUES ('$name', '$hashed_password', '$grade')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "User already exists! Please login.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="log.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <form method="POST" action="signup.php">
            <label for="name">Username</label>
            <input type="text" id="name" name="name" pattern="[A-Z][a-zA-Z0-9]*" title="Username must start with a capital letter." required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}" title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character." required>

            <label for="grade">Grade</label>
            <input type="text" id="grade" name="grade" required>

            <button type="submit">Sign Up</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</body>
</html>
