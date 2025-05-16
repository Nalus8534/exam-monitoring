<?php
require_once __DIR__. '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = trim($_POST['theme'] ?? '');
    $security_level = trim($_POST['security_level'] ?? '');

    // Validate that required fields are present.
    if (empty($theme) || empty($security_level)) {
        echo "All fields are required.";
        exit;
    }
    
    // Update the "theme" setting
    $stmt1 = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'theme'");
    $stmt1->bind_param("s", $theme);
    $stmt1->execute();
    $stmt1->close();

    // Update the "security_level" setting
    $stmt2 = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'security_level'");
    $stmt2->bind_param("s", $security_level);
    $stmt2->execute();
    $stmt2->close();

    echo "Settings updated successfully!";
}
$conn->close();
?>
