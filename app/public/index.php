<?php
// index.php
// This file simply redirects to the login page.
// It's good practice to have an index.php in your main directories.

// If your login page is inside an 'admin' or 'public' folder:
header("Location: login.php"); // Assuming login.php is in the same 'public' folder
// If your login page is in the same directory as this index.php:
// header("Location: login.php");
exit();
?>
