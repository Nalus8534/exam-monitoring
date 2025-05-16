<?php
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <a href="admin_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
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
</html>

