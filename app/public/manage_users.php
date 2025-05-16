<?php
require_once __DIR__ . '/../../config/db.php';

$query = "SELECT id, username, role FROM admins";
$result = $conn->query($query);

// Check if data was retrieved successfully
$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Store user records in the array
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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
<header class="page-header">
    <a href="admission_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
</header>

<h2>Manage Users</h2>
<p>View, add, edit, or remove user accounts.</p>

<table class="user-table">
    <thead>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= htmlspecialchars($user['role']); ?></td>
                    <td>
                        <button class="edit-btn">✏ Edit</button>
                        <button class="delete-btn">❌ Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No users found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<button class="action-btn">➕ Add New User</button>
🔹 What Changed?

<script>
document.getElementById("searchUser").addEventListener("input", function() {
    let query = this.value.toLowerCase();
    
    document.querySelectorAll("#userTable tr").forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(query) ? "" : "none";
    });
});
</script>

</body>
</html>
