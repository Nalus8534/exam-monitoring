<?php
session_start();

// Restrict access to invigilators only
if ($_SESSION['admin_role'] !== 'invigilator') {
    header("Location: unauthorized.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invigilator Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
/* Main Container */
.dashboard-container {
    display: flex;
    gap: 20px;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
}

/* Venue Selection */
.venue-selection {
    margin-bottom: 20px;
}

/* Chart Section */
.chart-section {
    flex: 1;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

/* Student Table */
.student-table {
    flex: 1;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.student-table table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.student-table th, .student-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.student-table th {
    background: #6c757d;
    color: #fff;
}


/* 🎓 Exam Overview (Balanced Light Theme) */
.exam-overview {
    flex: 1;
    background: #f8f9fa; /* Light Gray */
    color: #333;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Subtle Depth */
    text-align: left;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 5px solid #6c757d; /* Neutral Accent */
}

.exam-overview:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.exam-overview h2 {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 12px;
    color: #000;
}

.exam-overview p {
    font-size: 16px;
    color: #444;
    margin: 8px 0;
    font-weight: 500;
}

/* ⚡ Quick Actions (Balanced Light Theme) */
.quick-actions {
    flex: 1;
    background: #f1f3f5; /* Slightly darker gray */
    color: #333;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 5px solid #adb5bd;
}

.quick-actions:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.quick-actions h2 {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 12px;
    color: #000;
}

/* 🎯 Action Buttons */
.action-btn {
    display: block;
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    background: #6c757d; /* Neutral Gray */
    color: #fff;
    border: 2px solid #495057;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Hover & Click Effects */
.action-btn:hover {
    background: #495057;
    color: #fff;
}

.action-btn:active {
    transform: scale(0.97);
}

/* 🎥 Live Picture Feed (Optional) */
.live-picture {
    width: 100%;
    height: 200px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #444;
    margin-bottom: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

/* Live Picture Section */
.live-section {
    text-align: center;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

    </style>
</head>
<body>

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
            <h1>Welcome, Invigilator</h1>
        </header>

        <!-- Main Dashboard Wrapper -->
        <div class="dashboard-container">
            
            <!-- Exam Overview Section -->
            <section class="exam-overview">
                <h2>Ongoing Exam Sessions</h2>
                <p><strong>Current Date:</strong> <span id="current-date"></span></p>
                <p><strong>Active Exams:</strong> <span id="active-exams">5</span></p>
                <p><strong>Venue Assigned:</strong> <span id="assigned-venue">R12/13</span></p>
                <p><strong>Registered Students:</strong> <span id="total-students">45</span></p>
                <p><strong>Checked-in:</strong> <span id="checked-in">38</span></p>
            </section>

            <!-- Quick Actions Section -->
            <section class="quick-actions">
                <h2>Quick Actions</h2>
                <button class="action-btn" id="scan-btn">🔍 Scan Student ID</button>
                <button class="action-btn" id="view-students-btn">📋 View Registered Students</button>
                <button class="action-btn" id="exam-guidelines-btn">📖 Exam Guidelines</button>
                <button class="action-btn" id="report-incident-btn">⚠️ Report Incident</button>
            </section>
        <!-- Chart Section -->
        <section class="chart-section">
            <canvas id="studentChart"></canvas>
        </section>

        <!-- Student Table -->
        <section class="student-table">
                <select name="venue" id="venue" required>
                    <option value="">-- Choose Venue --</option>
                    <?php foreach ($venues as $v): ?>
                        <option value="<?= htmlspecialchars($v['venue_name']); ?>" <?= ($selected_venue === $v['venue_name']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($v['venue_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <table>
                <thead>
                    <tr>
                        <th>Admission No</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Exam No</th>
                    </tr>
                </thead>
                <tbody id="studentData">
                    <!-- Dynamic student data fills here -->
                </tbody>
            </table>
        </section>


        </div> <!-- End .dashboard-container -->

    </main>

    <script>
        // Automatically set the current date in the Exam Overview section
        document.getElementById("current-date").textContent = new Date().toDateString();
    </script>
    
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js Library -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    updateStudentData();
});

// Function to fetch and update student data based on venue selection
function updateStudentData() {
    let venue = document.getElementById("venue").value;
    document.getElementById("selected-venue").textContent = venue || "Venue";

    fetch(`fetch_students.php?venue=${encodeURIComponent(venue)}`)
    .then(response => response.json())
    .then(data => {
        updateStudentTable(data.students);
        updateChart(data.stats);
    });
}

// Function to update the student list
function updateStudentTable(students) {
    let tableBody = document.getElementById("studentData");
    tableBody.innerHTML = "";

    students.forEach(student => {
        let row = `<tr>
            <td>${student.admission_no}</td>
            <td>${student.name}</td>
            <td>${student.program}</td>
            <td>${student.exam_no}</td>
        </tr>`;
        tableBody.innerHTML += row;
    });
}

// Function to update the chart visualization
function updateChart(stats) {
    let ctx = document.getElementById("studentChart").getContext("2d");
    if (window.studentChart) window.studentChart.destroy(); // Clear old chart

    window.studentChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: Object.keys(stats),
            datasets: [{
                label: "Students per Venue",
                data: Object.values(stats),
                backgroundColor: "#6c757d",
                borderColor: "#444",
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
</script>

</body>
</html>
