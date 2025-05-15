<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

// Require database connection
require_once __DIR__ . "/../../config/db.php";

// Restrict access only to authorized users
if (!isset($_SESSION['admin_role']) || ($_SESSION['admin_role'] !== 'invigilator' && $_SESSION['admin_role'] !== 'admission office')) {
    header("Location: unauthorized.php");
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Retrieve venue options dynamically from the venues table
$venues = [];
$resultVenues = $conn->query("SELECT venue_name FROM venues ORDER BY venue_name ASC");

if ($resultVenues) {
    while ($row = $resultVenues->fetch_assoc()) {
        $venues[] = $row;
    }
}

// Initialize variables for form submission and results
$student_info = null;
$error_message = "";
$info_message = "";
$selected_venue = "";
$entered_admission = "";

// Process form submission (venue and admission number)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_venue = isset($_POST['venue']) ? trim($conn->real_escape_string($_POST['venue'])) : "";
    $entered_admission = isset($_POST['admission_no']) ? trim($conn->real_escape_string($_POST['admission_no'])) : "";

    if (empty($selected_venue)) {
        $error_message = "Please select a venue.";
    } elseif (empty($entered_admission)) {
        $error_message = "Admission number cannot be empty.";
    } else {
        // Prepare query to fetch student details
        $stmt = $conn->prepare("SELECT name, admission_no, exam_no, program, image_path, venue FROM students WHERE admission_no = ? AND venue = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $entered_admission, $selected_venue);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $student_info = $result->fetch_assoc();
                $info_message = "Student details found.";
            } else {
                $error_message = "Student with admission number '$entered_admission' not found in venue '$selected_venue'.";
            }
            $stmt->close();
        } else {
            $error_message = "Database query failed.";
            error_log("Query error: " . $conn->error);
        }
    }
}

