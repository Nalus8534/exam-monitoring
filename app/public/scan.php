<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// Retrieve venue options dynamically from the venues table.
$venues = [];
$resultVenues = $conn->query("SELECT venue_name FROM venues ORDER BY venue_name ASC");
if ($resultVenues) {
    while ($row = $resultVenues->fetch_assoc()) {
        $venues[] = $row;
    }
}

// Initialize variables for form submission and results.
$student_info      = null;
$error_message     = "";
$info_message      = "";
$selected_venue    = "";
$entered_admission = "";

// Process form submission (venue and admission number)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selected_venue    = isset($_POST['venue']) ? trim($conn->real_escape_string($_POST['venue'])) : "";
    $entered_admission = isset($_POST['admission_no']) ? trim($conn->real_escape_string($_POST['admission_no'])) : "";
    
    if (empty($selected_venue)) {
        $error_message = "Please select a venue.";
    } elseif (empty($entered_admission)) {
        $error_message = "Admission number cannot be empty.";
    } else {
        // Prepare query to fetch student details filtered by admission number and venue.
        $stmt = $conn->prepare("SELECT s.name, s.admission_no, s.exam_no, s.program, s.image_path, s.venue 
                                FROM students s 
                                WHERE s.admission_no = ? AND s.venue = ?");
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
        /* Student Details Card - Flex Layout for Passport Photo Style */
        .student-details-card {
            margin-top: 20px;
            background: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: row;
            align-items: flex-start;
        }
        /* Passport-Style Student Image */
        .student-image {
            width: 150px;
            height: 200px;
            object-fit: cover;
            margin-right: 20px;
            border: 1px solid #ccc;
            /* Passport style: rectangular, no rounded corners */
            border-radius: 0;
        }
        /* Details Section - Left Aligned */
        .details {
            text-align: left;
            width: 100%;
        }
        .details p {
            font-size: 16px;
            color: #333;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/atc_logo.png" alt="College Logo" class="logo">
                <h3>ATC Exam System</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="scan.php" class="<?= ($current_page == 'scan.php') ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fas fa-barcode"></i></span> Scan Student ID
                        </a>
                    </li>
                    <li>
                        <a href="assign_venue.php" class="<?= ($current_page == 'assign_venue.php') ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fas fa-map-marker-alt"></i></span> Assign Venue
                        </a>
                    </li>
                    <li><a href="add_student.php" class="<?= ($current_page == 'add_student.php') ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fas fa-user-plus"></i></span> Add/Delete Student</a></li>
                    <li>
                        <a href="upload_student_image.php" class="<?= ($current_page == 'upload_student_image.php') ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fas fa-camera"></i></span> Upload Student Image
                        </a>
                    </li>
                    <li>
                        <a href="view_students.php" class="<?= ($current_page == 'view_students.php') ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fas fa-users"></i></span> View Students
                        </a>
                    </li>
                    <li>
                        <a href="view_reports.php" class="<?= ($current_page == 'view_reports.php') ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> View Statistics
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1>Scan Student ID</h1>
            </header>
            <div class="form-container">
                <form id="scanForm" method="post" action="scan.php">
                    <label for="venue">Select Venue:</label>
                    <select name="venue" id="venue" required>
                        <option value="">-- Choose Venue --</option>
                        <?php foreach ($venues as $v): ?>
                            <option value="<?= htmlspecialchars($v['venue_name']); ?>" <?= ($selected_venue === $v['venue_name']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($v['venue_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="admission_no">Enter Admission Number:</label>
                    <input type="text" id="admission_no" name="admission_no" placeholder="Enter admission number" value="<?= htmlspecialchars($entered_admission); ?>" required autofocus>
                    <button type="submit" class="btn-primary">Scan Student</button>
                </form>
                <?php if (!empty($error_message)): ?>
                    <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
                <?php endif; ?>
                <?php if (!empty($info_message) && $student_info): ?>
                    <div class="student-details-card">
                        <?php
                        // Build the image display URL.
                        // The stored value is something like "uploads/4504.jpg" and we need to prepend "../../"
                        // because scan.php is in app/public
                        $relative_path      = $student_info['image_path'];
                        $display_image_url  = "../../" . $relative_path;
                        
                        // Build an absolute file path for checking existence.
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
        </main>
    </div>
    <!-- JavaScript to refresh/clear the form after scan -->
    <script>
        // If a scan result is shown (either success or error), reload the page after 3 seconds.
        <?php if (!empty($info_message) || !empty($error_message)): ?>
            setTimeout(function(){
                // Reload the page without query parameters
                window.location.href = window.location.pathname;
            }, 3000);
        <?php endif; ?>

        // Clear form inputs if the page is navigated back via the browser's back button
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                document.getElementById("scanForm").reset();
            }
        });
    </script>
</body>
</html>
