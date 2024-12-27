<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["username"]) || $_SESSION["username"] === "") {
    header("location: login.php");
    exit();
}

$username = $_SESSION["username"];

// Include necessary files
include("conn.php"); // Include the database connection
include("functions.php"); // Include functions for cart operations

// Step 1: Get cart items from the database
$cart = getCartFromDatabase($conn, $username); // Function is now defined in functions.php

// Step 2: Fetch user details from the database
$query = "SELECT id, username, contact, address FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists in the database
if ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
    $user_name = $row['username'];
    $user_phone = $row['contact'];
    $user_address = $row['address'];
} else {
    echo "Customer not found.";
    exit();
}
$stmt->close();

// Step 3: Calculate the total amount from the cart
$total = 0;
foreach ($cart as $item) {
    $item_total = $item['price'] * $item['quantity']; // Calculate the total for each item
    $total += $item_total; // Add item total to the overall total
}

// Step 4: Add fixed delivery fee of Rs. 200
$delivery_fee = 200;

// Step 5: Calculate the delivery date (5 days from today)
$delivery_date = date('Y-m-d', strtotime('+5 days'));

// Step 6: Handle checkout form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['checkout'])) {
    // Check if user is logged in again (security check)
    if (!isset($_SESSION["username"]) || $_SESSION["username"] === "") {
        $error_message = "You need to be logged in to proceed with the checkout.";
    } else {
        // Process the order
        $date = date('Y-m-d');
        $address = $_POST['address'];
        $contact_no = $_POST['contact_no'];
        $payment_method = $_POST['payment_method'];

        // Ensure the payment method is selected
        if (empty($payment_method)) {
            $error_message = "Please select a payment method.";
        } else {
            // Insert the order into the 'orders' table
            $stmt = $conn->prepare("INSERT INTO orders (username, address, contact_no, total, date, payment_method, delivery_fee, delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsdsss", $_SESSION["username"], $address, $contact_no, $total, $date, $payment_method, $delivery_fee, $delivery_date);

            if ($stmt->execute()) {
                // Get the inserted order ID
                $order_id = $conn->insert_id;

                // Insert each cart item into the 'order_items' table
                foreach ($cart as $item) {
                    $item_id = $item['product_id'];
                    $quantity = $item['quantity'];
                    $price = $item['price'];

                    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $item_stmt->bind_param("iiid", $order_id, $item_id, $quantity, $price);
                    $item_stmt->execute();
                }

                // Clear the cart after the order is placed
                clearCart($conn, $user_id);

                // Redirect to a success page based on payment method
                if ($payment_method == 'cod') {
                    // Redirect to dashboard for cash on delivery
                    header("Location: dashboard.php");
                } else {
                    // Redirect to payment page for ESEWA
                    header("Location: esewa.php");
                }
                exit();
            } else {
                $error_message = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #22336c, #3e3c3c);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            color: #fff;
        }

        .checkout-form {
            background: rgba(34, 51, 108, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 800px;
            color: #fff;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
            background-color: #f9f9f9;
            color: rgb(0, 0, 0);
        }

        .payment-methods {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .payment-card {
            background-color: #fff;
            color: #333;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 150px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .payment-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .payment-card img {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }

        .selected {
            border: 2px solid #4CAF50;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 14px 24px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            text-align: center;
            font-weight: 600;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #444;
        }
    </style>
</head>
<body>

    <div class="checkout-form">
        <h2>Customer Details</h2>

        <!-- Display error message if any -->
        <?php if (isset($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>

        <form action="checkout.php" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user_name) ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="4" required><?= htmlspecialchars($user_address) ?></textarea>
            </div>
            <div class="form-group">
                <label for="contact_no">Contact Number</label>
                <input type="tel" id="contact_no" name="contact_no" value="<?= htmlspecialchars($user_phone) ?>" required>
            </div>

            <!-- Display static data for delivery fee and date -->
            <div class="form-group">
                <label>Delivery Fee (Rs.)</label>
                <input type="text" value="200" readonly>
            </div>
            <div class="form-group">
                <label>Estimated Delivery Date</label>
                <input type="text" value="<?= $delivery_date ?>" readonly>
            </div>

            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <div class="payment-methods">
                    <div class="payment-card" id="cod" onclick="selectPaymentMethod('cod')">
                        <img src="images/COD1.png" alt="Cash on Delivery">
                        <p>Cash on Delivery</p>
                    </div>
                    <div class="payment-card" id="Esewa" onclick="selectPaymentMethod('Esewa')">
                        <img src="images/esewa1.png" alt="ESEWA">
                        <p>ESEWA</p>
                    </div>
                </div>
            </div>

            <button type="submit" name="checkout">Place Order</button>
            <input type="hidden" id="payment_method" name="payment_method" value="">
        </form>

        <h3>Your Cart</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalAmount = 0; // Initialize the total amount
                foreach ($cart as $item) {
                    $item_total = $item['price'] * $item['quantity']; // Calculate the total for each item
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['description']) . "</td>";
                    echo "<td>" . number_format($item['price'], 2) . "</td>";
                    echo "<td>" . $item['quantity'] . "</td>";
                    echo "<td>" . number_format($item_total, 2) . "</td>";
                    echo "</tr>";

                    $totalAmount += $item_total; // Add item total to overall total
                }
                ?>
            </tbody>
        </table>

        <p>Total Amount: <?= number_format($totalAmount + $delivery_fee, 2) ?></p>
    </div>

    <script>
        // Function to select the payment method
        function selectPaymentMethod(method) {
            // Remove selected class from both payment cards
            document.getElementById('cod').classList.remove('selected');
            document.getElementById('Esewa').classList.remove('selected');

            // Add selected class to the clicked payment card
            document.getElementById(method).classList.add('selected');

            // Set the payment method in the hidden input field
            document.getElementById('payment_method').value = method;
        }
    </script>

</body>
</html>
