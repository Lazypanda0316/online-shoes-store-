<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION["username"]) || $_SESSION["username"] == "") {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

// Connect to the database
include("conn.php");

// Fetch user id from the database (assuming there's a `users` table)
$query = "SELECT id, username, email, contact, address FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

// Error handling for the query
if (!$result) {
    die("Error fetching user data: " . mysqli_error($conn));
}

$user_data = mysqli_fetch_assoc($result);
if (!$user_data) {
    die("User not found.");
}

$user_id = $user_data['id']; // Get the logged-in user's ID
$user_name = $user_data['username'];
$user_email = $user_data['email'];
$user_contact = $user_data['contact'];
$user_address = $user_data['address'];

// Fetch order history from the database for the logged-in user
$order_query = "SELECT o.order_id, o.date, o.total, o.payment_method, o.delivery_fee, o.delivery_date 
                FROM orders o
                WHERE o.username = '$username'
                ORDER BY o.date DESC";
$order_result = mysqli_query($conn, $order_query);

// Error handling for the order query
if (!$order_result) {
    die("Error fetching order history: " . mysqli_error($conn));
}

$orders = mysqli_fetch_all($order_result, MYSQLI_ASSOC); // Fetch all orders
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #22336c, #3e3c3c);
            color: #FFFFFF;
        }
        .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 20px;
            text-align: center;
        }
        .dashboard-header {
            padding-top: 50px;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #fff;
        }
        .section {
            background-color: #333;
            padding: 20px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .section h3 {
            margin-top: 0;
            font-size: 1.5rem;
            text-align: center;
            color: #fff;
        }
        .section p {
            font-size: 1.1rem;
            color: #ddd;
        }
        .user-details p {
            margin: 10px 0;
        }
        .order-history table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            color: #fff;
        }
        .order-history th, .order-history td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        .order-history th {
            background-color: #444;
        }
        .order-history tr:hover {
            background-color: #555;
        }
        .order-history .empty {
            text-align: center;
            padding: 20px;
            color: #ddd;
        }
    </style>
</head>
<body>
    <!-- Header and Navigation -->
    <?php include "navigation.php"; ?>

    <!-- Dashboard Section -->
    <div class="container">
        <h2 class="dashboard-header">Welcome to Your Dashboard, <?php echo $username; ?>!</h2>
        
        <!-- User Details Section -->
        <div class="section user-details">
            <h3>Your Details</h3>
            <p><strong>Username:</strong> <?php echo $user_name; ?></p>
            <p><strong>Email:</strong> <?php echo $user_email; ?></p>
            <p><strong>Phone:</strong> <?php echo $user_contact; ?></p>
            <p><strong>Address:</strong> <?php echo $user_address; ?></p>
        </div>

        <!-- Order History Section -->
        <div class="section order-history">
            <h3>Your Recent Orders</h3>
            <?php if (!empty($orders)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment Method</th>
                            <th>Delivery Fee</th>
                            <th>Delivery Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): 
                            // Format order date and delivery date
                            $order_date = date("F j, Y", strtotime($order['date']));
                            $delivery_date = !empty($order['delivery_date']) ? date("F j, Y", strtotime($order['delivery_date'])) : "Not set"; 
                        ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order_date; ?></td>
                            <td>RS. <?php echo number_format($order['total'], 2); ?></td>
                            <td><?php echo $order['payment_method']; ?></td>
                            <td>RS. <?php echo number_format($order['delivery_fee'], 2); ?></td>
                            <td><?php echo $delivery_date; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty">You haven't placed any orders yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
