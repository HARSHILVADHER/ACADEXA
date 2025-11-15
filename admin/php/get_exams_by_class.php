<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$class_code = $_GET['class_code'] ?? '';

$conn = new mysqli('localhost', 'root', '', 'acadexa');

$stmt = $conn->prepare("SELECT DISTINCT e.id, e.exam_name FROM exam e 
                       INNER JOIN marks m ON e.exam_name = m.exam_name AND e.code = m.class_code
                       WHERE e.code = ? AND e.user_id = ? ORDER BY e.exam_date DESC");
$stmt->bind_param("si", $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$exams = [];
while($row = $result->fetch_assoc()) {
    $exams[] = $row;
}

echo json_encode($exams);
$conn->close();
?>
