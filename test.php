<?php
ob_start();
phpinfo();
$info = ob_get_clean();

if (strpos($info, 'mysqlnd') !== false) {
    echo "MySQLnd is ACTIVE!";
} else {
    echo "MySQLnd NOT FOUND!";
}