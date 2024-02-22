<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Quantity Input</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
        }

        label, input[type="number"] {
            flex: 0 0 45%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            flex-basis: 100%;
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        h2 {
            text-align: center;
        }
        
        input[type="date"] {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

/* Style for better alignment */
label[for="manualDate"] {
    margin-top: 10px;
    display: block;
}
    </style>
</head>
<body>
    <h2>Product Name & Quantity</h2>
    <form action="process.php" method="post">
        <?php
        // Create connection
        $db = new mysqli("184.168.97.210", "wk8divcqwwyu", "Sualaksharma@291100", "i7715383_wp2");

        // Check connection
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the current date
            $currentDate = date("Y-m-d");

            // Loop through the posted data
            foreach ($_POST as $inputName => $quantity) {
                // Ensure the input is a valid quantity and not empty
                if (is_numeric($quantity) && $quantity > 0) {
                    // Get the product name from the input name
                    $productName = str_replace('_', ' ', ucfirst($inputName));

                    // Fetch profit for the current product from the database
                    $profitQuery = "SELECT profit FROM product WHERE product_name = '$productName'";
                    $profitResult = $db->query($profitQuery);

                    if ($profitResult->num_rows > 0) {
                        $profitRow = $profitResult->fetch_assoc();
                        $profit = $profitRow["profit"];

                        // Calculate the sum
                        $sum = $quantity * $profit;

                        // Insert the data into the table
                        $insertQuery = "INSERT INTO product (product_name, quantity, profit, sum, date) VALUES ('$productName', $quantity, $profit, $sum, '$currentDate')";
                        $db->query($insertQuery);
                    } else {
                        echo "Error: Could not fetch profit for product $productName.";
                    }
                }
            }
        }

        // Fetch product names from the product table
        $sql = "SELECT product_name FROM product";
        $result = $db->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_name = $row["product_name"];
                $input_name = str_replace(' ', '_', strtolower($product_name));

                echo '<label for="' . $input_name . '">' . $product_name . '</label>';
                echo '<input type="number" name="' . $input_name . '" id="' . $input_name . '">';
            }
        } else {
            echo "No products found.";
        }
        

        // Close database connection
        $db->close();
        ?>
<label for="manualDate">Manual Date:</label>
        <input type="date" name="manualDate" id="manualDate">
        <input type="submit" value="Submit">
    </form>
</body>
</html>
