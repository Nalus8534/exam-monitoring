<?php
require_once __DIR__ . '/../../config/db.php';

header("Content-Type: application/json"); // Ensure proper JSON response

if (!isset($_FILES['id_image']) || $_FILES['id_image']['error'] !== UPLOAD_ERR_OK) {
    error_log("File upload error: " . $_FILES['id_image']['error']);
    echo json_encode(["success" => false, "error" => "Invalid file upload"]);
    exit();
}

$imagePath = $_FILES['id_image']['tmp_name'];

$command = "C:\\Program Files (x86)\\ZBar\\bin\\zbarimg.exe --quiet --raw " . escapeshellarg($imagePath);
$output = shell_exec($command);
$barcode = trim($output);

if (!empty($barcode)) {
    echo json_encode(["success" => true, "barcode" => $barcode]);
} else {
    error_log("No barcode detected in image.");
    echo json_encode(["success" => false, "error" => "No barcode detected"]);
}
?>
