<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

require_once('dbaccess.php'); //to retrieve connection details
$db_obj = new mysqli($host, $username, $password, $dbname);
if ($db_obj->connect_error) {
    echo "Connection Error: " . $db_obj->connect_error;
    exit();
}
?>