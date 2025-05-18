<?php
$admin_password = "atc123"; // Change this to the actual password
$invigilator_password = "lct123";

$hashed_admin = password_hash($admin_password, PASSWORD_DEFAULT);
$hashed_invigilator = password_hash($invigilator_password, PASSWORD_DEFAULT);

echo "INSERT INTO admins (username, password, password_hash, role) VALUES<br>";
echo "('adminoffice', '" . $admin_password . "', '" . $hashed_admin . "', 'admission_office'),<br>";
echo "('invig', '" . $invigilator_password . "', '" . $hashed_invigilator . "', 'invigilator');<br>";
?>
