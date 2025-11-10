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
    $stmt = $conn->prepare("SELECT DISTINCT name FROM classes WHERE user_id = ? ORDER BY name");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $classes = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row['name'];
    }
    
    echo json_encode(['success' => true, 'classes' => $classes]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>