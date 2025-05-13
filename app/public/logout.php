<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
if (session_destroy()) {
    // Prevent caching of the login page after logout
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Sat, 01 Jan 2000 00:00:00 GMT"); // Past date

    // Redirect to login page with a status message (optional)
    header("Location: login.php?status=logged_out");
    exit();
} else {
    // Handle error if session destruction fails
    error_log("Session destruction failed.");
    // Redirect to login anyway, but maybe with an error (optional)
    header("Location: login.php?error=logout_failed");
    exit();
}
?>
