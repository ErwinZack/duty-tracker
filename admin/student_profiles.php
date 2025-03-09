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
                            <td><div class="table-icon">
                                <a href="#" class="edit-btn btn" 
                                    data-id="<?php echo $student['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($student['name']); ?>"
                                    data-email="<?php echo htmlspecialchars($student['email']); ?>"
                                    data-course="<?php echo htmlspecialchars($student['course']); ?>"
                                    data-department="<?php echo htmlspecialchars($student['department']); ?>"
                                    data-scholarship-type="<?php echo htmlspecialchars($student['scholarship_type']); ?>"
                                    data-hk-duty-status="<?php echo htmlspecialchars($student['hk_duty_status']); ?>"
                                    data-year-level="<?php echo htmlspecialchars($student['year_level']); ?>">
                                    <img src="../assets/image/pen-icon.svg" alt="edit">
                                    </a>
                                    
                                    <a href="#" 
                                        class="btn delete-btn"
                                        data-id="<?php echo $student['id']; ?>"
                                        onclick="openDeleteModal(this)">
                                        <img src="../assets/image/trash-icon.svg" alt="delete">
                                        </a>
                                    </a>
                                </div>
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
            
            <form id="editStudentForm" method="POST">
                <input type="hidden" id="studentId" name="id">

                <label for="studentName"><i class="fas fa-user"></i>
                &nbsp;Name</label>
                <input type="text" id="studentName" name="name" placeholder="Name" required>

                <label for="studentEmail"><i class="fas fa-envelope"></i>
                &nbsp;Email</label>
                <input type="email" id="studentEmail" name="email" placeho  lder="Email" required>


                <label for="student-course"><i class="fa-solid fa-graduation-cap"></i>
                &nbsp;Course</label>
                <select name="course" id="studentCourse" required>
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

                <label for="student-department"><i class="fas fa-building"></i>
                &nbsp;Department</label>
                <select name="department" id="studentDepartment"required>
                <option value="" disabled selected>Select Department</option>
                                <option value="CEA">CEA</option>
                                <option value="CMA">CMA</option>
                                <option value="CAHS">CAHS</option>
                                <option value="CITE">CITE</option>
                                <option value="CCJE">CCJE</option>
                                <option value="CELA">CELA</option>
                </select>

                <label for="student-scholarship"><i class="fas fa-award"></i>
                &nbsp;Scholarship Type</label>
                    <select name="scholarship_type" id="studentScholarshipType">
                        <option value="" disabled selected>Select Scholarship Type</option>
                        <option value="HK 25">HK 25</option>
                        <option value="HK 50">HK 50</option>
                        <option value="HK 75">HK 75</option>
                    </select>

                <label for="student-hkdutystatus"><i class="fas fa-tasks"></i>
                &nbsp;HK Duty Status:</label>
                    <select name="hk_duty_status" id="studentHKDutyStatus" required>
                        <option value="" disabled selected>Select Duty Status</option>
                        <option value="Module Distributor">Module Distributor</option>
                        <option value="Student Facilitator">Student Facilitator</option>
                        <option value="Library Assistant">Library Assistant</option>
                        <option value="Admin Assistant">External Facilitator</option>
                    </select>

                <label for="student-yearLevel"><i class="fas fa-layer-group"></i>
                &nbsp;Year Level:</label>
                    <select name="year_level" id="studentYearLevel" required>
                        <option value="" disabled selected>Select Year Level</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                        <option value="5th Year">5th Year</option>
                    </select>
                <div class="buttons">
                    <button type="button" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="approve-button">Update</button>
                </div>
            </form>
        </div>
    </div>
    
    <!--delete modal -->

    <div id="deleteConfirmModal" class="delete-modal">
        <div class="delete-content">
            <span class="close" onclick="closeDeleteConfirmModal()">&times;</span>
            <h2>Confirm Action</h2>
            <p>Are you sure you want to proceed?</p>
            <form id="deleteConfirmForm" method="POST">
                <input type="hidden" id="deleteItemId" name="id">
                <div class="buttons">
                    <button type="button" onclick="closeDeleteConfirmModal()">Cancel</button>
                    <button type="submit" class="approve-button">Confirm</button>
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
document.addEventListener("DOMContentLoaded", function () {
    let form = document.getElementById("editStudentForm");
    let updateButton = form.querySelector("button[type='submit']");
    let inputs = form.querySelectorAll("input, select");

    // Store original values when modal opens
    let originalData = {};

    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            originalData = {
                name: this.dataset.name,
                email: this.dataset.email,
                course: this.dataset.course,
                department: this.dataset.department,
                scholarship_type: this.dataset.scholarshipType,
                hk_duty_status: this.dataset.hkDutyStatus,
                year_level: this.dataset.yearLevel,
            };
            updateButton.disabled = true; 
        });
    });

    // Check for changes in form fields
    inputs.forEach(input => {
        input.addEventListener("input", function () {
            let hasChanges = false;
            for (let key in originalData) {
                let formElement = document.querySelector(`[name='${key}']`);
                if (formElement && formElement.value !== originalData[key]) {
                    hasChanges = true;
                    break;
                }
            }
            updateButton.disabled = !hasChanges;
        });
    });
});
// Open Delete Confirmation Modal
function openDeleteModal(button) {
    let studentId = button.getAttribute("data-id");
    document.getElementById("deleteItemId").value = studentId;
    
    let modal = document.getElementById("deleteConfirmModal");
    if (modal) {
        modal.style.display = "flex";
    }
}

// Close Delete Modal Function
function closeDeleteConfirmModal() {
    let modal = document.getElementById("deleteConfirmModal");
    if (modal) {
        modal.style.display = "none";
    }
}

// Handle Delete Form Submission
document.getElementById("deleteConfirmForm").onsubmit = function (e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("delete_student.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            closeDeleteConfirmModal(); // Close modal on success
            location.reload();  // Refresh the table
        } else {
            alert("Error deleting student.");
        }
    })
    .catch(error => console.error("Error:", error));
};

// Ensure modal is hidden on page load
document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("deleteConfirmModal");
    if (modal) {
        modal.style.display = "none";
    }
});

    </script>

</body>

</html>