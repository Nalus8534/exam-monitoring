<?php
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? '');
    
    if ($action === 'add') {
        if (empty($username) || empty($password) || empty($role)) {
            echo "All fields (username, password, role) are required for adding a user.";
            exit;
        }
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO admins (username, password_hash, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $passwordHash, $password, $role);
        if ($stmt->execute()) {
            echo "User added successfully!";
        } else {
            echo "Error adding user: " . $stmt->error;
        }
        $stmt->close();
    }
    elseif ($action === 'edit') {
        if (empty($username) || empty($role)) {
            echo "Username and role are required for editing a user.";
            exit;
        }
        // If a new password is provided, update both password and role; otherwise update role only.
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE admins SET role = ?, password = ?, password_hash = ? WHERE username = ?");
            $stmt->bind_param("ssss", $role, $password, $passwordHash, $username);
            if ($stmt->execute()) {
                echo "User updated successfully!";
            } else {
                echo "Error updating user: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE admins SET role = ? WHERE username = ?");
            $stmt->bind_param("ss", $role, $username);
            if ($stmt->execute()) {
                echo "User updated successfully!";
            } else {
                echo "Error updating user: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    elseif ($action === 'delete') {
        if (empty($username)) {
            echo "Username is required for deletion.";
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            echo "User deleted successfully!";
        } else {
            echo "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
