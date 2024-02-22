<?php
// Create connection
$db = new mysqli("184.168.97.210", "wk8divcqwwyu", "Sualaksharma@291100", "i7715383_wp2");

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the current date or use the manually entered date
$manualDate = $_POST['manualDate'] ?? null;
$currentDate = $manualDate ? date('Y-m-d', strtotime($manualDate)) : date('Y-m-d');

$recordsAdded = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Loop through the posted data
    foreach ($_POST as $inputName => $quantity) {
        // Ensure the input is a valid quantity and not empty
        if (is_numeric($quantity) && $quantity > 0) {
            // Get the product name from the input name
            $productName = str_replace('_', ' ', ucfirst($inputName));

            // Fetch product details including profit and MRP
            $productQuery = "SELECT profit, mrp FROM product WHERE product_name = '$productName'";
            $productResult = $db->query($productQuery);

            if ($productResult->num_rows > 0) {
                $productRow = $productResult->fetch_assoc();
                $profit = $productRow["profit"];
                $mrp = $productRow["mrp"];

                // Calculate the sum and sales
                $sum = $quantity * $profit;
                $sales = $quantity * $mrp;

                // Insert the data into the table
                $insertQuery = "INSERT INTO sales_data (product_name, quantity, profit, mrp, sum, sales, date) VALUES ('$productName', $quantity, $profit, $mrp, $sum, $sales, '$currentDate')";
                $db->query($insertQuery);

                if ($db->error) {
                    die("Insertion error: " . $db->error);
                } else {
                    $recordsAdded = true;
                }
            } else {
                echo "Error: Could not fetch product details for product $productName.";
            }
        }
    }
}

// Display message if records were added successfully
if ($recordsAdded) {
    echo "<center><h1>Records added successfully!</h1></center>";
}

// Close database connection
$db->close();
?>
