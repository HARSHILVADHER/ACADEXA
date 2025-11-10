<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$class_code = $_POST['class_code'];

$stmt = $conn->prepare("SELECT e.* FROM exam e 
                       INNER JOIN classes c ON e.code = c.code AND c.user_id = e.user_id
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
