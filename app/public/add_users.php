<?php
session_start();
if (!isset($_SESSION['admin_role'])) {
header("Location: /exam_monitoring/app/public/login.php");
exit();

}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $role);
    
    if ($stmt->execute()) {
        echo "User added successfully!";
    } else {
        echo "Failed to add user.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invigilator Dashboard</title>
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

    <script>
    // Use history.pushState to prevent the browser from loading a cached page when the back button is pressed.
    if (window.history && window.history.pushState) {
      window.history.pushState('forward', null, window.location.href);
      window.onpopstate = function () {
          window.history.pushState('forward', null, window.location.href);
          // Optionally, you could also force a redirect here:
          // window.location.href = "login.php";
      };
    }
  </script>

</head>
<body>
    <form method="POST">
    <label>Name:</label>
    <input type="text" name="name" required>
    
    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Role:</label>
    <select name="role">
        <option value="Admin">Admin</option>
        <option value="Invigilator">Invigilator</option>
    </select>

    <button type="submit">💾 Save User</button>
</form>
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
