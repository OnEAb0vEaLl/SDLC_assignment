<?php
// Include the database connection
include 'dbconnection.php'; // Ensure this is the correct path

session_start(); // Start the session

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: dashboard.php'); // Redirect to dashboard if user is not logged in
    exit(); // Exit to prevent further execution
}

// Fetch user data, transactions, and investments
$username = $_SESSION['username']; // Use 'username' instead of 'user_id'

// Initialize variables
$user = [];
$transactions = [];
$investments = [];

// Fetch user information, transactions, and investments
try {
    // Fetch user information
    $stmt = $pdo->prepare("SELECT id, username, email, contact_number FROM Users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user === false) {
        throw new Exception("User not found or query failed.");
    }

    // Fetch transactions using user_id
    $stmt = $pdo->prepare("SELECT * FROM Transactions WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user['id']]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch investments
    $stmt = $pdo->prepare("SELECT * FROM InvestmentPlan WHERE id = :user_id");
    $stmt->execute(['user_id' => $user['id']]);
    $investments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile - Finance Forge</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9f7ff;
            margin: 0;
            padding: 20px;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .logo {
            width: 50px;
            margin-right: 15px;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            color: #007bff;
        }
        .card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            margin: 5px 0;
            display: inline-block;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <header>
        <img src="https://i.pinimg.com/564x/c7/2f/8b/c72f8b1c4a54638d5816d3b105969e42.jpg" alt="Finance Forge Logo" class="logo" />
        <h1>Finance Forge</h1>
    </header>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Profile</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="currency_converter.php">Currency Converter</a>
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

    <div class="container mt-4">
        <h2>User Profile</h2>
        <div class="card">
            <div class="card-body">
                <h3 class="card-title"><?php echo htmlspecialchars($user['username']); ?></h3>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="card-text"><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['contact_number']); ?></p>
                <a href="edit_profile.php" class="btn">Edit Profile</a>
                <a href="dashboard.php" class="btn">Go to Dashboard</a>
            </div>
        </div>

        <div class="mt-4">
            <h4>Transactions</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Currency From</th>
                        <th>Currency To</th>
                        <th>Amount</th>
                        <th>Converted Amount</th>
                        <th>Conversion Rate</th>
                        <th>Transaction Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7">No transactions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['currency_from']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['currency_to']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['converted_amount']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['conversion_rate']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h4>Investments</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Investment Option</th>
                        
                        <th>Min Monthly Investment</th>
                        <th>Min Initial Investment</th>
                        <th>Predicted Returns</th>
                        <th>Fees</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($investments)): ?>
                        <tr>
                            <td colspan="6">No investments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($investments as $investment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($investment['option_name']); ?></td>

                                <td>£<?php echo htmlspecialchars($investment['min_monthly_investment']); ?></td>
                                <td>£<?php echo htmlspecialchars($investment['min_initial_investment']); ?></td>
                                <td><?php echo htmlspecialchars($investment['predicted_returns']); ?>%</td>
                                <td><?php echo htmlspecialchars($investment['fees']); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
