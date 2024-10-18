<?php
$servername = getenv('DB_SERVER') ?: 'localhost'; // Your database server name
$username = getenv('DB_USERNAME') ?: 'root'; // Your database username
$password = getenv('DB_PASSWORD') ?: ''; // Your database password
$dbname = getenv('DB_NAME') ?: 'FinanceForge'; // Your database name

// Create a PDO instance
try {
    // DSN (Data Source Name)
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4"; // Use charset to avoid issues with special characters
    $pdo = new PDO($dsn, $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Debug statement: You can remove this in production
    // echo "Connected successfully to the database."; 
} catch (PDOException $e) {
    // Handle connection error
    error_log("Connection failed: " . $e->getMessage()); // Log the error for debugging
    die("Connection failed. Please try again later."); // Display a user-friendly message
}
?>
