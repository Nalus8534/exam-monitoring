<?php
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
