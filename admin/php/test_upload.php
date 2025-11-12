<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';

echo "Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "<br>";
echo "POST data: ";
print_r($_POST);
echo "<br>FILES data: ";
print_r($_FILES);
echo "<br>Upload dir exists: " . (file_exists('../../uploads/') ? 'YES' : 'NO');
