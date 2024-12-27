<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .login-container {
            background-color: #ffffff;
            width: 400px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-container img {
            width: 100px;
            margin-bottom: 30px;
        }

        .login-container h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .input-field {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .input-field:focus {
            border-color: #2a9d8f;
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background-color:rgb(33, 158, 29);
            border: none;
            color: #fff;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color:rgb(17, 77, 21);
        }

        .forgot-password {
            font-size: 14px;
            color: #888;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #2a9d8f;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .create-account {
            font-size: 14px;
            margin-top: 20px;
        }

        .create-account a {
            color: #2a9d8f;
            text-decoration: none;
        }

        .create-account a:hover {
            text-decoration: underline;
        }

        /* Modal Styling */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0); /* Black w/ opacity */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
            border-radius: 10px;
        }

        .modal-button {
            padding: 10px 20px;
            background-color: #2a9d8f;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .modal-button:hover {
            background-color: #298a7b;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="images/esewa1.png" alt="eSewa Logo">
        <h2>Login to eSewa</h2>
        
        <form id="login-form" action="payment_page.php" method="POST" onsubmit="event.preventDefault(); showModal();">
            <input type="text" class="input-field" name="phone" placeholder="Phone Number" required>
            <input type="password" class="input-field" name="mpin" placeholder="MPIN" required>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="forgot-password">
            <a href="#">Forgot your password?</a>
        </div>

        <div class="create-account">
            Don't have an account? <a href="#">Create one</a>
        </div>
    </div>

    <!-- Modal for Payment Success -->
    <div id="payment-success-modal" class="modal">
        <div class="modal-content">
            <h2>Payment Successful</h2>
            <p>Your payment was successfully processed. You will now be redirected to your dashboard.</p>
            <button class="modal-button" onclick="redirectToDashboard()">OK</button>
        </div>
    </div>

    <script>
        // Show the modal when the form is submitted
        function showModal() {
            document.getElementById('payment-success-modal').style.display = 'block';
        }

        // Redirect the user to the dashboard after clicking "OK" on the modal
        function redirectToDashboard() {
            window.location.href = "dashboard.php"; // Redirect to the dashboard page
        }

        // Close the modal if the user clicks anywhere outside of the modal
        window.onclick = function(event) {
            var modal = document.getElementById("payment-success-modal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>
