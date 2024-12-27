<?php
session_start();

// Ensure the username is set, otherwise redirect to login
if (!isset($_SESSION["username"])) {
    $_SESSION["username"] = "";  // Only set to empty if it's not already set
}

if (isset($_SESSION["username"])) {
    if (($_SESSION["username"]) == "") {
        header("location: login.php");
    } else {
        $username = $_SESSION["username"];
    }
} else {
    header("location: login.php");
}

include("functions.php");

// Handle updating quantities or removing products from the cart
if (isset($_POST['update'])) {
    $cart = getCartFromCookie();  // Retrieve current cart from cookie

    // Update the quantity of the product
    foreach ($cart as $id => $item) {
        $cart[$id]['quantity'] = $_POST['quantity'][$item['id']];
    }

    // Save updated cart to cookie
    saveCartToCookie($cart);
    saveCartToDatabase($cart);
    header("Location: cart.php"); // Refresh the page after update
}

if (isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    $cart = getCartFromCookie();  // Retrieve current cart from cookie

    // Remove the item from the cart
    foreach ($cart as $key => $item) {
        if ($item['id'] == $productId) {
            unset($cart[$key]);
            break;
        }
    }

    // Re-index the array after removal
    $cart = array_values($cart);

    // Save updated cart to cookie
    saveCartToCookie($cart);
    saveCartToDatabase($cart);
    header("Location: cart.php"); // Refresh the page after removal
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        /* Ensures the page takes up the full height */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #22336c, #3e3c3c);
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            min-height: 100vh;  /* Ensures body takes full height */
        }

        /* Main content area */
        .container {
            flex-grow: 1;  /* Allows the container to take up remaining space */
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        .cart-header {
            padding-top: 100px;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .cart-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .cart-table th, .cart-table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .cart-table th {
            background-color: #22336c;
        }

        .cart-table td {
            background-color: #333;
        }

        .cart-table input[type="number"] {
            padding: 5px;
            font-size: 16px;
            width: 60px;
            text-align: center;
        }

        .cart-table a {
            padding: 5px 10px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .cart-table a:hover {
            background-color: #c0392b;
        }

        .cart-total {
            margin-top: 20px;
            font-size: 1.5rem;
            color: #fff;
        }

        .cart-button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .cart-button:hover {
            background-color: #2980b9;
        }

        .cart-button1 {
            padding: 10px 20px;
            margin-top: 20px;
            position: relative;
            top: 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            position: relative;
        }

        .cart-button1:hover {
            background-color: #2980b9;
        }

        /* Footer styles */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Header and Navigation -->
    <?php include "navigation.php"; ?>

    <!-- Cart Section -->
    <div class="container">
        <h2 class="cart-header">Your Cart</h2>

        <?php
        $cart = getCartFromCookie();  // Retrieve cart items from cookie

        if (count($cart) > 0) {
            echo '<form method="POST" action="">';
            echo '<table class="cart-table">';
            echo '<tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Action</th>
                  </tr>';

            $total = 0;
            foreach ($cart as $item) {
                echo '<tr>';
                echo '<td style="color: white;">' . htmlspecialchars($item['name']) . '</td>';
                echo '<td>RS. ' . number_format($item['price'], 2) . '</td>';
                echo '<td style="color: white;">' . htmlspecialchars($item['description']) . '</td>';
                echo '<td><input type="number" name="quantity['.$item['id'].']" value="' . $item['quantity'] . '" min="1"></td>';
                echo '<td>
                        <a href="cart.php?remove=' . $item['id'] . '">Remove</a>
                      </td>';
                echo '</tr>';

                $total += $item['price'] * $item['quantity'];
                echo '<input type="hidden" name="product_id" value="' . $item['id'] . '">';
            }

            echo '</table>';
            echo '<button class="cart-button" type="submit" name="update">Update Cart</button>';
            echo '</form>';

            echo '<div class="cart-total">';
            echo '<strong>Total: RS. ' . number_format($total, 2) . '</strong>';
            echo '</div>';

            // Checkout button (optional)
            echo '<a href="checkout.php" class="cart-button1">Proceed to Checkout</a>';
        } else {
            echo '<p>Your cart is empty. Start shopping!</p>';
        }
        ?>

    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>
