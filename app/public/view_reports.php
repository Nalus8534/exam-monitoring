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
    die("Database Connection failed: " . $conn->connect_error);
    // In a real application, you might want to log the error and show a user-friendly message
    // die("An error occurred while connecting to the database.");
}


// Fetch venue reports
// Query venues – we fetch assigned_students directly along with a calculation for available seats.
$sql = "SELECT id, venue_name, capacity, assigned_students, (capacity - assigned_students) AS available_seats 
        FROM venues 
        ORDER BY venue_name ASC";
$result = $conn->query($sql);


// $conn->close(); // Close if no more queries on this page

$admin_username = htmlspecialchars($_SESSION['admin_username']);
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Reports - Examination Venue Monitoring</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <h1 class="page-title">Venue Utilization Reports</h1>
                 <div class="header-actions">
                     <a href="dashboard.php"><span class="nav-icon"><i class="fas fa-arrow-left"></i></span> Back to Dashboard</a>
                </div>
            </header>

            <div class="form-container"> <h3><i class="fas fa-clipboard-list"></i> Current Venue Status</h3>
                <?php if ($result && $result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-university"></i> Venue Name</th>
                                <th><i class="fas fa-users-cog"></i> Capacity</th>
                                <th><i class="fas fa-user-check"></i> Assigned Students</th>
                                <th><i class="fas fa-chair"></i> Available Seats</th>
                                <th><i class="fas fa-percentage"></i> Utilization</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                $utilization = 0;
                                if ($row['capacity'] > 0) {
                                    $utilization = round(($row['assigned_students'] / $row['capacity']) * 100, 1);
                                }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['venue_name']); ?></td>
                                    <td><?php echo $row['capacity']; ?></td>
                                    <td><?php echo $row['assigned_students']; ?></td>
                                    <td style="color: <?php echo ($row['available_seats'] == 0 ? '#e74c3c' : ($row['available_seats'] < 5 ? '#f39c12' : '#27ae60')); ?>; font-weight: bold;">
                                        <?php echo $row['available_seats']; ?>
                                    </td>
                                    <td>
                                        <div style="width: 100%; background-color: #e0e0e0; border-radius: 4px; overflow:hidden;">
                                            <div style="width: <?php echo $utilization; ?>%; background-color: <?php echo ($utilization > 90 ? '#e74c3c' : ($utilization > 70 ? '#f39c12' : '#27ae60')); ?>; color:white; text-align:center; padding: 2px 0; font-size:0.8em;">
                                                <?php echo $utilization; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="info-message">No venues found or data available to display reports.</p>
                <?php endif; ?>
                <?php if($result) $result->close(); $conn->close(); ?>
            </div>
        </main>
    </div>
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Arusha Technical College. All rights reserved.</p>
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
        // No file-specific JavaScript needed for view_reports.php
        // -------------------------------------------------------------------------------

    </script>
</body>
</html>
