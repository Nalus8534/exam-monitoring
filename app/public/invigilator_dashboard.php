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

// Restrict access to invigilators only
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'invigilator') {
    header("Location: unauthorized.php");
    exit();
}

require_once __DIR__. '/../../config/db.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$dashboardLink = '';
if (isset($_SESSION['admin_role'])) {
    if ($_SESSION['admin_role'] === 'invigilator') {
        $dashboardLink = 'invigilator_dashboard.php';
    } elseif ($_SESSION['admin_role'] === 'admission_office') {
        $dashboardLink = 'admission_dashboard.php';
    }
    // You can add more roles as needed...
}
// Fetch venues from the database. Assumes your table has a column "venue_name".
$venues = [];
$query = "SELECT venue_name FROM venues";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $venues[] = $row;
    }
}
// Set the default or selected venue (passed via GET or first venue if available)
$selected_venue = (isset($_GET['venue']) && !empty($_GET['venue'])) 
    ? $_GET['venue'] 
    : (count($venues) > 0 ? $venues[0]['venue_name'] : '');
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invigilator Dashboard</title>
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <!-- Font Awesome and Google Fonts -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<script>
  window.addEventListener("pageshow", function(event) {
    if (event.persisted || window.performance && window.performance.navigation.type === 2) {
      // Reload the page if it was loaded from the cache.
      window.location.reload();
    }
  });
</script>


  <style>
  /* Global Base */
body {
  margin: 0;
  font-family: 'Roboto', sans-serif;
  background: #f4f6f8;
  color: #333;
  display: flex;
  min-height: 100vh;
}

/* Sidebar */
    .sidebar {
      width: 240px;
      background: #2c3e50;
      padding: 20px;
      color: #ecf0f1;
      flex-shrink: 0;
      min-height: 100vh;
    }
    .sidebar .logo {
      width: 120px;
      display: block;
      margin: 0 auto 15px;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
    }
    .sidebar nav ul {
      list-style: none;
      padding: 0;
    }
    .sidebar nav ul li {
      margin: 12px 0;
    }
    .sidebar nav ul li a {
      color: #bdc3c7;
      text-decoration: none;
      font-size: 16px;
      padding: 10px 12px;
      display: block;
      border-radius: 4px;
      transition: background 0.3s, color 0.3s;
    }
    .sidebar nav ul li a:hover,
    .sidebar nav ul li a.active {
      background: #34495e;
      color: #ecf0f1;
    }
    .nav-icon { margin-right: 8px; }
    
/* Admin Main Content */
.admin-main-content{
  flex: 1;
  padding: 40px;
  background-color:rgb(252, 243, 225);
}
/* Header Section */
.header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 30px;
}

.header h1 {
  font-size: 32px;
  color: #2c3e50;
  font-weight: bold;
  margin-bottom: 15px;
}

/* Back to Dashboard Link */
.header a.back-link {
  text-decoration: none;
  color: #fff;
  font-size: 18px;
  padding: 10px 15px;
  background: #3498db;
  border-radius: 6px;
  font-weight: bold;
  display: flex;
  align-items: center;
  transition: background 0.3s ease-in-out;
}

.header a.back-link i {
  margin-right: 8px;
}

.header a.back-link:hover {
  background: #2980b9;
}

/* Dashboard Tabs */
.card-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
  margin-top: 20px;
}

/* Tab Cards */
.card {
  background: #ffffff;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  cursor: pointer;
  text-align: center;
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.18);
}

/* Card Titles */
.card h3 {
  font-size: 22px;
  margin-bottom: 12px;
  color: #3498db;
  font-weight: bold;
}

/* Card Descriptions */
.card p {
  font-size: 16px;
  color: #7f8c8d;
}

/* Venue Card Special Styling */
.card.venue-card {
  text-align: left;
}

.card.venue-card label {
  font-size: 16px;
  margin-bottom: 5px;
  display: block;
  color: #2c3e50;
}

.card.venue-card select {
  width: 100%;
  padding: 10px;
  border: 1px solid #bdc3c7;
  border-radius: 6px;
  font-size: 16px;
  color: #2c3e50;
  transition: border-color 0.3s ease-in-out;
}

.card.venue-card select:focus {
  border-color: #3498db;
  outline: none;
}

/* Footer */
.footer {
  text-align: center;
  font-size: 16px;
  margin-top: 80px;
  color:rgb(77, 84, 85);
}

    .timer-container {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
    }
    .timer-container h2 {
      margin-top: 0;
    }
    .timer-section {
      margin-bottom: 20px;
    }
    label {
      display: inline-block;
      width: 150px;
    }
    select, button {
      padding: 8px 12px;
      margin: 5px 0;
    }
    p {
      margin: 8px 0;
    }
  </style>
</head>
<body>
  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <img src="../assets/images/atc_logo.png" alt="Exam System Logo" class="logo">
    <h2>Invigilator</h2>
    <nav>
      <ul>
        <li><a href="invigilator_dashboard.php" class="active"><i class="fas fa-home nav-icon"></i>Dashboard</a></li>
        <li><a href="view_students.php"><i class="fas fa-users nav-icon"></i>View Students</a></li>
        <li><a href="scan.php"><i class="fas fa-barcode nav-icon"></i>Scan Student ID</a></li>
        <li><a href="view_reports.php"><i class="fas fa-chart-bar nav-icon"></i>View Statistics</a></li>
        <li><a href="incident_reporting.php" class="active"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt nav-icon"></i>Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="admin-main-content">
    <!-- Header with Back to Dashboard Link -->
    <div class="header">
      <h1>Invigilator Dashboard</h1>
      <a href="invigilator_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
    
    <!-- Dashboard Cards Grid -->
    <div class="card-container">     
      <!-- Attendance -->
      <div class="card" onclick="location.href='attendance.php'">
        <h3>Attendance</h3>
        <p>Mark and review student attendance.</p>
      </div>
      
      <!-- Incident Reporting -->
      <div class="card" onclick="location.href='incident_reporting.php'">
        <h3>Incident Reporting</h3>
        <p>Report exam hall incidents and issues.</p>
      </div>

      <div class="card" onclick="window.location.href='exam_timer.php';">
        <h2>Exam Timer</h2>
        <p>Click here to start exam timer.<br>Set the exam duration, view start and end times, and monitor countdown.</p>

      </div>
      
      <!-- Venue Selection Card -->
      <div class="card venue-card">
        <h3>Current Venue</h3>
        <label for="venue-select">Select Venue:</label>
        <select id="venue-select" onchange="updateVenue(this.value)">
          <?php foreach ($venues as $v): ?>
            <option value="<?= htmlspecialchars($v['venue_name']); ?>" <?= ($selected_venue === $v['venue_name']) ? 'selected' : ''; ?>>
              <?= htmlspecialchars($v['venue_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
     
  <!-- JavaScript -->
  <script>
    function updateVenue(venue) {
      // Reload the page with the selected venue as a query parameter
      window.location.href = 'invigilator_dashboard.php?venue=' + encodeURIComponent(venue);
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
