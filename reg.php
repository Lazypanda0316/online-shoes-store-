<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecom";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = '';

// Check if the registration form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];

    // Validate if all fields are filled
    if (empty($user) || empty($pass) || empty($email) || empty($address) || empty($contact)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Check if username already exists
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username already taken.";
        } else {
            // Insert the new user into the database with plain password (no hashing)
            $sql = "INSERT INTO users (username, email, password, address, contact) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $user, $email, $pass, $address, $contact);

            if ($stmt->execute()) {
                header("Location: login.php"); // Redirect to login page after successful registration
                exit();
            } else {
                $error_message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="login.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="login-container">
        <form action="reg.php" method="POST" class="login-form">
            <h2>Create Account</h2>

            <!-- Username Field -->
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <!-- Email Field -->
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Password Field -->
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Address Field -->
            <div class="input-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>

            <!-- Contact Field -->
            <div class="input-group">
                <label for="contact">Contact Number</label>
                <input type="text" id="contact" name="contact" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="login-button">Register</button>

            <!-- Display error message if registration fails -->
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <!-- Link to login page -->
            <div class="signup-link">
                <a href="login.php" class="create-account-button">Already have an account? Login</a>
            </div>
        </form>
    </div>

    <script src="login.js"></script> 
</body>
</html>
