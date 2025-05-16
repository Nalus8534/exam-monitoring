<?php
$plain_password = "atc123"; // Replace with your desired password
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Print the hashed password
echo "Hashed Password: " . $hashed_password;
?>
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