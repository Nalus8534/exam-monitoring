<?php
$plain_password = "atc123"; // Replace with your desired password
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Print the hashed password
echo "Hashed Password: " . $hashed_password;
?>
