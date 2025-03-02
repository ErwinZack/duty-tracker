<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
?>

<nav class="sidebar">
    <div class="logo-container">
        <img src="../assets/image/University_of_Pangasinan_logo.png" alt="Logo">
        <!-- Ensure logo path is correct -->
        <h2>Hk Admin</h2>
    </div>
    <hr> <!-- Horizontal line below Admin Panel title -->
    <ul>
        <li><a href="dashboard.php" class="active"><i class="fa fa-dashboard"></i></i> Dashboard</a></li>
        <li><a href="approve_duty.php"><i class="fa fa-check-square-o"></i> Approve
                Duty Logs</a></li>
        <li><a href="student_profiles.php"><i class="fas fa-user"></i> Student Profiles</a></li>
        <li><a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
        <li><a href="export_report.php"><i class="fas fa-file-export"></i> Export Reports</a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
