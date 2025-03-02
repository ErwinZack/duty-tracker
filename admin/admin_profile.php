<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch admin details
$stmt = $pdo->prepare("SELECT name, email, role, created_at FROM admin WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include '../admin/includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="header-container">
                <h2><i class="fa-solid fa-user"></i> Admin Profile</h2>
            </header>

            <section class="profile-section">
                <div class="profile-card">
                    <h3><?php echo htmlspecialchars($admin['name']); ?></h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
                    <p><strong>Role:</strong> <?php echo htmlspecialchars($admin['role']); ?></p>
                    <p><strong>Joined:</strong> <?php echo htmlspecialchars($admin['created_at']); ?></p>
                </div>
            </section>
        </main>
    </div>
</body>

</html>