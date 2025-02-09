<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if a user is logged in
function isLoggedIn() {
    // Return true if the session variable for user_id is set
    return isset($_SESSION['user_id']);
}

// Function to log a user out
function logout() {
    session_unset();  // Unset all session variables
    session_destroy();  // Destroy the session
    header("Location: login.php");  // Redirect to login page after logout
    exit();
}
?>