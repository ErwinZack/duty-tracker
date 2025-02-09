<?php
// Start session to check if the user is already logged in
session_start();

// Redirect to admin dashboard if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Include database connection file
require_once '../config/database.php';

$error = ''; // Initialize error variable

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare the SQL query to fetch admin details by username
        $query = "SELECT * FROM admins WHERE username = ? LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        // Check if user exists and verify password
        if ($admin && password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];

            // Redirect to admin dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // If login fails, show an error
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Username and password must be provided.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>
</body>

</html>