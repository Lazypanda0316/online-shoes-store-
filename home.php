<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION["username"]) || $_SESSION["username"] == "") {
    header("Location: login.php");
    exit();
}

// Include the database connection
include("conn.php");
include("functions.php");

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Handle adding products to the cart
if (isset($_GET['add_to_cart'])) {
    $productId = $_GET['add_to_cart'];
    $cart = getCartFromCookie();  // Retrieve current cart from cookie
    // Check if the product is already in the cart
    $found = false;
    foreach ($cart as &$item) {
        if ($item['id'] == $productId) {
            $item['quantity'] += 1;  // Increase quantity if the item is already in the cart
            $found = true;
            break;
        }
    }

    // Get product details from the database
    $query = "SELECT * FROM products WHERE product_id = $productId";
    $res1 = mysqli_query($conn, $query);
    if (mysqli_num_rows($res1) > 0) {
        $row1 = mysqli_fetch_assoc($res1);
    }

    // If the product is not found in the cart, add it
    if (!$found) {
        $cart[] = [
            'id' => $productId,
            'name' => $row1['name'],
            'price' => $row1['price'],
            'description' => $row1['description'],
            'quantity' => 1
        ];
    }

    // Save the updated cart back to the cookie
    saveCartToCookie($cart);

    // Return a success response as JSON
    echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        /* Basic styling and layout */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #22336c, #3e3c3c); /* Gradient background */
            color: #FFFFFF;
        }

        .header {
            background: linear-gradient(to right, #22336c, #3e3c3c);
            color: #fff;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            background: linear-gradient(to bottom right, rgb(24, 24, 24), rgb(53, 52, 104));
            color: #fff;
            position: relative;
            padding-top: 100px;
        }

        .hero h1 {
            font-size: 5rem;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.5rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .footer {
            background: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }

        .product {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 250px;
            margin: 10px;
            text-align: center;
        }

        .product img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .product h3 {
            margin: 10px 0;
            color: #333;
        }

        .product p {
            color: #555;
            margin-bottom: 20px;
        }

        .product button {
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .product button:hover {
            background-color: #2980b9;
        }

        /* Cart notification */
        .notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #2ecc71;
            color: white;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <!-- Header and Navigation -->
    <?php include "navigation.php"; ?>

    <!-- Hero Section -->
    <section class="hero">
    <?php include("carousel.php"); ?>

        <div class="container">
            <h1>Welcome to Panda Kicks</h1>
            <p>"Welcome to Panda Kicks, your ultimate destination for stylish and comfortable footwear that's as unique as you are!"</p>
        </div>
    </section>

    <!-- Product Section -->
    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                    <div class='product'>
                        <img src='images/" . $row["image_url"] . "' alt='" . $row["name"] . "'>
                        <h3>" . $row["name"] . "</h3>
                        <h3>RS. " . $row["price"] . "</h3>
                        <button class='add-to-cart-btn' data-id='" . $row["product_id"] . "'>ADD TO CART</button>
                    </div>";
            }
        } else {
            echo "<p>No products found</p>";
        }
        ?>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification"></div>

    <!-- Footer -->
    <footer>
        <?php include 'footer.php'; ?>
    </footer>

    <!-- JavaScript for handling cart additions -->
    <script>
        // Function to display the notification
        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000); // Hide after 3 seconds
        }

        // Handle Add to Cart button click
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                
                // Send an AJAX request to add the product to the cart
                fetch(`?add_to_cart=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Show success notification
                            showNotification(data.message);
                        } else {
                            alert('Failed to add product to cart');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>

</body>
</html>
