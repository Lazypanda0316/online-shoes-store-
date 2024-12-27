<?php
session_start();
include('conn.php'); // Include your database connection

// Initialize error message
$error_message = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values
    $user = trim($_POST['username']);  // Use trim to remove any leading/trailing spaces
    $pass = trim($_POST['password']);  // Use trim to remove any leading/trailing spaces

    // First, try querying the admin table
    $sql_admin = "SELECT * FROM admin WHERE username = ?";  // Query for admin table
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("s", $user);  // Bind the username parameter

    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    // Check if query returns any rows from the admin table
    if ($result_admin->num_rows > 0) {
        $row = $result_admin->fetch_assoc();
        
        // Check if password matches (use password_verify() if stored with hashing)
        if ($pass == $row['password']) {  // For now, plain text comparison (use password_verify() if hashed)
            $_SESSION['username'] = $user;
            $_SESSION['userid'] = $row['id'];
            $_SESSION['role'] = 'admin';  // Mark the role as admin

            // Redirect to the admin dashboard
            header("Location: admindashboard.php");
            exit();
        } else {
            $error_message = "Invalid Username or Password (Admin)"; 
        }
    } else {
        // Admin not found, check the users table
        $sql_user = "SELECT * FROM users WHERE username = ?";  // Query for user table
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("s", $user);  // Bind the username parameter

        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        // Check if query returns any rows from the users table
        if ($result_user->num_rows > 0) {
            $row = $result_user->fetch_assoc();

            // Check if password matches (use password_verify() if stored with hashing)
            if ($pass == $row['password']) {  // For now, plain text comparison (use password_verify() if hashed)
                $_SESSION['username'] = $user;
                $_SESSION['userid'] = $row['id'];
                $_SESSION['role'] = 'user';  // Mark the role as user

                // Redirect to the user home page
                header("Location: home.php");
                exit();
            } else {
                $error_message = "Invalid Username or Password (User)"; 
            }
        } else {
            $error_message = "Invalid Username or Password"; 
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="login-container">
        <form action="login.php" method="POST" class="login-form">
            <h2>Login</h2>
            
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-button">Login</button>

            <!-- Display error message if login fails -->
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <!-- Button to redirect to the registration page -->
            <div class="signup-link">
                <a href="reg.php" class="create-account-button">Create New Account</a>
            </div>
        </form>
    </div>

    <script src="login.js"></script> <!-- Link to external JS (optional) -->
</body>
</html>
