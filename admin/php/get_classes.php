<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    if (!file_exists('config.php')) {
        echo json_encode([]);
        exit;
    }
    
    require_once 'config.php';
    
    if (!isset($conn) || $conn->connect_error) {
        echo json_encode([]);
        exit;
    }
    
    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'classes'");
    if (!$tableCheck || $tableCheck->num_rows == 0) {
        echo json_encode([]);
        exit;
    }
    
    $sql = "SELECT code, name FROM classes";
    $result = $conn->query($sql);
    
    $classes = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }
    }
    
    echo json_encode($classes);
} catch (Exception $e) {
    echo json_encode([]);
}
?>