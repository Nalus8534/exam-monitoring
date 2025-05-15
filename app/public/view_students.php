<?php

session_start();
// Restrict access only to authorized users
if ($_SESSION['admin_role'] !== 'invigilator' && $_SESSION['admin_role'] !== 'admission_office') {
    header("Location: unauthorized.php");
    exit();
}
// view_students.php

// 1. Include the database connection
require_once __DIR__ . '/../../config/db.php';

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// 2. Get search terms and pagination variables from the GET parameters
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 15;
$offset = ($page - 1) * $records_per_page;

// 3. Build the dynamic WHERE clause for searching the students table
$where_clause = "";
$params = [];
$param_types = "";
if (!empty($search_term)) {
    // Create wildcards for partial matching
    $wild = "%" . $search_term . "%";
    // Search by name, admission number, exam number, program, or venue
    $where_clause = "WHERE s.name LIKE ? OR s.admission_no LIKE ? OR s.exam_no LIKE ? OR s.program LIKE ? OR s.venue LIKE ?";
    $params = array_fill(0, 5, $wild);
    $param_types = "sssss";
}

// 4. Count total records (for pagination)
$count_sql = "SELECT COUNT(s.id) as total FROM students s $where_clause";
if (!empty($where_clause)) {
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $result_count = $stmt->get_result();
    $count_row = $result_count->fetch_assoc();
    $total_records = $count_row['total'];
    $stmt->close();
} else {
    $result_count = $conn->query($count_sql);
    $count_row = $result_count->fetch_assoc();
    $total_records = $count_row['total'];
}
$total_pages = ceil($total_records / $records_per_page);

// 5. Build the main SELECT query using pagination and search conditions
$select_sql = "SELECT s.id as student_id, s.name as student_name, s.admission_no, s.exam_no, s.program, s.venue 
               FROM students s $where_clause 
               ORDER BY s.name ASC 
               LIMIT ?, ?";
               
if (!empty($where_clause)) {
    // Append two integers for LIMIT clause to our parameter types
    $param_types .= "ii";
    $params[] = $offset;
    $params[] = $records_per_page;
    $stmt = $conn->prepare($select_sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    // Bind all parameters; using splat operator to pass array values as individual arguments
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $result_stmt = $stmt->get_result();
} else {
    // No search; just bind LIMIT parameters
    $stmt = $conn->prepare($select_sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $offset, $records_per_page);
    $stmt->execute();
    $result_stmt = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Students - Examination Venue Monitoring</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Table styling */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
    }
    th {
      background-color: #f4f4f4;
    }
    td a{
      color:rgb(238, 111, 21);
    }
    /* Pagination styling */
    .pagination {
      margin-top: 20px;
      text-align: center;
    }
    .pagination a, .pagination span {
      display: inline-block;
      padding: 8px 12px;
      margin: 0 2px;
      border: 1px solid #ddd;
      color: #3498db;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.2s, color 0.2s;
    }
    .pagination a:hover {
      background-color: #ecf0f1;
      color: #2980b9;
    }
    .pagination .current-page {
      background-color: #3498db;
      color: #fff;
      border-color: #3498db;
    }
    .pagination .disabled {
      color: #bdc3c7;
      pointer-events: none;
      border-color: #ecf0f1;
    }
    /* Search Form styling */
    .search-form {
      margin-bottom: 20px;
      display: flex;
      gap: 10px;
      align-items: center;
    }
    .search-form input[type="text"] {
      flex-grow: 1;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body>
<div class="app-container">
  <!-- Sidebar can be included here if needed -->
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
      <h1 class="page-title"><i class="fas fa-users"></i> Registered Students</h1>
    </header>
    <div class="form-container">
      <!-- SEARCH FORM -->
      <form method="get" action="view_students.php" class="search-form">
        <input type="text" name="search" placeholder="Search by Name, Admission No, Exam No, Program, or Venue" value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Search</button>
        <?php if (!empty($search_term)): ?>
          <a href="view_students.php" class="btn-secondary"><i class="fas fa-times"></i> Clear Search</a>
        <?php endif; ?>
      </form>
      
      <!-- STUDENTS TABLE -->
      <div style="overflow-x: auto;">
        <table>
          <thead>
            <tr>
              <th><i class="fas fa-user"></i> Name</th>
              <th><i class="fas fa-id-card"></i> Admission No</th>
              <th><i class="fas fa-sort-numeric-up-alt"></i> Exam No</th>
              <th><i class="fas fa-graduation-cap"></i> Program</th>
              <th><i class="fas fa-building"></i> Assigned Venue</th>
              <th><i class="fas fa-cogs"></i> Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result_stmt && $result_stmt->num_rows > 0): ?>
              <?php while ($student = $result_stmt->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                  <td><?php echo htmlspecialchars($student['admission_no']); ?></td>
                  <td><?php echo htmlspecialchars($student['exam_no']); ?></td>
                  <td><?php echo htmlspecialchars($student['program']); ?></td>
                  <td><?php echo htmlspecialchars($student['venue']); ?></td>
                  <td>
                    <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="action-btn edit-btn">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6">No students found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      
      <!-- PAGINATION SECTION -->
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search_term); ?>">&laquo; Prev</a>
        <?php else: ?>
          <span class="disabled">&laquo; Prev</span>
        <?php endif; ?>
        
        <?php
          // Determine the range of page numbers to show
          $max_pages_to_show = 5;
          $start_page = max(1, $page - floor($max_pages_to_show / 2));
          $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);
          
          if ($start_page > 1) {
              echo '<a href="?page=1&search=' . urlencode($search_term) . '">1</a>';
              if ($start_page > 2) {
                echo '<span class="disabled">...</span>';
              }
          }
          for ($i = $start_page; $i <= $end_page; $i++):
              if ($i == $page):
                  echo '<span class="current-page">' . $i . '</span>';
              else:
                  echo '<a href="?page=' . $i . '&search=' . urlencode($search_term) . '">' . $i . '</a>';
              endif;
          endfor;
          if ($end_page < $total_pages) {
              if ($end_page < $total_pages - 1) {
                  echo '<span class="disabled">...</span>';
              }
              echo '<a href="?page=' . $total_pages . '&search=' . urlencode($search_term) . '">' . $total_pages . '</a>';
          }
        ?>
        
        <?php if ($page < $total_pages): ?>
          <a href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search_term); ?>">Next &raquo;</a>
        <?php else: ?>
          <span class="disabled">Next &raquo;</span>
        <?php endif; ?>
      </div>
      
    </div>
  </main>
</div>
</body>
</html>
