<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch students
$stmt = $pdo->prepare("SELECT * FROM students");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete student
if (isset($_GET['delete'])) {
    $student_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    if ($stmt->execute([$student_id])) {
        header('Location: student_profiles.php');
        exit();
    } else {
        echo "Error deleting student profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profiles</title>
    <link rel="stylesheet" href="../assets/admin.css">
    <script src="../assets/dashboard.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Match with approved_duties.php -->

        <?php include '../includes/sidebar.php'; ?>

        <main class="main-content">
            <!-- Match with approved_duties.php -->
            <header class="header-container">
    <div class="header-left">
        <h2><i class="fas fa-users"></i> Total Students</h2>
    </div>
    
    <div class="header-right">
        <div class="search-sort-container">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search...">
            </div>
            
            <div class="dropdown">
                <img src="../assets/image/sort-icon.jpg" alt="Sort" onclick="toggleDropdown()">
                <div class="dropdown-content" id="dropdown">
                    <select id="sortSelect">
                        <option value="id">ID</option>
                        <option value="student_id">Student ID</option>
                        <option value="name">Name</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</header>


            <section class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Student ID</th>
                            <th>Scholar- Type</th>
                            <th>Course</th>
                            <th>Depart ment</th>
                            <th>Year Level</th>
                            <th>HK Duty Status</th>
                            <th>Register Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['scholarship_type']); ?></td>
                            <td><?php echo htmlspecialchars($student['course']); ?></td>
                            <td><?php echo htmlspecialchars($student['department']); ?></td>
                            <td><?php echo htmlspecialchars($student['year_level']); ?></td>
                            <td><?php echo htmlspecialchars($student['hk_duty_status']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($student['created_at'])); ?></td>
                            <td>
                            <a href="#" class="edit-btn btn" 
                                data-id="<?php echo $student['id']; ?>"
                                data-name="<?php echo htmlspecialchars($student['name']); ?>"
                                data-email="<?php echo htmlspecialchars($student['email']); ?>"
                                data-course="<?php echo htmlspecialchars($student['course']); ?>"
                                data-department="<?php echo htmlspecialchars($student['department']); ?>"
                                data-scholarship-type="<?php echo htmlspecialchars($student['scholarship_type']); ?>"
                                data-hk-duty-status="<?php echo htmlspecialchars($student['hk_duty_status']); ?>"
                                data-year-level="<?php echo htmlspecialchars($student['year_level']); ?>">
                                Edit
                                </a>
                                <a href="?delete=<?php echo $student['id']; ?>" class="btn"
                                    onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <!-- Edit Student Modal -->
    <div id="editStudentModal" class="edit-modal">
        <div class="edit-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Student Profile</h2>
            <div id="modalMessage"></div>
            
            <form id="editStudentForm">
                <input type="hidden" id="studentId" name="id">

                <label>Name</label>
                <input type="text" id="studentName" name="name" placeholder="Name" required>

                <label>Email</label>
                <input type="email" id="studentEmail" name="email" placeholder="Email" required>

                <label>Course</label>
                <input type="text" id="studentCourse" name="course" placeholder="Course" required>

                <label>Department</label>
                <input type="text" id="studentDepartment" name="department" placeholder="Department" required>

                <label>Scholarship Type</label>
                <input type="text" id="studentScholarshipType" name="scholarship_type" placeholder="Scholarship Type" required>

                <label>HK Duty Status</label>
                <input type="text" id="studentHKDutyStatus" name="hk_duty_status" placeholder="HK Duty Status" required>

                <label>Year Level</label>
                <input type="text" id="studentYearLevel" name="year_level" placeholder="Year Level" required>

                <div class="buttons">
                    <button type="button" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="approve-button">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Get the modal and elements
        var modal = document.getElementById("editStudentModal");
        var closeBtn = document.getElementsByClassName("close")[0];

        // Handle edit button click
        document.querySelectorAll(".edit-btn").forEach(button => {
    button.addEventListener("click", function () {
        document.getElementById("studentId").value = this.dataset.id;
        document.getElementById("studentName").value = this.dataset.name;
        document.getElementById("studentEmail").value = this.dataset.email;
        document.getElementById("studentCourse").value = this.dataset.course;
        document.getElementById("studentDepartment").value = this.dataset.department;
        document.getElementById("studentScholarshipType").value = this.dataset.scholarshipType;
        document.getElementById("studentHKDutyStatus").value = this.dataset.hkDutyStatus;
        document.getElementById("studentYearLevel").value = this.dataset.yearLevel;

        document.getElementById("editStudentModal").style.display = "block";
    });
});

function closeModal() {
    document.getElementById("editStudentModal").style.display = "none";
}

document.getElementById("editStudentForm").onsubmit = function (e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("edit_student_profile.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("modalMessage").innerHTML = data;
        setTimeout(() => {
            closeModal();
            location.reload();  // Refresh table after update
        }, 1500);
    });
};

    </script>

</body>

</html>