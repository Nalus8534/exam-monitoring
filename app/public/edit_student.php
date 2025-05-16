<?php
session_start();
if (!isset($_SESSION['admin_role'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Location: /exam_monitoring/app/public/login.php");
    exit();
}

// Prevent caching via PHP headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Restrict access only to authorized users
if ($_SESSION['admin_role'] !== 'invigilator' && $_SESSION['admin_role'] !== 'admission_office') {
    header("Location: unauthorized.php");
    exit();
}


// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '/../../config/db.php';

// Check if the database connection was successful
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
    // In a real application, you might want to log the error and show a user-friendly message
    // die("An error occurred while connecting to the database.");
}


$message = '';
$message_type = ''; // 'success' or 'error'
$student_id = 0;
$student_data = null;

// Define the list of programs (same list as add_student.php)
$programs = [
    'Ordinary Diploma In Automotive Engineering',
    'Ordinary Diploma In Auto-electrical And Electronics Engineering',
    'Ordinary Diploma In Civil And Highway Engineering',
    'Ordinary Diploma In Civil And Irrigation Engineering',
    'Ordinary Diploma In Civil Engineering',
    'Ordinary Diploma In Computer Science',
    'Ordinary Diploma In Cyber Security And Digital Forensic',
    'Ordinary Diploma In Electrical And Biomedical Engineering',
    'Ordinary Diploma In Electrical And Hydro Power Engineering',
    'Ordinary Diploma In Electrical And Solar Energy Engineering',
    'Ordinary Diploma In Electrical And Wind Energy Engineering',
    'Ordinary Diploma In Electrical Engineering',
    'Ordinary Diploma In Electronics And Telecommunication Engineering',
    'Ordinary Diploma In Heavy Duty Equipment Engineering',
    'Ordinary Diploma In Information Technology',
    'Ordinary Diploma In Laboratory Science And Technology',
    'Ordinary Diploma In Mechanical And Bio-ernergy Engineering',
    'Ordinary Diploma In Mechanical Engineering',
    'Ordinary Diploma In Pipe Works,oil And Gas Engineering',
    'Ordinary Dipolma In Instrumentation Engineering'
];


// --- Handle GET request to fetch student data for editing ---
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = intval($_GET['id']);

    // Fetch student data including the program
    $stmt = $conn->prepare("SELECT id, name, admission_no, exam_no, program FROM students WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $student_data = $result->fetch_assoc();
        } else {
            $message = "Student not found.";
            $message_type = 'error';
            // No student data, so no form will be shown below if $student_data is null
        }
        $stmt->close();
    } else {
        $message = "Database error preparing to fetch student: " . $conn->error;
        $message_type = 'error';
    }
}
// --- Handle POST request to update student data ---
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $name = trim($conn->real_escape_string($_POST['name']));
    $admission_no = trim($conn->real_escape_string($_POST['admission_no']));
    // Exam number label updated, but field can still be empty in DB if column allows NULL
    $exam_no = trim($conn->real_escape_string($_POST['exam_no']));
    // Get program from the dropdown
    $program = trim($conn->real_escape_string($_POST['program']));


    if (empty($student_id) || empty($name) || empty($admission_no) || empty($program)) {
        $message = "Student ID, Full Name, Admission Number, and Program are required.";
        $message_type = 'error';
        // To re-populate the form on error, use POSTed data
        $student_data = [
            'id' => $student_id,
            'name' => $_POST['name'],
            'admission_no' => $_POST['admission_no'],
            'exam_no' => $_POST['exam_no'],
            'program' => $_POST['program'] // Use the submitted program
        ];


    } else {
        // Check if admission number is being changed and if the new one already exists for another student
        $check_adm_stmt = $conn->prepare("SELECT id FROM students WHERE admission_no = ? AND id != ?");
        if ($check_adm_stmt) {
            $check_adm_stmt->bind_param("si", $admission_no, $student_id);
            $check_adm_stmt->execute();
            $check_adm_result = $check_adm_stmt->get_result();

            if ($check_adm_result->num_rows > 0) {
                $message = "Another student with this Admission Number already exists.";
                $message_type = 'error';
                // Repopulate form with submitted data
                $student_data = $_POST; // Contains student_id, name, admission_no etc.
                $student_data['id'] = $student_id; // Ensure ID is set for the form hidden field
            } else {
                // Proceed with update
                $update_stmt = $conn->prepare("UPDATE students SET name = ?, admission_no = ?, exam_no = ?, program = ? WHERE id = ?");
                if ($update_stmt) {
                    $update_stmt->bind_param("ssssi", $name, $admission_no, $exam_no, $program, $student_id);
                    if ($update_stmt->execute()) {
                        $message = "Student details updated successfully!";
                        $message_type = 'success';
                        // Fetch the updated data to display in the form
                        $stmt_fetch_updated = $conn->prepare("SELECT id, name, admission_no, exam_no, program FROM students WHERE id = ?");
                        if ($stmt_fetch_updated) {
                            $stmt_fetch_updated->bind_param("i", $student_id);
                            $stmt_fetch_updated->execute();
                            $student_data = $stmt_fetch_updated->get_result()->fetch_assoc();
                            $stmt_fetch_updated->close();
                        }
                    } else {
                        $message = "Error updating student: " . $update_stmt->error;
                        $message_type = 'error';
                        $student_data = $_POST; // Repopulate with submitted data
                        $student_data['id'] = $student_id;
                    }
                    $update_stmt->close();
                } else {
                    $message = "Database error (prepare update): " . $conn->error;
                    $message_type = 'error';
                    $student_data = $_POST; // Repopulate
                    $student_data['id'] = $student_id;
                }
            }
            $check_adm_stmt->close();
        } else {
            $message = "Database error (prepare check admission): " . $conn->error;
            $message_type = 'error';
            $student_data = $_POST; // Repopulate
            $student_data['id'] = $student_id;
        }
    }
}
// If neither GET with ID nor POST, it's an invalid access
elseif ($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET['id'])) {
     $message = "No student ID provided for editing.";
     $message_type = 'error';
}


