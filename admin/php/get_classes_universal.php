<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check for authentication with both possible session variables
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'] ?? $_SESSION['admin_id'];

try {
    $stmt = $conn->prepare("SELECT name, code, year, mentor_name FROM classes WHERE user_id = ? ORDER BY name");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $classes = [];
    $classNames = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
        $classNames[] = $row['name'];
    }
    
    echo json_encode([
        'success' => true, 
        'classes' => $classNames,
        'data' => $classes
    ]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>