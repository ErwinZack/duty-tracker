<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize message variables
$message = [
    'type' => '',
    'text' => ''
];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $student_id = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $scholarship_type = trim($_POST['scholarship_type'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $hk_duty_status = trim($_POST['hk_duty_status'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate all required fields are filled
    $requiredFields = [
        $student_id, $name, $scholarship_type, $course, 
        $department, $year_level, $hk_duty_status, 
        $email, $password, $confirm_password
    ];

    if (in_array('', $requiredFields, true)) {
        $message = [
            'type' => 'error',
            'text' => 'Please fill in all fields.'
        ];
    } elseif ($password !== $confirm_password) {
        $message = [
            'type' => 'error',
            'text' => 'Passwords do not match.'
        ];
    } else {
        try {
            // Check if student_id or email already exists
            $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
            $stmt->execute([$student_id, $email]);

            if ($stmt->rowCount() > 0) {
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing['id'] === $student_id && $existing['email'] === $email) {
                    $message = [
                        'type' => 'error',
                        'text' => 'Both Student ID and Email already exist.'
                    ];
                } elseif ($existing['id'] === $student_id) {
                    $message = [
                        'type' => 'error',
                        'text' => 'Student ID already exists.'
                    ];
                } else{
                    $message = [
                        'type' => 'error',
                        'text' => 'User is Already Existed'
                    ];
                }
            } else{
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                // Insert student data
                $stmt = $pdo->prepare("INSERT INTO students (student_id, name, scholarship_type, course, department, year_level, hk_duty_status, email, password) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$student_id, $name, $scholarship_type, $course, $department, $year_level, $hk_duty_status, $email, $hashed_password])) {
                    $message = [
                        'type' => 'success',
                        'text' => 'Student successfully registered!'
                    ];

                    $_POST = [];
                } else {
                    $message = [
                        'type' => 'error',
                        'text' => 'Error occurred while adding student.'
                    ];
                }
            }
        } catch (PDOException $e) {
            $message = [
                'type' => 'error',
                'text' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'?>

        <main class="main-content">
            <div class="form-container">
                <h2><i class="fas fa-user-graduate"></i> Student Registration</h2>

                <form action="" method="POST" id="studentRegistrationForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="student_id"><i class="fas fa-id-card"></i> Student ID:</label>
                            <input type="text" name="student_id" id="student_id" placeholder="Enter student ID" 
                                   value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Full Name:</label>
                            <input type="text" name="name" id="name" placeholder="Enter full name" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                            <input type="email" name="email" id="email" placeholder="Enter email address" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="scholarship_type"><i class="fas fa-award"></i> Scholarship Type:</label>
                            <select name="scholarship_type" id="scholarship_type" required>
                                <option value="" disabled selected>Select Scholarship Type</option>
                                <option value="HK 25" <?= isset($_POST['scholarship_type']) && $_POST['scholarship_type'] == 'HK 25' ? 'selected' : '' ?>>HK 25</option>
                                <option value="HK 50" <?= isset($_POST['scholarship_type']) && $_POST['scholarship_type'] == 'HK 50' ? 'selected' : '' ?>>HK 50</option>
                                <option value="HK 75" <?= isset($_POST['scholarship_type']) && $_POST['scholarship_type'] == 'HK 75' ? 'selected' : '' ?>>HK 75</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="course"><i class="fas fa-graduation-cap"></i> Course:</label>
                            <select name="course" id="course" required>
                                <option value="" disabled selected>Select Course</option>
                                <option value="BSIT">BS Information Technology</option>
                                <option value="BSCS">BS Computer Science</option>
                                <option value="BSE">BS Education</option>
                                <option value="BBA">BS Business Administration</option>
                                <option value="BSCRIM">BS Criminology</option>
                                <option value="BSA">BS Accountancy</option>
                                <option value="BSN">BS Nursing</option>
                                <option value="BSARCH">BS Architecture</option>
                                <option value="BSCOE">BS Computer Engineering</option>
                                <option value="BSEE">BS Electrical Engineering</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="department"><i class="fas fa-building"></i> Department:</label>
                            <select name="department" id="department" required>
                                <option value="" disabled selected>Select Department</option>
                                <option value="CEA">CEA</option>
                                <option value="CMA">CMA</option>
                                <option value="CAHS">CAHS</option>
                                <option value="CITE">CITE</option>
                                <option value="CCJE">CCJE</option>
                                <option value="CELA">CELA</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="year_level"><i class="fas fa-layer-group"></i> Year Level:</label>
                            <select name="year_level" id="year_level" required>
                                <option value="" disabled selected>Select Year Level</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                                <option value="5th Year">5th Year</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="hk_duty_status"><i class="fas fa-tasks"></i> HK Duty Status:</label>
                            <select name="hk_duty_status" id="hk_duty_status" required>
                                <option value="" disabled selected>Select Duty Status</option>
                                <option value="Module Distributor">Module Distributor</option>
                                <option value="Student Facilitator">Student Facilitator</option>
                                <option value="Library Assistant">Library Assistant</option>
                                <option value="Admin Assistant">External Facilitator</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password:</label>
                            <div class="password-container">
                                <input type="password" name="password" id="password" placeholder="Enter password" required>
                                <img src="../assets/image/eye-beauty.png" alt="Show Password" class="toggle-password" data-target="password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password"><i class="fas fa-check-circle"></i> Confirm Password:</label>
                            <div class="password-container">
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
                                <img src="../assets/image/eye-beauty.png" alt="Show Password" class="toggle-password" data-target="confirm_password">
                            </div>
                        </div>
                    </div>
                    <button type="submit"><i class="fas fa-user-plus"></i> Register Student</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <div id="toast-container"></div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            let input = document.getElementById(this.dataset.target);
            input.type = input.type === "password" ? "text" : "password";
            this.src = input.type === "password" ? "../assets/image/eye-beauty.png" : "../assets/image/hide.png";
        });
    });

    const form = document.getElementById('studentRegistrationForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const studentIdInput = document.getElementById('student_id');
    const nameInput = document.getElementById('name');
    
    const studentIdPattern = /^03-\d{4}-\d{6}$/;
    const namePattern = /^[A-Za-z\s]+$/;

    function createErrorElement() {
        let error = document.createElement('p');
        error.style.color = 'red';
        error.style.fontSize = '12px';
        error.style.marginTop = '2px';
        return error;
    }
    
    let studentIdError = createErrorElement();
    studentIdInput.parentNode.appendChild(studentIdError);
    
    let nameError = createErrorElement();
    nameInput.parentNode.appendChild(nameError);

    studentIdInput.addEventListener('input', function() {
        const inputValue = studentIdInput.value;
        if (/[a-zA-Z]/.test(inputValue)) {
            studentIdError.textContent = '❌ Letters are not allowed! Use only numbers and dashes.';
        } else if (!studentIdPattern.test(inputValue) && inputValue.length >= 14) {
            studentIdError.textContent = '❌ Invalid format! E.g., 03-2324-031593';
        } else {
            studentIdError.textContent = '';
        }
    });

    nameInput.addEventListener('input', function() {
        const inputValue = nameInput.value;
        if (/[0-9]/.test(inputValue)) {
            nameError.textContent = '❌ Numbers are not allowed!';
        } else if (inputValue.length < 3) {
            nameError.textContent = '❌ Name is too short!';
        } else if (!namePattern.test(inputValue)) {
            nameError.textContent = '❌ Invalid format! Only letters and spaces are allowed.';
        } else {
            nameError.textContent = '';
        }
    });

    form.addEventListener('submit', function(event) {
        let isValid = true;
        if (passwordInput.value !== confirmPasswordInput.value) {
            showToast("Please make sure passwords match", "error");
            isValid = false;
        }
        if (nameInput.value.length < 4) {
        showToast("Name must be at least 4 characters long!", "error");
        isValid = false;
        }

        if (!studentIdPattern.test(studentIdInput.value)) {
            showToast("Invalid Student ID format!", "error");
            isValid = false;
        }
        if (!isValid) event.preventDefault();
    });

    <?php if (!empty($message['text'])): ?>
        showToast("<?= addslashes($message['text']) ?>", "<?= $message['type'] ?>");
    <?php endif; ?>
});

function showToast(message, type) {
    const toastContainer = document.getElementById("toast-container");
    document.querySelectorAll('.toast').forEach(toast => toast.remove());

    const toast = document.createElement("div");
    toast.classList.add("toast", type);
    toast.innerHTML = `
        <span class="icon">${type === "success" ? "✔" : "✖"}</span>
        <div class="toast-content">
            <div class="toast-message">${message}</div>
        </div>
        <div class="toast-progress"></div>
    `;
    
    toastContainer.appendChild(toast);
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 5000);
}

</script>

</body>
</html>