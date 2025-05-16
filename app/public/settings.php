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

// Connect to the database and retrieve current settings
require_once __DIR__. '/../../config/db.php';
// Set defaults in case the settings table is empty
$settings = [
  'theme'           => 'light',
  'security_level'  => 'medium'
];

$query = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('theme', 'security_level')";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
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
  <title>System Settings - Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

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
    /* Global Reset and Base Styles */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      transition: background-color 0.3s, color 0.3s;
      padding: 20px;
      line-height: 1.6;
    }
    /* Theme Styles */
    .theme-light {
      background-color: #f0f2f5;
      color: #333;
    }
    .theme-dark {
      background-color: #1a1a1a;
      color: #eee;
    }
    /* Header & Back Link */
    .page-header {
      margin-bottom: 20px;
    }
    .back-link {
      text-decoration: none;
      font-size: 16px;
      padding: 5px 10px;
      border: 1px solid #007bff;
      border-radius: 4px;
      color: #007bff;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .back-link:hover {
      background-color: #007bff;
      color: #fff;
    }
    /* Title & Description */
    h2 {
      margin-bottom: 10px;
      font-size: 28px;
      text-align: center;
      color: inherit;
    }
    p {
      margin-bottom: 20px;
      font-size: 16px;
      text-align: center;
      color: inherit;
    }
    /* Settings Container */
    .settings-container {
      max-width: 500px;
      margin: 0 auto;
      background: #fff;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .settings-container form {
      display: flex;
      flex-direction: column;
    }
    .settings-container label {
      font-size: 16px;
      margin-bottom: 5px;
    }
    .settings-container select,
    .settings-container input {
      font-size: 16px;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .settings-container button {
      background-color: #28a745;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .settings-container button:hover {
      background-color: #218838;
    }
    /* Popup Message */
    .popup {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #28a745;
      color: #fff;
      padding: 10px 20px;
      border-radius: 4px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      opacity: 0;
      transition: opacity 0.5s ease;
      z-index: 9999;
    }
    .popup.show {
      opacity: 1;
    }
    /* Responsive Design */
    @media (max-width: 480px) {
      h2 { font-size: 24px; }
      .settings-container { padding: 15px; }
    }
  </style>
</head>
<body class="<?= ($settings['theme'] == 'dark') ? 'theme-dark' : 'theme-light'; ?>">
  <!-- Back Link -->
  <header class="page-header">
    <a href="admission_dashboard.php" class="back-link">&larr; Back to Dashboard</a>
  </header>
  
  <h2>System Settings</h2>
  <p>Configure system preferences below.</p>
  
  <div class="settings-container">
    <form id="settingsForm">
      <!-- Theme Selection -->
      <label for="theme">Theme:</label>
      <select name="theme" id="theme" required>
        <option value="light" <?= ($settings['theme'] == 'light') ? 'selected' : ''; ?>>Light</option>
        <option value="dark" <?= ($settings['theme'] == 'dark') ? 'selected' : ''; ?>>Dark</option>
      </select>
      <!-- Security Level Selection -->
      <label for="security_level">Security Level:</label>
      <select name="security_level" id="security_level" required>
        <option value="low" <?= ($settings['security_level'] == 'low') ? 'selected' : ''; ?>>Low</option>
        <option value="medium" <?= ($settings['security_level'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
        <option value="high" <?= ($settings['security_level'] == 'high') ? 'selected' : ''; ?>>High</option>
      </select>
      <!-- Submit Button -->
      <button type="submit">Save Settings</button>
    </form>
  </div>
  
  <!-- Popup Success Message -->
  <div id="popupMessage" class="popup"></div>
  
  <!-- Internal JavaScript for AJAX Submission and Theme Refresh -->
  <script>
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent default form submission
      const formData = new FormData(this);
      // Submit the settings via fetch
      fetch('process_settings.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.text())
      .then(data => {
          // Show success message
          const popup = document.getElementById('popupMessage');
          popup.textContent = data;
          popup.classList.add('show');
          // Remove the popup after 3 seconds
          setTimeout(() => {
              popup.classList.remove('show');
          }, 3000);
          // Optionally, change the theme immediately by reloading the page
          // or by modifying the body class:
          document.body.className = (document.getElementById('theme').value === 'dark') 
            ? 'theme-dark' : 'theme-light';
      })
      .catch(error => {
          console.error('Error:', error);
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
