<?php
include 'dbconnection.php'; 

$errors = [];
$success = false;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $password = trim($_POST['password']);

    // Simple validations
    if (empty($username)) {
        $errors['username'] = "Username is required.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($contact_number)) {
        $errors['contact_number'] = "Contact number is required.";
    } elseif (!preg_match('/^\d{10,15}$/', $contact_number)) {
        $errors['contact_number'] = "Contact number must be between 10 to 15 digits.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters long.";
    }

   
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); 

        try {
            // Prepare and execute the statement
            $stmt = $pdo->prepare("INSERT INTO Users (username, password, contact_number, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $contact_number, $email]);

            $success = true; // Registration successful
        } catch (PDOException $e) {
            $errors['general'] = "Registration failed: " . $e->getMessage(); 
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Forge - Register</title>
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
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <header>
        <img src="https://i.pinimg.com/564x/c7/2f/8b/c72f8b1c4a54638d5816d3b105969e42.jpg" alt="Finance Forge Logo" class="logo">
        <h1 class="m-0">Finance Forge</h1>
    </header>

    <h2>Register</h2>

   
    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
        Registration successful! You can now log in.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    
    <?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $errors['general']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <form method="POST" action="" class="container">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" class="form-control" id="username" required>
            <?php if (isset($errors['username'])): ?>
                <span class="text-danger"><?php echo $errors['username']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" id="email" required>
            <?php if (isset($errors['email'])): ?>
                <span class="text-danger"><?php echo $errors['email']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact Number:</label>
            <input type="text" name="contact_number" class="form-control" id="contact_number">
            <?php if (isset($errors['contact_number'])): ?>
                <span class="text-danger"><?php echo $errors['contact_number']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" id="password" required>
            <?php if (isset($errors['password'])): ?>
                <span class="text-danger"><?php echo $errors['password']; ?></span>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <div class="container mt-3 text-center">
        <a href="index.php" class="btn btn-secondary">Back to Home</a>
    </div>

   
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                
                alert(successAlert.innerText);
            }
        });
    </script>
</body>
</html>  
