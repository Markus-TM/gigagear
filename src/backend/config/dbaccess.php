<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

// Entferne diese Zeile, da sie eine rekursive Einbindung verursacht
// require_once('dbaccess.php');

$db_obj = new mysqli($host, $username, $password, $dbname);
if ($db_obj->connect_error) {
    echo "Connection Error: " . $db_obj->connect_error;
    exit();
}
?>
