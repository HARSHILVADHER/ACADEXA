<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$className = $_GET['class'] ?? '';
$userId = $_SESSION['user_id'];

if (empty($className)) {
    echo json_encode(['success' => false, 'message' => 'Class name required']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT * FROM timetable 
        WHERE class_name = ? AND user_id = ? 
        ORDER BY day_of_week, start_time
    ");
    $stmt->bind_param('si', $className, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $timetable = [];
    while ($row = $result->fetch_assoc()) {
        $timetable[] = $row;
    }
    
    echo json_encode(['success' => true, 'timetable' => $timetable]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>