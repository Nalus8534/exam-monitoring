<?php
// Temporary script to generate a password hash
$password = 'atc123'; // <-- **CHANGE THIS** to the password you want for your admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Your hashed password is: " . $hashed_password;
?>