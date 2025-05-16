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
header("Location: /exam_monitoring/app/public/login.php");
exit();

}

if (!isset($_SESSION['admin_id'])) {
header("Location: /exam_monitoring/app/public/login.php");
exit();

}

// Include database connection
require_once __DIR__ . '/../../config/db.php';

if ($conn->connect_error) {
    error_log("Database Connection failed on assign venue page: " . $conn->connect_error);
    die("Database Connection failed: Could not connect to the database. Please try again later.");
}

$message = '';
$message_type = ''; // 'success' or 'error'

// Define the list of allowed venues
$allowed_venues = [
    'R12/13',
    'UG06',
    'UG07',
    'G11',
    'T12',
    'T11',
    'S10',
    'T10',
    'F12',
    'H/WAY',
    'US02',
    'UF05',
    'UF01',
    'DH'
];

// Create a comma‐separated string for SQL filtering on allowed venues
$allowed_venues_string = "'" . implode("','", array_map(function($value) use ($conn) {
    return $conn->real_escape_string($value);
}, $allowed_venues)) . "'";

// -------------------------------------------------------------------------
// FETCH UNASSIGNED STUDENTS
// A student is considered unassigned if their `venue` value is NULL or empty.
$students_sql = "SELECT id, name, admission_no FROM students 
                 WHERE venue IS NULL OR TRIM(venue) = '' 
                 ORDER BY name ASC";
$students_result = $conn->query($students_sql);
if ($students_result === FALSE) {
    error_log("Error fetching students: " . $conn->error);
    $message = "Error fetching student list.";
    $message_type = 'error';
}

// -------------------------------------------------------------------------
// FETCH AVAILABLE VENUES
// Only select allowed venues that have space (i.e. assigned_students < capacity)
$venues_sql = "SELECT id, venue_name, capacity, assigned_students 
               FROM venues 
               WHERE venue_name IN ($allowed_venues_string) 
                 AND assigned_students < capacity 
               ORDER BY venue_name ASC";
$venues_result = $conn->query($venues_sql);
if ($venues_result === FALSE) {
    error_log("Error fetching venues: " . $conn->error);
    $message = "Error fetching venue list.";
    $message_type = 'error';
}

