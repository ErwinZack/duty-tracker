<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture time in and time out
    $time_in = trim($_POST['time_in']);
    $time_out = trim($_POST['time_out']);
    $student_id = $_SESSION['student_id'];  // Get student ID from session
    $duration = (strtotime($time_out) - strtotime($time_in)) / 3600;  // Calculate duration in hours

    // Validate inputs
    if (empty($time_in) || empty($time_out)) {
        $error = "Please provide both time in and time out.";
    } elseif ($duration <= 0) {
        $error = "Time out must be later than time in.";
    } else {
        // Check if student_id exists in the students table (use student_id to get the id)
        $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);
        
        if ($stmt->rowCount() == 0) {
            $error = "Student ID does not exist in the system.";
        } else {
            // Fetch the student id from the result
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            $student_id_from_db = $student['id'];  // This is the id we need for the duty log

            // If student exists, insert the duty log into the database
            $stmt = $pdo->prepare("INSERT INTO duty_logs (student_id, time_in, time_out, duration, status) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$student_id_from_db, $time_in, $time_out, $duration, 'Pending'])) {
                $success = "Duty log added successfully!";
            } else {
                $error = "Error occurred while adding the duty log.";
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
    <title>Add Duty Log</title>
    <link rel="stylesheet" href="../assets/student.css">
</head>

<body>
    <div class="add-duty-container">
        <header>
            <h2>Add Duty Log</h2>
            <a href="dashboard.php">Back to Dashboard</a>
        </header>

        <!-- Display error or success messages -->
        <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
        <?php elseif ($success): ?>
        <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <!-- Duty Log Form -->
        <form action="" method="POST">
            <label for="time_in">Time In:</label>
            <input type="datetime-local" name="time_in" required>

            <label for="time_out">Time Out:</label>
            <input type="datetime-local" name="time_out" required>

            <button type="submit">Submit Duty Log</button>
        </form>
    </div>
</body>

</html>