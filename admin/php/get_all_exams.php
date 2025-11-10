<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

try {
    $sql = "SELECT e.*, c.name AS class_name, c.code AS class_code FROM exam e 
            JOIN classes c ON e.code = c.code 
            WHERE e.user_id = ? ORDER BY e.exam_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $exams = [];
    while ($row = $result->fetch_assoc()) {
        $exams[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($exams);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error']);
}
?>