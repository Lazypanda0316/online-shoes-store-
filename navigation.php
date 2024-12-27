<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar</title>
    <link rel="stylesheet" href="navigation.css">
</head>
<body>
    <header>
        <nav class="nav">
            <ul>
                <li class="list"><a href="home.php"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="text">Home</span></a></li>
                <li class="list"><a href="cart.php"><span class="icon"><ion-icon name="cart-outline"></ion-icon></span><span class="text">Cart</span></a></li>
                <li class="list"><a href="dashboard.php"><span class="icon"><ion-icon name="person-outline"></ion-icon></span><span class="text">Profile</span></a></li>
                <li class="list"><a href="logout.php"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="text">Logout</span></a></li>
            </ul>
            <div class="indicator"></div>
        </nav>
    </header>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>   
    <script src="navigation.js"></script>
</body>
</html>
