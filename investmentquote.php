<?php
// Database connection
$host = 'localhost'; // Change this if your database is hosted elsewhere
$db = 'FinanceForge';
$user = 'root'; // Change this to your database username
$pass = ''; // Change this to your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $min_initial_investment = $_POST['min_initial_investment'];
    $min_monthly_investment = $_POST['min_monthly_investment'];
    $option_name = $_POST['option_name'];
    $predicted_returns = $_POST['predicted_returns'];
    $fees = $_POST['fees'];

    // Insert or update investment data
    if ($id) {
        // Update existing investment
        $stmt = $pdo->prepare("UPDATE InvestmentPlan SET min_initial_investment = ?, min_monthly_investment = ?, option_name = ?, predicted_returns = ?, fees = ? WHERE id = ?");
        $stmt->execute([$min_initial_investment, $min_monthly_investment, $option_name, $predicted_returns, $fees, $id]);
    } else {
        // Insert new investment
        $stmt = $pdo->prepare("INSERT INTO InvestmentPlan (min_initial_investment, min_monthly_investment, option_name, predicted_returns, fees) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$min_initial_investment, $min_monthly_investment, $option_name, $predicted_returns, $fees]);
    }

    // Redirect to dashboard or display a success message
    header("Location: dashboard.php"); // Change to the appropriate dashboard file
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savings and Investments</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        /* General styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        .main-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        h1 {
            color: #333;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input, select, button {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .quote-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f8f8;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
        }

        .back-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Savings and Investments</h1>
    </header>

    <div class="main-content">
        <form id="investment-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="id">Investment ID (for update):</label>
            <input type="number" name="id" id="id" placeholder="Enter investment ID (optional)" min="0">

            <label for="initial-lump-sum">Initial Lump Sum to be Invested:</label>
            <input type="number" name="min_initial_investment" id="initial-lump-sum" placeholder="Enter amount" required min="0" step="0.01">

            <label for="monthly-amount">Monthly Amount to be Invested:</label>
            <input type="number" name="min_monthly_investment" id="monthly-amount" placeholder="Enter amount" required min="0" step="0.01">

            <label for="investment-type">Type of Investment:</label>
            <select name="option_name" id="investment-type" required>
                <option value="Basic Savings Plan">Option 1 – Basic Savings Plan</option>
                <option value="High Yield Savings Plan">Option 2 – High Yield Savings Plan</option>
                <option value="Risk Managed Investment Plan">Option 3 – Risk Managed Investment Plan</option>
                <option value="Equity Investment Plan">Option 4 – Equity Investment Plan</option>
            </select>

            <button type="button" class="get-quote" onclick="getQuote()">Get Quote</button>
            <button type="submit" class="submit-button">Submit</button>
        </form>

        <div class="quote-section">
            <h3>Personalized Investment Quote</h3>
            <p id="quote-details">Your quote will appear here after getting a quote.</p>
        </div>

        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>

    <script>
        const investmentData = {};

        function getQuote() {
            const initialLumpSum = parseFloat(document.getElementById('initial-lump-sum').value);
            const monthlyAmount = parseFloat(document.getElementById('monthly-amount').value);
            const investmentType = document.getElementById('investment-type').value;

            // Ensure both amounts are positive
            if (isNaN(initialLumpSum) || initialLumpSum < 0 || isNaN(monthlyAmount) || monthlyAmount < 0) {
                alert("Please enter positive amounts for both investments.");
                return;
            }

            let maxReturn, minReturn, totalFees;

            switch (investmentType) {
                case 'Basic Savings Plan':
                    maxReturn = (initialLumpSum + (monthlyAmount * 12)) * 0.024;
                    minReturn = (initialLumpSum + (monthlyAmount * 12)) * 0.012;
                    totalFees = 0.25; 
                    break;
                case 'High Yield Savings Plan':
                    maxReturn = (initialLumpSum + (monthlyAmount * 12)) * 0.035;
                    minReturn = (initialLumpSum + (monthlyAmount * 12)) * 0.025;
                    totalFees = 0.50; 
                    break;
                case 'Risk Managed Investment Plan':
                    maxReturn = (initialLumpSum + (monthlyAmount * 12)) * 0.06;
                    minReturn = (initialLumpSum + (monthlyAmount * 12)) * 0.04;
                    totalFees = 0.75; 
                    break;
                case 'Equity Investment Plan':
                    maxReturn = (initialLumpSum + (monthlyAmount * 12)) * 0.10;
                    minReturn = (initialLumpSum + (monthlyAmount * 12)) * 0.06;
                    totalFees = 1.00; 
                    break;
                default:
                    maxReturn = minReturn = totalFees = 0;
            }

            // Store investment data for submission
            investmentData.option_name = investmentType;
            investmentData.max_investment = initialLumpSum;
            investmentData.min_monthly_investment = monthlyAmount;
            investmentData.min_initial_investment = initialLumpSum;
            investmentData.predicted_returns = `£${((maxReturn + minReturn) / 2).toFixed(2)}`;
            investmentData.fees = totalFees;

            // Construct the quote details
            const quoteDetails = `
                <strong>Investment Amount:</strong> £${initialLumpSum.toFixed(2)}<br>
                <strong>Monthly Investment:</strong> £${monthlyAmount.toFixed(2)}<br>
                <strong>Predicted Returns:</strong> £${investmentData.predicted_returns}<br>
                <strong>Total Fees:</strong> £${totalFees.toFixed(2)}<br>
            `;

            // Display quote details
            document.getElementById('quote-details').innerHTML = quoteDetails;
        }

        // Add hidden inputs to the form for submission
        document.getElementById('investment-form').addEventListener('submit', function(event) {
            // Ensure there's investment data before submitting
            if (!investmentData.predicted_returns) {
                alert("Please get a quote before submitting the form.");
                event.preventDefault(); // Prevent form submission
                return;
            }

            const hiddenReturnsInput = document.createElement('input');
            hiddenReturnsInput.type = 'hidden';
            hiddenReturnsInput.name = 'predicted_returns';
            hiddenReturnsInput.value = investmentData.predicted_returns;
            this.appendChild(hiddenReturnsInput);

            const hiddenFeesInput = document.createElement('input');
            hiddenFeesInput.type = 'hidden';
            hiddenFeesInput.name = 'fees';
            hiddenFeesInput.value = investmentData.fees; // Ensure 'totalFees' is accessible here
            this.appendChild(hiddenFeesInput);
        });
    </script>
</body>
</html>
