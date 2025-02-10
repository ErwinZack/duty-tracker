<?php
// Start session to check if the user is already logged in
session_start();

// Redirect to student or admin dashboard if already logged in
if (isset($_SESSION['student_id'])) {
    header('Location: student/dashboard.php');
    exit();
} elseif (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page - Duty Tracker</title>
    <link rel="stylesheet" href="assets/style.css">
    <link
    href="https://fonts.googleapis.com/css2?
        family=Anek+Devanagari:wght@100..800&
        family=Jost:ital,wght@0,100..900;1,100..900&
        family=Roboto:ital,wght@0,100..900;1,100..900&
        family=Bebas+Neue&
        family=Poppins:ital, wght@0,100..900;1,100..900&
        family=Quicksand:wght@300..700&
        family=Varela+Round&
        display=swap"
        rel="stylesheet"
    />
</head>

<body>
    <div class="container">
        <h2 class="heading">Welcome to the Duty Tracker System</h2>
        <p class="sub-heading">Please choose your role to log in:</p>

        <div class="login-box-container">

            <!-- Student Login Form -->
            <div class="login-box">
                <h3>Student Login</h3>
                <form action="student/login.php" method="POST">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>

                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>

                    <button type="submit" class="btn">Login</button>
                </form>
            </div>

            <!-- Admin Login Form -->
            <div class="login-box">
                <h3>Admin Login</h3>
                <form action="admin/login.php" method="POST">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>

                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>

                    <button type="submit" class="btn">Login</button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>