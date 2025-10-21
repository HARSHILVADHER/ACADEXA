<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Check if faculty table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'faculty'");
    if ($table_check->num_rows == 0) {
        echo json_encode([]);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id, faculty_id, name, dob, contact_number, email, subject, created_at, block_status FROM faculty WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $faculty = [];
    while ($row = $result->fetch_assoc()) {
        $faculty[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($faculty);
} catch (Exception $e) {
    echo json_encode([]);
}
?>