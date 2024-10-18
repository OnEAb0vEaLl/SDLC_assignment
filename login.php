<?php
session_start(); // Start the session
include 'dbconnection.php'; // Include your database connection file

$login_error = ''; // Initialize login error variable

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT password FROM Users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Check if username exists
    if ($stmt->rowCount() > 0) {
        $hashed_password = $stmt->fetchColumn(); // Fetch the hashed password

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Successful login
            $_SESSION['username'] = $username; // Store username in session
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            $login_error = "Invalid password."; // Incorrect password
        }
    } else {
        $login_error = "Username not found."; // Username not found
    }

    // No need to close the PDO connection, it's closed automatically when the script ends.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Forge - Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

    <style>
        body {
            background-color: #e9f7ff; /* Light blue background */
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #007bff; /* Blue background for the header */
            color: white; /* White text color */
            padding: 15px 20px; /* Padding for the header */
            display: flex;
            align-items: center;
        }

        header .logo {
            width: 50px; /* Logo width */
            margin-right: 15px; /* Space between logo and title */
        }

        h2 {
            color: #343a40; /* Dark gray text for the heading */
            text-align: center; /* Center align heading */
            margin-top: 20px; /* Margin above heading */
        }

        .form-group {
            margin-bottom: 20px; /* Space between form groups */
        }

        .btn {
            margin: 0 10px; /* Space between buttons */
        }

        .text-danger {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <img src="https://i.pinimg.com/564x/c7/2f/8b/c72f8b1c4a54638d5816d3b105969e42.jpg" alt="Finance Forge Logo" class="logo">
        <h1 class="m-0">Finance Forge</h1>
    </header>

    <h2>Login</h2>

    <!-- Display login error message if exists -->
    <?php if ($login_error): ?>
    <div class="alert alert-danger text-center" role="alert">
        <?php echo $login_error; ?>
    </div>
    <?php endif; ?>

    <form method="POST" class="container">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <div class="container mt-3 text-center">
        <a href="index.php" class="btn btn-secondary">Back to Home</a>
    </div>
</body>
</html>
