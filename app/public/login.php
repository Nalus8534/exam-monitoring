<?php
session_start();
// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once __DIR__ . '/../../config/db.php';

// Check if the database connection was successful
if ($conn->connect_error) {
    // Log the error and show a user-friendly message
    error_log("Database Connection failed on login page: " . $conn->connect_error);
    // Instead of dying, maybe show an error message on the login page itself
    $error_message = "Could not connect to the database. Please try again later.";
    $conn = null; // Ensure $conn is null if connection failed
} else {
    $error_message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['username']) || empty($_POST['password'])) {
            $error_message = "Username and password are required.";
        } else {
            // Use $conn only if connection was successful
            if ($conn) {
                $username = $conn->real_escape_string($_POST['username']);
                $password_attempt = $_POST['password'];

                // Using prepared statements to prevent SQL injection
                // Ensure 'password_hash' column exists in your 'admins' table
                $stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
                if ($stmt) {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows == 1) {
                        $admin = $result->fetch_assoc();
                        // Verify the hashed password
                        if (password_verify($password_attempt, $admin['password_hash'])) {
                            // Password is correct, start session
                            $_SESSION['admin_id'] = $admin['id'];
                            $_SESSION['admin_username'] = $admin['username'];

                            // Redirect to dashboard
                            header("Location: dashboard.php");
                            exit();
                        } else {
                            // Invalid password
                            $error_message = "Invalid username or password.";
                        }
                    } else {
                        // Invalid username
                        $error_message = "Invalid username or password.";
                    }
                    $stmt->close();
                } else {
                    $error_message = "Database error during login process.";
                    error_log("Login statement preparation failed: " . $conn->error);
                }
            } // else case (connection failed) is handled above
        }
    }
}

// Close connection if it was opened successfully
if ($conn && !$conn->connect_error) {
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Examination Venue Monitoring</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php
    // The conflicting inline style block was here. It has been removed.
    // All necessary styles for the login page, including the password toggle,
    // should now be in style.css
    ?>
</head>
<body class="login-page">
    <div class="login-container">
        <img src="../assets/images/atc_logo.png" alt="College Logo" class="logo">
        <h2>Admin Login</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                 <label for="username">Username:</label>
                 <i class="fas fa-user input-icon"></i>
                <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="form-group">
                 <label for="password">Password:</label>
                 <i class="fas fa-lock input-icon"></i>
                <input type="password" id="password" name="password" required>
                <span class="password-toggle-icon" onclick="togglePasswordVisibility()">
                  <i class="fas fa-eye"></i>
                </span>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        // Toggles password visibility in the password field
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const icon = document.querySelector('.password-toggle-icon i');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        // Prevent form resubmission when user navigates back
        // This is important for login forms to avoid unintended login attempts
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <?php
    // The inline style block was here. It has been removed.
    ?>
</body>
</html>
