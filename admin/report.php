<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$sql = "SELECT * FROM duty_logs ORDER BY date DESC";
$result = $conn->query($sql);
?>

<h2>Duty Logs Report</h2>
<a href="dashboard.php">Back to Dashboard</a> |
<a href="export_report.php">Export Report</a>

<table border="1">
    <tr>
        <th>Student ID</th>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row["student_id"]; ?></td>
        <td><?php echo $row["date"]; ?></td>
        <td><?php echo $row["time_in"]; ?></td>
        <td><?php echo $row["time_out"]; ?></td>
        <td><?php echo $row["status"]; ?></td>
    </tr>
    <?php } ?>
</table>