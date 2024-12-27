<?php
session_start(); // Start the session to access session variables

// Ensure the admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; // Admin's username from session
$role = $_SESSION['role']; // Admin's role (either 'admin' or 'super_admin')

// Connect to the database
include("conn.php");

// Fetch all users' details
$user_query = "SELECT id, username, email, contact, address FROM users";
$user_result = mysqli_query($conn, $user_query);

// Error handling for the user query
if (!$user_result) {
    die("Error fetching user data: " . mysqli_error($conn));
}

// Fetch all orders along with their details
$order_query = "SELECT o.order_id, o.username, o.address, o.contact_no, o.total, o.payment_method, o.delivery_fee, o.delivery_date, o.date
                FROM orders o
                ORDER BY o.date DESC";

// Fetch orders data
$order_result = mysqli_query($conn, $order_query);

// Error handling for the order query
if (!$order_result) {
    die("Error fetching orders: " . mysqli_error($conn));
}

$orders = mysqli_fetch_all($order_result, MYSQLI_ASSOC); // Fetch all orders

// Handle "Accept" request (to move to accepted_orders table)
if (isset($_GET['accept_id'])) {
    $user_id = $_GET['accept_id'];
    
    // Fetch user details
    $user_query = "SELECT * FROM users WHERE id = $user_id";
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);

    // Check if user exists
    if ($user) {
        // Insert the user data into accepted_orders table
        $insert_query = "INSERT INTO accepted_orders (order_id, username, address, contact_no, total, payment_method, delivery_fee, delivery_date, date)
                         SELECT order_id, username, address, contact_no, total, payment_method, delivery_fee, delivery_date, date
                         FROM orders WHERE username = '{$user['username']}'";
        if (mysqli_query($conn, $insert_query)) {
            // If the insertion was successful, delete the order from the orders table
            $delete_query = "DELETE FROM orders WHERE username = '{$user['username']}'";
            mysqli_query($conn, $delete_query);

            // Show a success message and redirect back to admin dashboard
            echo "<script>
                    alert('Order Accepted!');
                    window.location = 'admin_dashboard.php'; // Redirect back to dashboard
                </script>";
        } else {
            echo "<script>
                    alert('Error accepting order');
                    window.location = 'admin_dashboard.php'; // Redirect back to dashboard
                </script>";
        }
    } else {
        echo "<script>
                alert('User not found');
                window.location = 'admin_dashboard.php'; // Redirect back to dashboard
            </script>";
    }
    exit();
}

// Fetch accepted orders to display in a separate section
$accepted_order_query = "SELECT * FROM accepted_orders ORDER BY date DESC";
$accepted_orders_result = mysqli_query($conn, $accepted_order_query);
$accepted_orders = mysqli_fetch_all($accepted_orders_result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Styles for the dashboard */
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
        .user-list table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            color: #fff;
        }
        .user-list th, .user-list td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        .user-list th {
            background-color: #444;
        }
        .user-list tr:hover {
            background-color: #555;
        }
        .user-list .empty {
            text-align: center;
            padding: 20px;
            color: #ddd;
        }

        /* Styles for Accepted Orders */
        .accepted-orders-container {
            margin-top: 30px;
            background-color: #444;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .accepted-orders-container table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            color: #fff;
        }
        .accepted-orders-container th, .accepted-orders-container td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #555;
        }
        .accepted-orders-container th {
            background-color: #555;
        }
        .accepted-orders-container tr:hover {
            background-color: #666;
        }

        /* Styles for Order Table */
        .order-table-container {
            display: flex;
            justify-content: center;  /* Center the table horizontally */
            width: 100%;
        }

        .order-table {
            width: 80%; /* Adjust width to be responsive */
            margin-top: 20px;
            border-collapse: collapse;
            color: #fff;
            display: none; /* Initially hidden */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);  /* Add a subtle shadow */
        }

        .order-table th, .order-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #666;
        }

        .order-table th {
            background-color: #444;
        }

        .order-table tr:hover {
            background-color: #666;
        }

        .toggle-button {
            background-color: #444;
            color: #fff;
            padding: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .toggle-button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <!-- Header and Navigation -->
    <?php include "navigation.php"; ?>

    <!-- Admin Dashboard Section -->
    <div class="container">
        <h2 class="dashboard-header">Welcome to Admin Dashboard, <?php echo $username; ?>!</h2>
        
        <!-- User List Section -->
        <div class="section user-list">
            <h3>All Users</h3>
            <?php if (mysqli_num_rows($user_result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($user_result)): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['contact']; ?></td>
                                <td><?php echo $user['address']; ?></td>
                                <td>
                                    <!-- Accept Button -->
                                    <a href="?admindahboard.php=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to accept this user?');">Accept</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <!-- Button to Toggle Orders -->
                                    <button class="toggle-button" onclick="toggleOrders(<?php echo $user['id']; ?>)">
                                        View Orders for <?php echo $user['username']; ?>
                                    </button>

                                    <!-- Order Table Wrapper -->
                                    <div class="order-table-container">
                                        <table class="order-table" id="orders-<?php echo $user['id']; ?>">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Order Date</th>
                                                    <th>Total</th>
                                                    <th>Payment Method</th>
                                                    <th>Delivery Fee</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                // Check orders for this user
                                                $user_orders = array_filter($orders, function($order) use ($user) {
                                                    return $order['username'] == $user['username']; // Match using username
                                                });

                                                if (count($user_orders) > 0) {
                                                    foreach ($user_orders as $order) {
                                                        echo '<tr>';
                                                        echo '<td>' . $order['order_id'] . '</td>';
                                                        echo '<td>' . date("F j, Y", strtotime($order['date'])) . '</td>';
                                                        echo '<td>RS. ' . number_format($order['total'], 2) . '</td>';
                                                        echo '<td>' . $order['payment_method'] . '</td>';
                                                        echo '<td>RS. ' . number_format($order['delivery_fee'], 2) . '</td>';
                                                        echo '</tr>';
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="5">No orders found for this user.</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty">No users found.</p>
            <?php endif; ?>
        </div>

        <!-- Accepted Orders Section -->
        <div class="section accepted-orders-container">
            <h3>Accepted Orders</h3>
            <!-- Toggle Button for Accepted Orders -->
            <button class="toggle-button" onclick="toggleAcceptedOrders()">
                View Accepted Orders
            </button>

            <!-- Accepted Orders Table Wrapper -->
            <div class="order-table-container">
                <table class="order-table" id="accepted-orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Username</th>
                            <th>Address</th>
                            <th>Total</th>
                            <th>Payment Method</th>
                            <th>Delivery Fee</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($accepted_orders) > 0): ?>
                            <?php foreach ($accepted_orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['username']; ?></td>
                                    <td><?php echo $order['address']; ?></td>
                                    <td>RS. <?php echo number_format($order['total'], 2); ?></td>
                                    <td><?php echo $order['payment_method']; ?></td>
                                    <td>RS. <?php echo number_format($order['delivery_fee'], 2); ?></td>
                                    <td><?php echo date("F j, Y", strtotime($order['date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No accepted orders yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        // Function to toggle order table visibility for each user
        function toggleOrders(userId) {
            var table = document.getElementById("orders-" + userId);
            if (table.style.display === "none" || table.style.display === "") {
                table.style.display = "table";
            } else {
                table.style.display = "none";
            }
        }

        // Function to toggle visibility of accepted orders
        function toggleAcceptedOrders() {
            var table = document.getElementById("accepted-orders-table");
            if (table.style.display === "none" || table.style.display === "") {
                table.style.display = "table";
            } else {
                table.style.display = "none";
            }
        }
    </script>

</body>
</html>
