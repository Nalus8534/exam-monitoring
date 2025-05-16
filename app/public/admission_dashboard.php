<?php
session_start();

// Restrict access to admission office staff only
if ($_SESSION['admin_role'] !== 'admission_office') {
    header("Location: unauthorized.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission Office Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
/* Centered Header */
.admin-header {
    text-align: center;
    padding: 30px;
}

.admin-header h1 {
    font-size: 32px;
    font-weight: bold;
    color: #333;
}

.admin-instructions {
    font-size: 18px;
    color: #666;
    margin-bottom: 20px;
}

/* Admin Dashboard Tabs (Arranged in a Row) */
.admin-dashboard-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    padding: 20px;
}

/* Individual Tabs */
.admin-tab {
    background: #f1f3f5;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    text-align: center;
    width: 220px;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.admin-tab:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.admin-tab h2 {
    font-size: 20px;
    color: #333;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-dashboard-container {
        flex-direction: column;
        align-items: center;
    }

    .admin-tab {
        width: 80%;
    }
}

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

<!-- main content -->
 <main class="admin-main-content">
            <header class="page-header">
                <a href="admission_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
            </header>
        <header class="admin-header">
            <h1>Welcome, Admin</h1>
            <p class="admin-instructions">Select a tab from the sidebar to begin managing tasks.</p>
        </header>

        <!-- Responsive Grid Layout -->
        <div class="admin-dashboard-container">
            <div class="admin-panel" onclick="navigate('manage_users.php')">
                <h2>👥 Manage Users</h2>
                <p>View, add, or modify user roles.</p>
            </div>
            <div class="admin-panel" onclick="navigate('manage_exams.php')">
                <h2>📝 Manage Exams</h2>
                <p>Schedule, edit, or monitor exam sessions.</p>
            </div>
            <div class="admin-panel" onclick="navigate('view_reports.php')">
                <h2>📊 View Reports</h2>
                <p>Access performance analytics & logs.</p>
            </div>
            <div class="admin-panel" onclick="navigate('settings.php')">
                <h2>⚙ System Settings</h2>
                <p>Configure system preferences & security.</p>
            </div>
        </div>

    </main>

    </div>

<script>
function navigate(tab) {
    window.location.href = tab;
}
</script>

</body>
</html>
