<?php
// Default settings (placeholder)
$theme = "light";
$security_level = "medium";
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

<h2>System Settings</h2>
<p>Configure system preferences, security settings, and database updates.</p>

<div class="settings-container">
    <form method="post" action="save_settings.php">
        <label for="theme">Theme:</label>
        <select id="theme" name="theme">
            <option value="light" <?= ($theme == "light") ? "selected" : ""; ?>>Light</option>
            <option value="dark" <?= ($theme == "dark") ? "selected" : ""; ?>>Dark</option>
        </select>

        <label for="security">Security Level:</label>
        <select id="security" name="security_level">
            <option value="low" <?= ($security_level == "low") ? "selected" : ""; ?>>Low</option>
            <option value="medium" <?= ($security_level == "medium") ? "selected" : ""; ?>>Medium</option>
            <option value="high" <?= ($security_level == "high") ? "selected" : ""; ?>>High</option>
        </select>

        <button type="submit" class="action-btn">💾 Save Settings</button>
    </form>
</div>

</body>
</html>