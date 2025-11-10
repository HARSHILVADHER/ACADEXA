<?php
session_start();
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
    
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode([]);
        exit;
    }
    
    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'study_materials'");
    if (!$tableCheck || $tableCheck->num_rows == 0) {
        echo json_encode([]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT sm.*, c.name as class_name 
            FROM study_materials sm 
            LEFT JOIN classes c ON sm.code = c.code 
            WHERE sm.user_id = ?
            ORDER BY sm.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $materials = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $materials[] = $row;
        }
    }
    
    echo json_encode($materials);
} catch (Exception $e) {
    echo json_encode([]);
}
?>