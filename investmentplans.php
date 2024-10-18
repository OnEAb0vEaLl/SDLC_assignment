<?php

require_once 'dbconnection.php';

try {
    
    $query = "SELECT * FROM InvestmentQuote";
    $stmt = $pdo->prepare($query); 
    $stmt->execute(); 
    $investmentPlans = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
   
    error_log("Query failed: " . $e->getMessage()); 
    die("Query failed: " . $e->getMessage()); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Plans - Finance Forge</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
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
        .nav-link {
            color: white; 
            transition: background-color 0.3s; 

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1); 
            text-decoration: none; 
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
        }
        h2 {
            color: #007bff; /* Primary color */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
        .back-button {
            display: block;
            margin: 20px auto;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            width: 160px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #0056b3; /* Darker shade on hover */
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
        <a class="navbar-brand" href="#">Investment Plans</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto"> <!-- Align navbar items to the right -->
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
    <h2>Investment Plans</h2>
    <div class="main-content">
        <table class="table table-bordered">
            <caption>Investment Plans Overview</caption>
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($investmentPlans as $plan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($plan['option_name']); ?></td>
                        <td>
                            Maximum investment per year: <?php echo htmlspecialchars($plan['max_investment_year']); ?><br>
                            Minimum monthly investment: <?php echo htmlspecialchars($plan['min_monthly_investment']); ?><br>
                            Minimum initial investment lump sum: <?php echo htmlspecialchars($plan['min_initial_investment']); ?><br>
                            Predicted returns per year: <?php echo htmlspecialchars($plan['predicted_returns']); ?><br>
                            Estimated tax: <?php echo htmlspecialchars($plan['estimated_tax']); ?><br>
                            RBSX group fees per month: <?php echo htmlspecialchars($plan['group_fees']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <a href="dashboard.php" class="back-button">Back to dashboard</a>
</div>

</body>
</html>

<?php

?>
