<?php
// Connect and fetch users from the database (admins table)
require_once __DIR__. '/../../config/db.php';

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

$query = "SELECT username, role FROM admins";
$result = $conn->query($query);
$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

<script>
  window.addEventListener("pageshow", function(event) {
    if (event.persisted || window.performance && window.performance.navigation.type === 2) {
      // Reload the page if it was loaded from the cache.
      window.location.reload();
    }
  });
</script>


  <!-- Internal CSS -->
  <style>
    /* Basic Reset */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f2f5;
      color: #333;
      padding: 20px;
      line-height: 1.6;
    }
    /* Header & Back Link */
    .page-header { margin-bottom: 20px; }
    .back-link {
      text-decoration: none;
      font-size: 16px;
      padding: 5px 10px;
      border: 1px solid #007bff;
      border-radius: 4px;
      color: #007bff;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .back-link:hover { background-color: #007bff; color: #fff; }
    /* Main Titles & Paragraphs */
    h2 {
      margin-bottom: 10px;
      font-size: 28px;
      text-align: center;
      color: #444;
    }
    p {
      margin-bottom: 20px;
      font-size: 16px;
      text-align: center;
    }
    /* Table Styling */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      background: #fff;
      border-radius: 4px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    table th, table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    table th { background-color: #007bff; color: #fff; font-size: 16px; }
    table tr:nth-child(even) { background-color: #f9f9f9; }
    table tr:hover { background-color: #f1f3f5; }
    /* Buttons */
    .action-btn {
      background-color: #28a745;
      color: #fff;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      display: inline-block;
      margin: 10px 0;
      transition: background-color 0.3s ease;
    }
    .action-btn:hover { background-color: #218838; }
    .edit-btn, .delete-btn {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 18px;
      /* Spacing to separate the buttons */
      padding: 0 8px;
    }
    .edit-btn { color: #ffc107; }
    .delete-btn { color: #dc3545; }
    /* Modal Styling */
    .modal {
      display: none;
      position: fixed;
      z-index: 100;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background: rgba(0,0,0,0.5);
    }
    .modal-content {
      background: #fff;
      margin: 10% auto;
      padding: 20px;
      width: 90%;
      max-width: 400px;
      border-radius: 8px;
      position: relative;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .modal-content h3 { margin-bottom: 15px; }
    .modal-content label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    .modal-content input[type="text"],
    .modal-content input[type="password"],
    .modal-content select {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .modal-content button { width: 100%; }
    .close {
      color: #aaa;
      font-size: 28px;
      font-weight: bold;
      position: absolute;
      right: 10px;
      top: 5px;
      cursor: pointer;
    }
    .close:hover,
    .close:focus { color: #000; }
    /* Responsive Design */
    @media (max-width: 480px) {
      h2 { font-size: 24px; }
      .action-btn { font-size: 14px; padding: 8px 12px; }
      table th, table td { padding: 8px 10px; }
    }
  </style>
</head>
<body>
  <header class="page-header">
    <a href="admission_dashboard.php" class="back-link">&larr; Back to Dashboard</a>
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
              <button class="edit-btn" data-username="<?= htmlspecialchars($user['username']); ?>" data-role="<?= htmlspecialchars($user['role']); ?>">&#9998;</button>
              <button class="delete-btn" data-username="<?= htmlspecialchars($user['username']); ?>">&#10060;</button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="3">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  
  <button class="action-btn" id="addUserBtn">➕ Add New User</button>
  
  <!-- Modal for Adding/Editing User -->
  <div id="userModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3 id="modalTitle">Add New User</h3>
      <form id="userForm">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
  
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" <?php /* required only for adding */ ?> >
  
        <label for="role">Role:</label>
        <!-- Options match those in your database -->
        <select name="role" id="role" required>
          <option value="">-- Select Role --</option>
          <option value="invigilator">invigilator</option>
          <option value="admission_office">admission_office</option>
        </select>
  
        <input type="hidden" name="action" id="action" value="add">
        <button type="submit" class="action-btn">Save User</button>
      </form>
    </div>
  </div>
  
  <!-- Internal JavaScript -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const modal = document.getElementById("userModal");
      const addUserBtn = document.getElementById("addUserBtn");
      const closeBtn = document.querySelector(".modal-content .close");
      const userForm = document.getElementById("userForm");
      const modalTitle = document.getElementById("modalTitle");
      const actionInput = document.getElementById("action");
      const usernameInput = document.getElementById("username");
      const passwordInput = document.getElementById("password");
      const roleSelect = document.getElementById("role");
  
      // Open modal for adding a new user
      addUserBtn.addEventListener("click", function() {
        modal.style.display = "block";
        modalTitle.textContent = "Add New User";
        actionInput.value = "add";
        userForm.reset();
        usernameInput.readOnly = false;
        // Ensure password field is required when adding a new user
        passwordInput.required = true;
      });
  
      // Attach event listener for edit buttons
      document.querySelectorAll(".edit-btn").forEach(function(btn) {
        btn.addEventListener("click", function() {
          const username = this.getAttribute("data-username");
          const role = this.getAttribute("data-role");
          modal.style.display = "block";
          modalTitle.textContent = "Edit User";
          actionInput.value = "edit";
          usernameInput.value = username;
          usernameInput.readOnly = true; // prevent editing the username
          roleSelect.value = role;
          passwordInput.value = ""; // leave password blank to keep unchanged
          // Make password optional when editing
          passwordInput.required = false;
        });
      });
  
      // Attach event listeners to delete buttons
      document.querySelectorAll(".delete-btn").forEach(function(btn) {
        btn.addEventListener("click", function() {
          const username = this.getAttribute("data-username");
          if (confirm(`Are you sure you want to delete user: ${username}?`)) {
            let formData = new FormData();
            formData.append("action", "delete");
            formData.append("username", username);
  
            fetch('process_user.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.text())
            .then(data => {
              alert(data);
              window.location.reload();
            })
            .catch(error => console.error('Error:', error));
          }
        });
      });
  
      // Handle form submission via AJAX
      userForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(userForm);
        fetch('process_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            window.location.reload();
        })
        .catch(error => console.error('Error:', error));
      });
  
      // Close modal when clicking the close button or outside the modal content
      closeBtn.addEventListener("click", function() {
        modal.style.display = "none";
      });
      window.addEventListener("click", function(event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      });
    });

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
