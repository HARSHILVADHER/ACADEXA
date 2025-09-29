<?php
$host = "localhost"; // Change this to your actual DB host if not localhost
$db_user = "root"; // Change this to your actual DB user
$db_pass = "";
$db_name = "acadexa"; // Change this to your actual DB name

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>