// -------------------------------------------------------------------------
// HANDLE FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the student_id and venue_id from the POST data
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $venue_id = isset($_POST['venue_id']) ? intval($_POST['venue_id']) : 0;
    
    if (empty($student_id) || empty($venue_id)) {
        $message = "Please select both a student and a venue.";
        $message_type = 'error';
    } else {
        // Double-check the venue's availability
        $venue_check_stmt = $conn->prepare("SELECT capacity, assigned_students 
                                             FROM venues 
                                             WHERE id = ? AND venue_name IN ($allowed_venues_string)");
        if ($venue_check_stmt) {
            $venue_check_stmt->bind_param("i", $venue_id);
            $venue_check_stmt->execute();
            $venue_data_result = $venue_check_stmt->get_result();
            $venue_data = $venue_data_result->fetch_assoc();
            $venue_check_stmt->close();
            
            if ($venue_data && $venue_data['assigned_students'] < $venue_data['capacity']) {
                // Check if the student already has an assignment.
                // Since we list only unassigned students, this is a safety net.
                $check_assign_stmt = $conn->prepare("SELECT id 
                                                     FROM student_venues 
                                                     WHERE student_id = ?");
                if ($check_assign_stmt) {
                    $check_assign_stmt->bind_param("i", $student_id);
                    $check_assign_stmt->execute();
                    $check_result = $check_assign_stmt->get_result();
                    
                    // If the student is already assigned, update their record; else, insert a new one.
                    if ($check_result->num_rows > 0) {
                        $assign_stmt = $conn->prepare("UPDATE student_venues 
                                                       SET venue_id = ?, assignment_timestamp = CURRENT_TIMESTAMP 
                                                       WHERE student_id = ?");
                        if ($assign_stmt) {
                            $assign_stmt->bind_param("ii", $venue_id, $student_id);
                        }
                    } else {
                        $assign_stmt = $conn->prepare("INSERT INTO student_venues (student_id, venue_id, assignment_timestamp) 
                                                       VALUES (?, ?, CURRENT_TIMESTAMP)");
                        if ($assign_stmt) {
                            $assign_stmt->bind_param("ii", $student_id, $venue_id);
                        }
                    }
                    $check_assign_stmt->close();

                    if (isset($assign_stmt)) {
                        if ($assign_stmt->execute()) {
                            // Update assigned_students count in the venues table
                            $update_venue_stmt = $conn->prepare("UPDATE venues 
                                                                  SET assigned_students = assigned_students + 1 
                                                                  WHERE id = ?");
                            if ($update_venue_stmt) {
                                $update_venue_stmt->bind_param("i", $venue_id);
                                $update_venue_stmt->execute();
                                $update_venue_stmt->close();
                            }
                            
                            // Also, update the student's record in the students table so that their venue becomes set
                            $update_student_stmt = $conn->prepare("UPDATE students 
                                                                   SET venue = (SELECT venue_name FROM venues WHERE id = ?)
                                                                   WHERE id = ?");
                            if ($update_student_stmt) {
                                $update_student_stmt->bind_param("ii", $venue_id, $student_id);
                                $update_student_stmt->execute();
                                $update_student_stmt->close();
                            }
                            
                            $message = "Student has been successfully assigned to the venue.";
                            $message_type = 'success';
                            
                            // Re-fetch unassigned students and available venues after successful assignment
                            $students_result = $conn->query($students_sql);
                            $venues_result   = $conn->query($venues_sql);
                        } else {
                            $message = "Error assigning student: " . $assign_stmt->error;
                            $message_type = 'error';
                            error_log("Error executing assignment: " . $assign_stmt->error);
                        }
                        $assign_stmt->close();
                    } else {
                        $message = "Error preparing assignment statement: " . $conn->error;
                        $message_type = 'error';
                        error_log("Error preparing assignment statement: " . $conn->error);
                    }
                } else {
                    $message = "Error preparing student assignment check: " . $conn->error;
                    $message_type = 'error';
                    error_log("Error preparing student assignment check: " . $conn->error);
                }
            } else {
                $message = "The selected venue is full or does not exist in the allowed list.";
                $message_type = 'error';
            }
        } else {
            $message = "Error checking venue details: " . $conn->error;
            $message_type = 'error';
            error_log("Error preparing venue details check: " . $conn->error);
        }
    }
}

// Optional: you can use the following session variable to display the logged admin's username:
$admin_username = htmlspecialchars($_SESSION['admin_username']);

// Determine current page for sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Assign Venue - Examination Venue Monitoring</title>
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
      <h1 class="page-title">Assign Student to Venue</h1>
                  <header class="page-header">
                <a href="admission_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
            </header>
    </header>
    <div class="form-container">
      <?php if (!empty($message)): ?>
        <p class="<?= ($message_type == 'success') ? 'success-message' : 'error-message'; ?>">
          <?= $message; ?>
        </p>
      <?php endif; ?>
      <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
          <label for="student_id"><i class="fas fa-user-graduate"></i> Select Student:</label>
          <select id="student_id" name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php
            // List only students without an assigned venue.
            if ($students_result && $students_result->num_rows > 0) {
                while ($student = $students_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($student['id']) . "'>" . htmlspecialchars($student['name']) . " (" . htmlspecialchars($student['admission_no']) . ")</option>";
                }
            } else {
                echo "<option value='' disabled>No unassigned students available</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label for="venue_id"><i class="fas fa-university"></i> Select Venue:</label>
          <select id="venue_id" name="venue_id" required>
            <option value="">-- Select Venue --</option>
            <?php
            if ($venues_result && $venues_result->num_rows > 0) {
                while ($venue = $venues_result->fetch_assoc()) {
                    $available_slots = $venue['capacity'] - $venue['assigned_students'];
                    echo "<option value='" . htmlspecialchars($venue['id']) . "'>" . htmlspecialchars($venue['venue_name']) . " (Available: $available_slots)</option>";
                }
            } else {
                // If no venues available, check if any allowed venues exist in DB.
                $check_any_venues_sql = "SELECT COUNT(*) as total FROM venues WHERE venue_name IN ($allowed_venues_string)";
                $check_any_venues_result = $conn->query($check_any_venues_sql);
                $total_allowed_venues_in_db = 0;
                if ($check_any_venues_result && $check_any_venues_result->num_rows > 0) {
                    $total_allowed_venues_in_db = $check_any_venues_result->fetch_assoc()['total'];
                    $check_any_venues_result->close();
                }
                if ($total_allowed_venues_in_db == 0) {
                    echo "<option value='' disabled>No allowed venues found in the database.</option>";
                } else {
                    echo "<option value='' disabled>All allowed venues are currently full.</option>";
                }
            }
            ?>
          </select>
        </div>
        <button type="submit" class="btn-primary"><i class="fas fa-check-circle"></i> Assign Venue</button>
      </form>
    </div>
  </main>
</div>
<footer class="footer">
  <p>&copy; <?php echo date("Y"); ?> Arusha Technical College. All rights reserved.</p>
</footer>
<button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
<script>
function toggleSidebar() {
    var sidebar = document.querySelector('.sidebar');
    var mainContent = document.querySelector('.main-content');
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('sidebar-collapsed');
}
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};

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