<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nike AIR</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        header {
            background: linear-gradient(135deg, #1a237e, #424242);
            padding: 1rem 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* nav a {
            color: white;
            text-decoration: none;
            margin: 0 1.5rem;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        } */
        /* Hero Section */
        .hero {
            background-image: url('shoe-banner.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        .hero p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        .hero button {
            background-color: #1a237e;
            border: none;
            padding: 1rem 2rem;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .hero button:hover {
            background-color: #424242;
        }

        /* Product Section */
        .products {
            padding: 3rem 2rem;
            background-color: white;
            text-align: center;
        }
        .products h2 {
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        .product-grid {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .product-item {
            width: 30%;
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #f1f1f1;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .product-item img {
            max-width: 100%;
            border-radius: 10px;
        }
        .product-item h3 {
            margin: 1rem 0;
        }
        .product-item p {
            font-size: 0.9rem;
            color: #424242;
        }
        .product-item button {
            background-color: #1a237e;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            cursor: pointer;
        }
        .product-item button:hover {
            background-color: #424242;
        }

        /* Footer */
        footer {
            background-color: #1a237e;
            color: white;
            padding: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>

    <header>
    <?php include "navigation.php"?>
        
    </header>

    <section class="hero">
        <h1>Nike AIR Collection</h1>
        <p>Step into the future of comfort and style with Nike AIR.</p>
        <button>Shop Now</button>
    </section>

    <section class="products">
        <h2>Our Featured Shoes</h2>
        <div class="product-grid">
            <div class="product-item">
                <img src="lou1.png" alt="Nike Air">
                <h3>Nike AIR</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia, laborum cumque dignissimos quidem atque et eligendi aperiam voluptates beatae maxime.</p>
                <button>SEE MORE &#8599;</button>
            </div>
            <div class="product-item">
                <img src="nikepanda.png" alt="Nike Panda">
                <h3>Nike Panda</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia, laborum cumque dignissimos quidem atque et eligendi aperiam voluptates beatae maxime.</p>
                <button>SEE MORE &#8599;</button>
            </div>
            <div class="product-item">
                <img src="convo.png" alt="Converse PRO Leather">
                <h3>Converse PRO Leather</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia, laborum cumque dignissimos quidem atque et eligendi aperiam voluptates beatae maxime.</p>
                <button>SEE MORE &#8599;</button>
            </div>
        </div>
    </section>
    <footer>
        <p>&copy; 2024 Nike AIR. All rights reserved.</p>
    </footer>

</body>
</html>