$admin_username = htmlspecialchars($_SESSION['admin_username']);
$current_page = basename($_SERVER['PHP_SELF']); // Not directly used in sidebar, but good practice
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
    <title>Edit Student - Examination Venue Monitoring</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <script>
    // Use history.pushState to prevent the browser from loading a cached page when the back button is pressed.
    if (window.history && window.history.pushState) {
      window.history.pushState('forward', null, window.location.href);
      window.onpopstate = function () {
          window.history.pushState('forward', null, window.location.href);
          // Optionally, you could also force a redirect here:
          // window.location.href = "login.php";
      };
    }
  </script>

<style>
    .page-header {
    text-align: left;
    padding: 15px;
}

.back-link {
    font-size: 18px;
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
    transition: color 0.3s ease;
}

.back-link:hover {
    color: #0056b3;
}
</style>

</head>
<body>
    <div class="app-container">
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

        <main class="main-content">
            <header class="content-header">
                <h1 class="page-title"><i class="fas fa-user-edit"></i> Edit Student Details</h1>
            </header>

            <div class="form-container">
                <?php if (!empty($message)): ?>
                    <p class="<?php echo ($message_type == 'success') ? 'success-message' : 'error-message'; ?>">
                        <i class="fas fa-<?php echo ($message_type == 'success') ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo $message; ?>
                    </p>
                <?php endif; ?>

                <?php if ($student_data): // Only show form if student data was fetched ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_data['id']); ?>">

                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Full Name:</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($student_data['name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="admission_no"><i class="fas fa-id-card"></i> Admission Number:</label>
                        <input type="text" id="admission_no" name="admission_no" required value="<?php echo htmlspecialchars($student_data['admission_no']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="exam_no"><i class="fas fa-sort-numeric-up-alt"></i> Exam Number (Optional):</label>
                        <input type="text" id="exam_no" name="exam_no" value="<?php echo htmlspecialchars($student_data['exam_no'] ?? ''); ?>">
                    </div>
                     <div class="form-group">
                        <label for="program"><i class="fas fa-graduation-cap"></i> Program:</label>
                        <select id="program" name="program" required>
                            <option value="">-- Select Program --</option>
                            <?php
                            // Loop through the programs array to create options
                            foreach ($programs as $prog) {
                                // Check if this program matches the student's current program to pre-select it
                                $selected = ($student_data['program'] == $prog) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($prog) . "\" {$selected}>" . htmlspecialchars($prog) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                </form>
                <?php elseif (empty($message_type) || $message_type === 'error' && !isset($_GET['id'])): // If no ID was provided initially and no other message is set ?>
                     <p class="error-message"><i class="fas fa-exclamation-triangle"></i> Invalid request. No student specified for editing.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Arusha Technical College - Examination Venue Monitoring System. All rights reserved.</p>
    </footer>
    <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    <script>
        // Function to toggle the sidebar's collapsed state
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            // Toggle the 'collapsed' class on the sidebar element
            sidebar.classList.toggle('collapsed');
            // Toggle the 'sidebar-collapsed' class on the main content element
            // This class controls the left margin of the main content
            mainContent.classList.toggle('sidebar-collapsed');
        }
          // Only apply forced reload if we are NOT on the login page:
    if (window.location.pathname.indexOf('login.php') === -1) {
        window.addEventListener("pageshow", function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
        });
    }

    </script>
</body>
</html>
