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

// Fetch exam data (placeholder)
$exams = [
    ["id" => 101, "course" => "Mathematics", "date" => "2025-06-10", "venue" => "R12/13", "status" => "Scheduled"],
    ["id" => 102, "course" => "Physics", "date" => "2025-06-12", "venue" => "A203", "status" => "Ongoing"],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
    <title>Document</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
    <header class="page-header">
    <a href="admission_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
</header>

<h2>Manage Exams</h2>
<p>Schedule new exams, edit details, and monitor exam progress.</p>

<table class="exam-table">
    <thead>
        <tr>
            <th>Exam ID</th>
            <th>Course</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($exams as $exam): ?>
            <tr>
                <td><?= htmlspecialchars($exam['id']); ?></td>
                <td><?= htmlspecialchars($exam['course']); ?></td>
                <td><?= htmlspecialchars($exam['date']); ?></td>
                <td><?= htmlspecialchars($exam['venue']); ?></td>
                <td><?= htmlspecialchars($exam['status']); ?></td>
                <td>
                    <button class="edit-btn">✏ Edit</button>
                    <button class="delete-btn">❌ Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button class="action-btn">📅 Schedule New Exam</button>

</body>

<script>
      // Only apply forced reload if we are NOT on the login page:
  if (window.location.pathname.indexOf('login.php') === -1) {
    window.addEventListener("pageshow", function(event) {
      if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        window.location.reload();
      }
    });
  }
</script>

</html>

