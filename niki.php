<?php
include('connection.php');
session_start();
include("navigation.php");

if (!isset($_SESSION['cemail'])) {
    header("Location: login.php"); 
    exit();
}

$cemail = $_SESSION['cemail'];

// Retrieve user details
$query = "SELECT id, name, contact, address FROM customer WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $cemail);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
    $user_name = $row['name'];
    $user_phone = $row['contact'];
    $user_address = $row['address'];
} else {
    echo "Customer not found.";
    exit();
}
$stmt->close();

// Calculate total price for cart items
// Fetch cart items for the customer
$query = "SELECT i.image_url, i.name, i.price, ci.quantity, ci.id as cart_id
          FROM cart_items ci
          JOIN items i ON ci.item_id = i.id
          WHERE ci.cust_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
$cart_items = []; // Store cart items for display in the table

while ($row = $result->fetch_assoc()) {
    $item_total = $row['price'] * $row['quantity'];
    $total_price += $item_total;

    // Store item details for displaying in the table
    $cart_items[] = $row;
}

$stmt->close();

// Calculate delivery date (2 days from today)
$delivery_date = date('Y-m-d', strtotime("+2 days"));

// Set default delivery fee
$delivery_fee = 120;

// Calculate the final total price including delivery fee
$final_total = $total_price + $delivery_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #faf3e0;
        }

        .headline {
            background-color: #d4a373;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .headline h1 {
            margin: 0;
            font-size: 2rem;
        }

        .outer-container {
            background-color: #fff;
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2d1c3;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #4b3832;
        }

        input, select {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #e2d1c3;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-submit {
            padding: 12px 25px;
            background-color: #d4a373;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #c89666;
        }

        .total-container {
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
            color: #4b3832;
            margin-top: 20px;
            padding: 10px 0;
            border-top: 1px solid #e2d1c3;
        }

table {
    width: 100%;
    margin-top: 30px;
    border-collapse: collapse;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 15px;
    text-align: center;
    font-size: 1rem;
    color: #4b3832;
}

th {
    background-color: #d4a373;
    color: white;
    font-weight: bold;
    border-bottom: 2px solid #e2d1c3;
}

td {
    background-color: #fff;
    border-bottom: 1px solid #e2d1c3;
    transition: background-color 0.3s ease;
}

/* Hover effect for table rows */
tr:hover td {
    background-color: #f4e1c1;
}

/* Image Styling */
td img {
    width: 80px;
    height: auto;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Row Alternating Colors */
tbody tr:nth-child(even) td {
    background-color: #faf3e0;
}

tbody tr:nth-child(odd) td {
    background-color: #fff;
}

/* Total Price Section */
.total-cart-price {
    text-align: right;
    font-weight: bold;
    font-size: 1.2rem;
    margin-top: 20px;
    color: #4b3832;
    padding: 10px 0;
    border-top: 2px solid #e2d1c3;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

</style>
</head>
<body>
    <div class="headline">
        <h1>Checkout</h1>
    </div>
    <div class="outer-container">
        <form action="process_checkout.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>" required>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_phone); ?>" required>

            <label for="address">Shipping Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user_address); ?>" required>

            <label for="delivery_date">Estimated Delivery Date:</label>
            <input type="text" id="delivery_date" name="delivery_date" value="<?php echo htmlspecialchars($delivery_date); ?>" readonly>

            <label for="delivery_fee">Delivery Fee (Rs):</label>
            <input type="text" id="delivery_fee" name="delivery_fee" value="<?php echo htmlspecialchars($delivery_fee); ?>" readonly>

            <button type="submit" class="btn-submit">Place Order</button>
        </form>
    </div>

    <!-- Display Cart Items in a Table -->
    <h3>Your Cart Items</h3>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Item Name</th>
                <th>Price (Rs)</th>
                <th>Quantity</th>
                <th>Total (Rs)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_cart_price = 0; // Recalculate total price for cart items
            foreach ($cart_items as $item) {
                $item_total = $item['price'] * $item['quantity'];
                $total_cart_price += $item_total;

                echo '<tr>';
                echo '<td><img src="' . htmlspecialchars($item['image_url']) . '" alt="Item Image"></td>';
                echo '<td>' . htmlspecialchars($item['name']) . '</td>';
                echo '<td>Rs ' . htmlspecialchars($item['price']) . '</td>';
                echo '<td>' . htmlspecialchars($item['quantity']) . '</td>';
                echo '<td>Rs ' . htmlspecialchars($item_total). '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    <!-- Total Price Section including Delivery Fee -->
    <div class="total-cart-price">
        <p>Total Cart Price: Rs <?php echo htmlspecialchars($total_cart_price); ?></p>
        <p>Delivery Fee: Rs <?php echo htmlspecialchars($delivery_fee); ?></p>
        <p><strong>Final Total: Rs <?php echo htmlspecialchars($final_total); ?></strong></p>
    </div>

</body>
</html>