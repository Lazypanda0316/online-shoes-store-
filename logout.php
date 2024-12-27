<?php
// Start the session
session_start();

// Destroy the session to log the user out
session_unset();  // Remove all session variables
session_destroy(); // Destroy the session

// Redirect to the login page or home page
header("Location: login.php"); // Adjust the redirect as necessary
exit();
?>
