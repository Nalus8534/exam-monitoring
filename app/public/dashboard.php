<?php
session_start();
// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT"); // A date in the past

// Restrict access only to authorized users
if ($_SESSION['admin_role'] !== 'invigilator' && $_SESSION['admin_role'] !== 'admission_office') {
    header("Location: unauthorized.php");
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../config/db.php';

// Check if the database connection was successful
if ($conn->connect_error) {
    // Log the error and show a user-friendly message
    error_log("Database Connection failed on dashboard: " . $conn->connect_error);
    $db_error = "Could not connect to the database to fetch dashboard data.";
    $conn = null; // Ensure $conn is null if connection failed
} else {
    $db_error = null;

    // --- Fetch Dashboard Data ---

    // 1. Total Registered Students
    $total_students = 0;
    $total_students_query = $conn->query("SELECT COUNT(*) as total FROM students");
    if ($total_students_query && $total_students_query->num_rows > 0) {
        $total_students = $total_students_query->fetch_assoc()['total'];
    }

    // 2. Total Configured Venues
    $total_venues = 0;
    $total_venues_query = $conn->query("SELECT COUNT(*) as total FROM venues");
    if ($total_venues_query && $total_venues_query->num_rows > 0) {
        $total_venues = $total_venues_query->fetch_assoc()['total'];
    }

    // 3. Students Not Yet Assigned to a Venue
    // Students in 'students' table but NOT in 'student_venues' table OR are in 'student_venues' but venue_id is NULL
    $unassigned_students = 0;
    $unassigned_students_query = $conn->query("SELECT COUNT(*) as total FROM students WHERE venue IS NULL OR venue = ''");
    if ($unassigned_students_query && $unassigned_students_query->num_rows > 0) {
         $unassigned_students = $unassigned_students_query->fetch_assoc()['total'];
    }


    // 4. Venues with Available Capacity
    $venues_with_space = 0;
    $venues_with_space_query = $conn->query("SELECT COUNT(id) as total FROM venues WHERE assigned_students < capacity");
     if ($venues_with_space_query && $venues_with_space_query->num_rows > 0) {
         $venues_with_space = $venues_with_space_query->fetch_assoc()['total'];
    }

    // 5. Venues that are Full
     $full_venues = 0;
    $full_venues_query = $conn->query("SELECT COUNT(id) as total FROM venues WHERE assigned_students >= capacity");
     if ($full_venues_query && $full_venues_query->num_rows > 0) {
         $full_venues = $full_venues_query->fetch_assoc()['total'];
    }


    // 6. Recent Student Assignments (e.g., last 10)
    $recent_assignments = [];
    // Select student name, admission no, and venue name for recent assignments
    $recent_assignments_sql = "SELECT s.name as student_name, s.admission_no, v.venue_name, sv.assignment_timestamp
                               FROM student_venues sv
                               JOIN students s ON sv.student_id = s.id
                               JOIN venues v ON sv.venue_id = v.id
                               ORDER BY sv.assignment_timestamp DESC
                               LIMIT 10"; // Get the 10 most recent
    $recent_assignments_result = $conn->query($recent_assignments_sql);
    if ($recent_assignments_result) {
        while ($row = $recent_assignments_result->fetch_assoc()) {
            $recent_assignments[] = $row;
        }
        $recent_assignments_result->close();
    }


    // Close connection after use
    $conn->close();
}


$admin_username = htmlspecialchars($_SESSION['admin_username']);

// Determine active page for sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Examination Venue Monitoring</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Additional styles for dashboard features */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); /* Responsive grid */
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #ecf0f1; /* Light grey */
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #3498db; /* Default accent */
        }
        .stat-card.students { border-left-color: #3498db; } /* Blue */
        .stat-card.venues { border-left-color: #2ecc71; } /* Green */
        .stat-card.unassigned { border-left-color: #e74c3c; } /* Red */
        .stat-card.available { border-left-color: #f1c40f; } /* Yellow */
        .stat-card.full { border-left-color: #9b59b6; } /* Purple */


        .stat-card h4 {
            margin-top: 0;
            font-size: 1.1em;
            color: #2c3e50;
        }
        .stat-card .stat-value {
            font-size: 2em;
            font-weight: bold;
            color: #3498db; /* Default color */
            margin-top: 10px;
        }
         .stat-card.students .stat-value { color: #3498db; }
         .stat-card.venues .stat-value { color: #2ecc71; }
         .stat-card.unassigned .stat-value { color: #e74c3c; }
         .stat-card.available .stat-value { color: #f1c40f; }
         .stat-card.full .stat-value { color: #9b59b6; }


        .quick-actions {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .quick-actions h3 {
            margin-top: 0;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .quick-action-buttons {
            display: flex;
            gap: 15px; /* Space between buttons */
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }
         .quick-action-buttons a {
            flex: 1 1 auto; /* Allow buttons to grow/shrink */
            min-width: 180px; /* Minimum width before wrapping */
            text-align: center;
            padding: 15px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
         }
         .quick-action-buttons a .fas {
            margin-right: 10px;
         }

        .btn-scan { background-color: #3498db; color: white; } /* Blue */
        .btn-scan:hover { background-color: #2980b9; transform: translateY(-2px); }

        .btn-assign { background-color: #2ecc71; color: white; } /* Green */
        .btn-assign:hover { background-color: #27ae60; transform: translateY(-2px); }

        .btn-add-student { background-color: #f1c40f; color: white; } /* Yellow */
        .btn-add-student:hover { background-color: #f39c12; transform: translateY(-2px); }


        .recent-activity {
             padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
         .recent-activity h3 {
            margin-top: 0;
            color: #2c3e50;
            margin-bottom: 15px;
         }
         .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
         }
         .activity-item {
            padding: 10px 0;
            border-bottom: 1px dashed #ecf0f1;
            font-size: 0.95em;
            color: #555;
         }
         .activity-item:last-child {
            border-bottom: none;
         }
         .activity-item strong {
            color: #333;
         }
         .activity-item .timestamp {
            font-size: 0.85em;
            color: #7f8c8d;
            margin-left: 10px;
         }

         /* Alert for unassigned students */
         .alert-warning {
             padding: 15px;
             background-color: #fef0db; /* Light orange/yellow */
             border: 1px solid #f1c40f; /* Yellow border */
             color: #e67e22; /* Darker orange text */
             border-radius: 5px;
             margin-bottom: 20px;
             font-weight: bold;
         }
         .alert-warning .fas {
             margin-right: 10px;
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
                
                <li><a href="scan.php" class="<?= ($current_page == 'scan.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="fas fa-barcode"></i></span> Scan Student ID</a></li>
                
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
                <h1 class="page-title">Dashboard</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo $admin_username; ?>!</span>
                    </div>
            </header>

            <?php if ($db_error): ?>
                 <p class="error-message"><i class="fas fa-database"></i> <?php echo $db_error; ?></p>
            <?php else: ?>

                <div class="form-container"> <h2>System Overview</h2>
                    <p>Welcome to the Examination Venue Monitoring System dashboard. Here's a quick overview of the system's status.</p>

                    <?php if ($unassigned_students > 0): ?>
                        <div class="alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> There are <strong><?php echo $unassigned_students; ?></strong> students not yet assigned to a venue.
                            <a href="assign_venue.php" style="color: #e67e22; text-decoration: underline; margin-left: 10px;">Assign Now</a>
                        </div>
                    <?php endif; ?>

                    <div class="dashboard-stats">
                        <div class="stat-card students">
                            <h4>Total Students</h4>
                            <div class="stat-value"><?php echo $total_students; ?></div>
                        </div>
                        <div class="stat-card venues">
                            <h4>Total Venues</h4>
                            <div class="stat-value"><?php echo $total_venues; ?></div>
                        </div>
                         <div class="stat-card unassigned">
                            <h4>Unassigned Students</h4>
                            <div class="stat-value"><?php echo $unassigned_students; ?></div>
                        </div>
                         <div class="stat-card available">
                            <h4>Venues with Space</h4>
                            <div class="stat-value"><?php echo $venues_with_space; ?></div>
                        </div>
                         <div class="stat-card full">
                            <h4>Full Venues</h4>
                            <div class="stat-value"><?php echo $full_venues; ?></div>
                        </div>
                    </div>

                    <div class="quick-actions">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                        <div class="quick-action-buttons">
                            <a href="scan.php" class="btn-scan"><i class="fas fa-barcode"></i> Scan Student ID</a>
                            <a href="assign_venue.php" class="btn-assign"><i class="fas fa-map-marker-alt"></i> Assign Venue</a>
                            <a href="add_student.php" class="btn-add-student"><i class="fas fa-user-plus"></i> Add New Student</a>
                            </div>
                    </div>

                     <div class="recent-activity">
                        <h3><i class="fas fa-history"></i> Recent Assignments</h3>
                        <?php if (!empty($recent_assignments)): ?>
                            <ul class="activity-list">
                                <?php foreach ($recent_assignments as $activity): ?>
                                    <li class="activity-item">
                                        <strong><?php echo htmlspecialchars($activity['student_name']); ?></strong> (<?php echo htmlspecialchars($activity['admission_no']); ?>) assigned to <strong><?php echo htmlspecialchars($activity['venue_name']); ?></strong>
                                        <span class="timestamp"><?php echo date('Y-m-d H:i', strtotime($activity['assignment_timestamp'])); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="info-message">No recent assignments found.</p>
                        <?php endif; ?>
                     </div>


                </div>

            <?php endif; // End of if(!db_error) ?>

        </main>
    </div>
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Arusha Technical College - Examination Venue Monitoring System. All rights reserved.</p>
    </footer>

    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

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

        // The cache control headers in PHP are the primary way to prevent back button access after logout.
        // This client-side script is less reliable for security but can sometimes help with perceived behavior
        // by forcing a reload if the page is loaded from the browser's cache.
        window.onpageshow = function(event) {
            if (event.persisted) {
                // If the page is loaded from cache (persisted), force a reload
                // This ensures the PHP session check runs when navigating back
                 window.location.reload();
            }
        };

        // --- File-Specific JavaScript (Keep this section if needed for a specific file) ---
        // No file-specific JavaScript needed for dashboard.php
        // -------------------------------------------------------------------------------

    </script>
</body>
</html>
