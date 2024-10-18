<?php
$servername = getenv('DB_SERVER') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'root'; 
$password = getenv('DB_PASSWORD') ?: ''; 
$dbname = getenv('DB_NAME') ?: 'FinanceForge'; 


try {
    
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4"; 
    $pdo = new PDO($dsn, $username, $password);
    
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    
} catch (PDOException $e) {
    
    error_log("Connection failed: " . $e->getMessage()); 
    die("Connection failed. Please try again later."); 
}
?>
