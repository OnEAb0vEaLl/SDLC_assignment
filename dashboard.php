<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'dbconnection.php'; // Ensure this is the correct path

// Initialize variables
$transactions = []; // Initialize to an empty array
$investment_value = 0; // Initialize investment value
$new_client_count = 0; // Initialize new client count

// Fetch transactions and other data
try {
    // Fetch all transactions
    $stmt = $pdo->query("SELECT * FROM Transactions"); // Use correct table name
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total investment value (assuming you want total transaction amount)
    $investmentValueStmt = $pdo->query("SELECT SUM(amount) as total FROM Transactions"); // Querying Transactions for total amount
    $investment_value = $investmentValueStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0; // Get total investment value

    // Count new clients in the last month
    $newClientCountStmt = $pdo->query("SELECT COUNT(*) as count FROM Users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
    $new_client_count = $newClientCountStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0; // Count new clients
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Forge - Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e9f7ff; /* Light blue background */
            font-family: Arial, sans-serif; 
        }
        header {
            background-color: #007bff; /* Primary blue color */
            color: white; 
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Aligns logo and title to the left */
        }
        .logo {
            width: 50px; 
            margin-right: 15px; 
        }
        h1 {
            margin: 0; 
            font-size: 24px; 
        }
        h2, h3, h4 {
            color: #343a40; 
        }
        .nav-link {
            color: white; 
            transition: background-color 0.3s; /* Smooth transition for hover effect */
        }
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Lighten background on hover */
            text-decoration: none; 
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Add shadow to navbar */
        }
        .stat-card {
            background-color: #ffffff; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
            padding: 20px; 
            text-align: center; 
            transition: box-shadow 0.3s ease; /* Adds transition effect on hover */
        }
        .stat-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Enhances shadow on hover */
        }
        table {
            margin-top: 20px; 
            background-color: #ffffff; /* White background for the table */
            border-radius: 10px; 
            overflow: hidden; /* Ensures border radius is applied */
        }
        th {
            background-color: #007bff; /* Table header background */
            color: white; 
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2; /* Light gray for odd rows */
        }
    </style>
</head>
<body>
    <header>
        <img src="https://i.pinimg.com/564x/c7/2f/8b/c72f8b1c4a54638d5816d3b105969e42.jpg" alt="Finance Forge Logo" class="logo">
        <h1>Finance Forge</h1>
    </header>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto"> <!-- Align navbar items to the right -->
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="currency_converter.php">Currency Converter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investmentquote.php">Investment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investmentplans.php">Investment Plans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Welcome to the Finance Forge Dashboard!</h2>
        <p>Manage your clients, track transactions, and review investment opportunities all in one place.</p>
        
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <h4>Total Transactions</h4>
                    <h2><?php echo count($transactions); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h4>Total Investment Value</h4>
                    <h2>$<?php echo number_format($investment_value, 2); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h4>New Clients</h4>
                    <h2><?php echo $new_client_count; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h4>Market Trends</h4>
                    <h2>
                        <?php
                            // Generate a random percentage between -5 and +5
                            $randomTrend = rand(-5, 5) / 10; // to get values like -0.5 to +0.5
                            echo ($randomTrend >= 0 ? '+' : '') . number_format($randomTrend, 1) . '%'; // Format to one decimal place
                        ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h3>Your Transactions</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Transaction Type</th> <!-- Add Transaction Type -->
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td> <!-- Display Transaction Type -->
                        <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Investment Submission Form -->
        <div class="mt-4">
            <h3>Submit Investment</h3>
            <form action="investmentquote.php" method="POST"> <!-- Change the action to your investment quote handling file -->
                <div class="form-group">
                    <label for="investmentAmount">Investment Amount</label>
                    <input type="number" class="form-control" id="investmentAmount" name="investment_amount" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit Investment</button>
            </form>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
