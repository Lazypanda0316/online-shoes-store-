<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            width: 100%;
        }
        .footer {
            background-color: #333;
            color: #fff;
            padding: 40px 0;
        }
        .footer-container {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: auto;
            justify-content: space-between;
        }
        .footer-section {
            flex: 1;
            min-width: 200px;
            margin: 20px;
        }
        .footer-section h3 {
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
        }
        .footer-section ul {
            list-style: none;
            padding: 0;
        }
        .footer-section ul li {
            margin: 10px 0;
        }
        .footer-section ul li a {
            color: #fff;
            text-decoration: none;
        }
        .footer-section ul li a:hover {
            text-decoration: underline;
        }
        .footer-bottom {
            text-align: center;
            padding: 15px 0;
            background-color: #222;
            color: #bbb;
        }
    </style>
</head>
<body>
    <div class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>Panda Kicks is a new e-commerce website that sells trending shoes in the online market. It is simple to used and user-friendly.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: PandaKicks@gmail.com</p>
                <p>Phone: +123-456-7890</p>
                <p>Address: Patan, Lalitpur, Nepal</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <ul>
                    <li><a href="#facebook">Facebook</a></li>
                    <li><a href="#twitter">Twitter</a></li>
                    <li><a href="#instagram">Instagram</a></li>
                    <li><a href="#linkedin">LinkedIn</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 Panda Kicks. All rights reserved.</p>
    </div>
</body>
</html>
