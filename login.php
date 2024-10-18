<?php
session_start(); 
include 'dbconnection.php'; 

$login_error = ''; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    
    $stmt = $pdo->prepare("SELECT password FROM Users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    
    if ($stmt->rowCount() > 0) {
        $hashed_password = $stmt->fetchColumn(); 

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Successful login
            $_SESSION['username'] = $username; 
            header("Location: dashboard.php"); 
            exit();
        } else {
            $login_error = "Invalid password."; 
        }
    } else {
        $login_error = "Username not found."; 
    }

   
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
            background-color: #e9f7ff; 
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #007bff; 
            color: white; 
            padding: 15px 20px; 
            display: flex;
            align-items: center;
        }

        header .logo {
            width: 50px; 
            margin-right: 15px; 
        }

        h2 {
            color: #343a40; 
            text-align: center; 
            margin-top: 20px; 
        }

        .form-group {
            margin-bottom: 20px; 
        }

        .btn {
            margin: 0 10px;
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