$conn->close();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan Student ID - Examination Venue Monitoring</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Font Awesome for sidebar icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Inbuilt CSS for scan page styling -->
    <style>
        /* Button Styling */
        .btn-primary {
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        /* Form Container */
        .form-container {
            max-width: 500px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-container form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .form-container form input[type="text"],
        .form-container form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 15px;
        }
        /* Message Styles */
        .error-message {
            color: #e74c3c;
            font-weight: bold;
            margin-top: 15px;
        }
        .success-message {
            color: #27ae60;
            font-weight: bold;
            margin-top: 15px;
        }
.scan-wrapper {
    display: flex;
    flex-direction: row;
    gap: 20px;
    align-items: flex-start;
}

.form-container, .student-details-container {
    flex: 1;
    min-width: 300px;
}

/* Student details card styling */
.student-details-card {
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: row;
    align-items: center;
}

.student-image {
    width: 150px;
    height: 200px;
    object-fit: cover;
    margin-right: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.details {
    text-align: left;
    width: 100%;
}

.details p {
    font-size: 16px;
    color: #333;
    margin: 8px 0;
}

.details h3 {
    font-size: 22px;
    color: #2c3e50;
    font-weight: bold;
    margin-bottom: 15px;
}

    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/atc_logo.png" alt="Exam System" class="logo">
        <h3>Dashboard</h3>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <?php if ($_SESSION['admin_role'] === 'invigilator') { ?>
                <li><a href="view_students.php" class="<?= ($current_page == 'view_students.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span> View Students</a></li>

                <li><a href="scan.php" class="<?= ($current_page == 'scan.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-barcode"></i></span> Scan Student ID</a></li>

                <li><a href="view_reports.php" class="<?= ($current_page == 'view_reports.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> View Statistics</a></li>
                <li><a href="logout.php">
                    <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Logout</a></li>

        <?php } elseif ($_SESSION['admin_role'] === 'admission_office') { ?>

                <li><a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a></li>
                
                <li><a href="assign_venue.php" class="<?= ($current_page == 'assign_venue.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-map-marker-alt"></i></span> Assign Venue</a></li>
                
                <li><a href="add_student.php" class="<?= ($current_page == 'add_student.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-user-plus"></i></span> Add/Delete Student</a></li>
                
                <li><a href="upload_student_image.php" class="<?= ($current_page == 'upload_student_image.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-camera"></i></span> Upload Student Image</a></li>
                
                <li><a href="view_students.php" class="<?= ($current_page == 'view_students.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span> View Students</a></li>
                
                <li><a href="view_reports.php" class="<?= ($current_page == 'view_reports.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> View Statistics</a></li>
                
                <li><a href="logout.php">
                    <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Logout</a></li>
            <?php } ?>
        </ul>
    </nav>
</aside>
        <!-- Main Content -->
<main class="main-content">
    <header class="content-header">
        <h1>Scan Student ID</h1>
    </header>
    
    <!-- Parent container using flex layout for side-by-side display -->
    <div class="scan-wrapper">
        <!-- Left Column: Form -->
        <div class="form-container">
            <form id="scanForm" method="post">
                <label for="venue">Select Venue:</label>
                <select name="venue" id="venue" required>
                    <option value="">-- Choose Venue --</option>
                    <?php foreach ($venues as $v): ?>
                        <option value="<?= htmlspecialchars($v['venue_name']); ?>" <?= ($selected_venue === $v['venue_name']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($v['venue_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
    
                <label for="admission_no">Enter Admission Number (Optional):</label>
                <input type="text" id="admission_no" name="admission_no" placeholder="Enter admission number manually">
    
                <div class="scan-container">
                    <h2>Barcode Scanner</h2>
                    <!-- Barcode input field that receives scanner input -->
                    <input type="text" id="barcode_input" name="barcode" placeholder="Scan barcode here..." autofocus>
                                    </div>
    
                <button type="button" id="scanButton" class="btn-primary">Scan Student</button>
            </form>
        </div>
    
        <!-- Right Column: Student Details -->
        <div class="student-details-container" id="student_details">
            <!-- This area will be filled by JS or by PHP server-side messages -->
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <?php if (!empty($info_message) && $student_info): ?>
                <div class="student-details-card">
                    <?php
                        // Build image URL.
                        $relative_path     = $student_info['image_path'];
                        $display_image_url = "../../" . $relative_path;
                        // Check if file exists; if not, use default image.
                        $absolute_path = __DIR__ . "/../../" . $relative_path;
                        if (!file_exists($absolute_path)) {
                            $display_image_url = "../assets/images/default_user.png";
                        }
                    ?>
                    <img src="<?= htmlspecialchars($display_image_url); ?>" alt="Student Image" class="student-image">
                    <div class="details">
                        <p><strong>Name:</strong> <?= htmlspecialchars($student_info['name']); ?></p>
                        <p><strong>Admission No:</strong> <?= htmlspecialchars($student_info['admission_no']); ?></p>
                        <p><strong>Exam No:</strong> <?= htmlspecialchars($student_info['exam_no'] ?? 'N/A'); ?></p>
                        <p><strong>Program:</strong> <?= htmlspecialchars($student_info['program']); ?></p>
                        <p><strong>Venue:</strong> <?= htmlspecialchars($student_info['venue']); ?></p>
                    </div>
                </div>
            <?php elseif (!empty($info_message) && !$student_info): ?>
                <p class="error-message"><?= htmlspecialchars($info_message); ?></p>
            <?php endif; ?>
        </div>
    </div> <!-- End .scan-wrapper -->
</main>
    </div>

    <!-- JavaScript to refresh/clear the form after scan -->
<script>
document.addEventListener("DOMContentLoaded", function() { 
    let barcodeInput   = document.getElementById("barcode_input");
    let admissionInput = document.getElementById("admission_no");
    let scanButton     = document.getElementById("scanButton");
    let studentDetails = document.getElementById("student_details");

    let defaultImageUrl = "../assets/images/default_user.png"; // adjust as needed

    function clearStudentDetails() {
        studentDetails.innerHTML = "";
    }

    function displayStudent(student) {
        let imageUrl = (student.image_path && student.image_path.trim() !== "")
            ? `../../${student.image_path}` 
            : defaultImageUrl;
            
        studentDetails.innerHTML = `
            <div class="student-details-card">
                <img src="${imageUrl}" alt="Student Image" class="student-image" onerror="this.src='${defaultImageUrl}';">
                <div class="details">
                    <h3>${student.name}</h3>
                    <p><strong>Admission No:</strong> ${student.admission_no}</p>
                    <p><strong>NTA Level:</strong> ${student.nta_level}</p>
                    <p><strong>Exam No:</strong> ${student.exam_no}</p>
                    <p><strong>Program:</strong> ${student.program}</p>
                    <p><strong>Venue:</strong> ${document.getElementById("venue").value.trim()}</p>
                </div>
            </div>
        `;
    }

    function triggerScan() {
        clearStudentDetails();
        let barcode = barcodeInput.value.trim();
        let admissionNo = admissionInput.value.trim();
        let venue = document.getElementById("venue").value.trim();

        if (!barcode && !admissionNo) {
            studentDetails.innerHTML = "<p class='error-message'>Please enter an admission number or barcode.</p>";
            return;
        }
        if (!venue) {
            studentDetails.innerHTML = "<p class='error-message'>Please select a venue.</p>";
            return;
        }
        
        // Build query string including venue parameter.
        let searchQuery = barcode 
                          ? `barcode=${encodeURIComponent(barcode)}&venue=${encodeURIComponent(venue)}`
                          : `admission_no=${encodeURIComponent(admissionNo)}&venue=${encodeURIComponent(venue)}`;
        console.log("Searching with query:", searchQuery);
    
        fetch("fetch_student.php?" + searchQuery)
            .then(response => response.json())
            .then(data => {
                console.log("Fetch response:", data);
                if (data.success) {
                    displayStudent(data.student);
                    barcodeInput.value = "";
                    admissionInput.value = "";
                } else {
                    studentDetails.innerHTML = "<p class='error-message'>Student not found!</p>";
                }
            })
            .catch(error => {
                console.error("Error fetching student:", error);
                studentDetails.innerHTML = "<p class='error-message'>Unexpected error occurred while searching.</p>";
            });
    }

    // Trigger scan on button click.
    if (scanButton) {
        scanButton.addEventListener("click", function(e) {
            e.preventDefault();
            triggerScan();
        });
    }

    // Optionally, listen for Enter key on the barcode input:
    barcodeInput.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            triggerScan();
        }
    });
});
</script>

</body>
</html>