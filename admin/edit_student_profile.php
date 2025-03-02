<?php
session_start();
require_once '../config/database.php';

$error = "";
$success = "";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check if 'id' is provided in the URL
if (!isset($_GET['id'])) {
    echo "Student ID not provided.";
    exit();
}

$student_id = $_GET['id'];

// Fetch student data from the database
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if student was found
if (!$student) {
    echo "Student not found.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated student details
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $department = trim($_POST['department']);
    $scholarship_type = trim($_POST['scholarship_type']);
    $hk_duty_status = trim($_POST['hk_duty_status']);
    $year_level = trim($_POST['year_level']);

    // Validate input
    if (empty($name) || empty($email) || empty($course) || empty($department) || empty($hk_duty_status)) {
        $error = "All fields are required.";
    } else {
        // Update student profile in the database
        $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, course = ?, department = ?, scholarship_type = ?, hk_duty_status = ?, year_level = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $course, $department, $scholarship_type, $hk_duty_status, $year_level, $student_id])) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Error updating profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <div class="edit-profile-container">
        <h2>Edit Student Profile</h2>

        <!-- Display success or error message -->
        <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
        <?php elseif ($success): ?>
        <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <!-- Form to update student details -->
        <form method="POST">
            <input type="text" name="name" value="<?php echo $student['name']; ?>" placeholder="Name" required>
            <input type="email" name="email" value="<?php echo $student['email']; ?>" placeholder="Email" required>
            <input type="text" name="course" value="<?php echo $student['course']; ?>" placeholder="Course" required>
            <input type="text" name="department" value="<?php echo $student['department']; ?>" placeholder="Department"
                required>
            <input type="text" name="scholarship_type" value="<?php echo $student['scholarship_type']; ?>"
                placeholder="Scholarship Type" required>
            <input type="text" name="hk_duty_status" value="<?php echo $student['hk_duty_status']; ?>"
                placeholder="HK Duty Status" required>
            <input type="text" name="year_level" value="<?php echo $student['year_level']; ?>" placeholder="Year Level"
                required>
            <button type="submit">Update Profile</button>
        </form>

        <!-- Back to Dashboard button -->
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>

</html>