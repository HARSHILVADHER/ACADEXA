<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'] ?? $_SESSION['admin_id'];

try {
    $stmt = $conn->prepare("SELECT faculty_id as id, name FROM faculty WHERE user_id = ? ORDER BY name");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $faculty = [];
    while ($row = $result->fetch_assoc()) {
        $faculty[] = $row;
    }
    
    echo json_encode(['success' => true, 'faculty' => $faculty]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>