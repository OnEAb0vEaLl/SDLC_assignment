<?php

include 'dbconnection.php';


$result_message = '';
$transactions = [];


function getConversionRates($baseCurrency) {
    $apiKey = 'YOUR_API_KEY'; 
    $url = "https://api.exchangerate-api.com/v4/latest/$baseCurrency"; /

    $response = file_get_contents($url);
    if ($response === FALSE) {
        die('Error occurred while fetching conversion rates.');
    }

    return json_decode($response, true)['rates'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currency_from = $_POST['currency_from'];
    $currency_to = $_POST['currency_to'];
    $amount = $_POST['amount'];

  
    $conversion_rates = getConversionRates($currency_from);

    
    if (isset($conversion_rates[$currency_to])) {
        $converted_amount = $amount * $conversion_rates[$currency_to];
        $conversion_rate = $conversion_rates[$currency_to];
        $result_message = "$amount $currency_from = " . number_format($converted_amount, 2) . " $currency_to (Rate: $conversion_rate)";

        
        $user_id = 1; 
        $transaction_type = 'Conversion'; 

        
        $stmt = $pdo->prepare("INSERT INTO Transactions (currency_from, currency_to, amount, converted_amount, conversion_rate, user_id, transaction_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$currency_from, $currency_to, $amount, $converted_amount, $conversion_rate, $user_id, $transaction_type]);
    } else {
        $result_message = "Conversion rate not available.";
    }
}


try {
    $stmt = $pdo->query("SELECT * FROM Transactions ORDER BY transaction_date DESC");
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching transactions: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Converter - Finance Forge</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 15px 0;
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

        h2 {
            color: #343a40;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .result {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            margin-top: 20px;
        }

        .table {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            background-color: #343a40;
        }

        .nav-link {
            color: white;
        }

        .nav-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <img src="https://i.pinimg.com/564x/c7/2f/8b/c72f8b1c4a54638d5816d3b105969e42.jpg" alt="Finance Forge Logo" class="logo">
        <h1>Finance Forge</h1>
    </header>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Currency Converter</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investmentquote.php">Investment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investmentplans.php">Investment Plans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Currency Converter</h2>
        <form method="POST">
            <div class="form-group">
                <label for="currency_from">From Currency:</label>
                <select name="currency_from" id="currency_from" class="form-control" required>
                    <option value="GBP">GBP (£)</option>
                    <option value="USD">USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="BRL">BRL (R$)</option>
                    <option value="JPY">JPY (¥)</option>
                    <option value="TRY">TRY (₺)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="currency_to">To Currency:</label>
                <select name="currency_to" id="currency_to" class="form-control" required>
                    <option value="GBP">GBP (£)</option>
                    <option value="USD">USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="BRL">BRL (R$)</option>
                    <option value="JPY">JPY (¥)</option>
                    <option value="TRY">TRY (₺)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" name="amount" id="amount" class="form-control" required min="0" step="0.01">
            </div>

            <button type="submit" class="btn btn-primary">Convert</button>
        </form>

        <div class="result mt-3">
            <h4>Conversion Result:</h4>
            <p><?php echo $result_message; ?></p>
        </div>

        <h2>Transaction History</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Amount</th>
                    <th>Converted Amount</th>
                    <th>Conversion Rate</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['currency_from']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['currency_to']); ?></td>
                    <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                    <td>$<?php echo number_format($transaction['converted_amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($transaction['conversion_rate']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-3">
            <a href="dashboard.php" class="btn btn-secondary">Go to Dashboard</a>
        </div>
    </div>
</body>
</html>
