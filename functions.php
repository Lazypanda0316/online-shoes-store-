<?php

function saveCartToDatabase($cart)
{
    include("conn.php");

    $deleteQuery = "DELETE FROM `cart` WHERE user_id = ".$_SESSION['userid'];
    $deleteResult = mysqli_query($conn, $deleteQuery);

    foreach($cart as $item){
        $query="INSERT INTO `cart`(`user_id`, `products_id`, `quantity`, `added_at`) VALUES ('".$_SESSION['userid']."','".$item['id']."','".$item['quantity']."',CURDATE())";
        $res1=mysqli_query($conn, $query);
    }
}



// functions.php
function getCartItems() {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        return $_SESSION['cart']; // Assuming you store the cart in the session
    }
    return []; // Return an empty array if no cart exists
}

function getCartFromDatabase($conn, $username) {
    // Check if username is set in the session
    if (empty($username)) {
        die("Username is not set in session.");
    }

    // Step 1: Retrieve user ID based on username
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("s", $username);

    if (!$stmt->execute()) {
        die('Execute error: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    
    
    if ($row = $result->fetch_assoc()) {
        $userId = $row['id'];  // Get user ID
    } else {
        die("User not found in the database.");
    }

    $stmt->close();

    
    $query = "SELECT c.cart_id, p.product_id, p.name, p.price, p.description, c.quantity
              FROM cart c
              JOIN products p ON c.products_id = p.product_id
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("i", $userId);

    if (!$stmt->execute()) {
        die('Execute error: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $cart = [];

    
    while ($row = $result->fetch_assoc()) {
        $cart[] = [
            'cart_id' => $row['cart_id'],
            'product_id' => $row['product_id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'description' => $row['description'],
            'quantity' => $row['quantity']
        ];
    }

    $stmt->close();
    return $cart;
}



function clearCart() {
    // Clear the cart stored in the cookie by setting it to an empty array or empty string
    setcookie('cart', '', time() - 3600, '/');  // Expire the cookie by setting a past time
}


// Function to get cart items from the cookie
function getCartFromCookie() {
    if (isset($_COOKIE['cart'])) {
        return json_decode($_COOKIE['cart'], true);
    }
    return [];
}

// Function to save the cart items to a cookie
function saveCartToCookie($cart) {
    setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); // 30 days expiration
}

?>