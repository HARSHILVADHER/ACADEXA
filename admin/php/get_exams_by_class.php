<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$class_code = $_GET['class_code'] ?? '';

if (empty($class_code)) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT e.id, e.exam_name FROM exam e 
                        INNER JOIN classes c ON e.code = c.code 
                        WHERE e.code = ? AND c.user_id = ? 
                        ORDER BY e.exam_date DESC");
$stmt->bind_param('si', $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$exams = [];
while ($row = $result->fetch_assoc()) {
    $exams[] = $row;
}

echo json_encode($exams);
$stmt->close();
$conn->close();
?>
