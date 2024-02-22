<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales & Purchase Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        p {
            text-align: center;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>


<?php
// Create connection
$db = new mysqli("184.168.97.210", "wk8divcqwwyu", "Sualaksharma@291100", "i7715383_wp2");

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get start and end dates from the user input
$startDate = $_POST['startDate'] ?? null;
$endDate = $_POST['endDate'] ?? null;

// Validate and sanitize the dates
$startDate = $startDate ? date('Y-m-d', strtotime($startDate)) : null;
$endDate = $endDate ? date('Y-m-d', strtotime($endDate)) : null;

if ($startDate && $endDate && $startDate <= $endDate) {
    // Fetch profits and sums for each day within the date range
    $dateQuery = "SELECT date, SUM(sales) as totalProfit, SUM(sum) as totalSum
                  FROM sales_data
                  WHERE date BETWEEN '$startDate' AND '$endDate'
                  GROUP BY date
                  ORDER BY date";

    $result = $db->query($dateQuery);

    if ($result) {
        echo "<h2>Daily Sales & Purchase Report</h2>";
        echo "<p>Report for the period: $startDate to $endDate</p>";

        $totalSales = 0;
        $totalProfit = 0;

        if ($result->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Date</th><th>Total Sales</th><th>Total Purchase</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['date']}</td>";
                echo "<td>Rs. {$row['totalProfit']}</td>";
                echo "<td>Rs. {$row['totalSum']}</td>";
                echo "</tr>";

                // Add to the total
                $totalSales += floatval($row['totalProfit']);
                $totalProfit += floatval($row['totalSum']);
            }

            echo "</table>";
        } else {
            echo "No records found for the specified date range.";
        }

        echo "<p>Total Sales: Rs. " . number_format($totalSales, 2, '.', '') . "</p>";
        echo "<p>Total Purchase: Rs. " . number_format($totalProfit, 2, '.', '') . "</p>";

        $result->free();
    } else {
        echo "Error: " . $db->error;
    }
} else {
    echo "Invalid date range.";
}

// Close database connection
$db->close();
?>
</body>
</html>
