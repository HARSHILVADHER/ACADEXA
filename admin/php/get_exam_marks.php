<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$exam_id = $_GET['exam_id'] ?? '';

$conn = new mysqli('localhost', 'root', '', 'acadexa');

$stmt = $conn->prepare("SELECT exam_name, code FROM exam WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $exam_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$exam = $result->fetch_assoc();

if(!$exam) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT student_name, student_roll_no, actual_marks, total_marks 
                       FROM marks 
                       WHERE exam_name = ? AND class_code = ? AND user_id = ? 
                       ORDER BY actual_marks DESC");
$stmt->bind_param("ssi", $exam['exam_name'], $exam['code'], $user_id);
$stmt->execute();
$result = $stmt->get_result();

$marks = [];
while($row = $result->fetch_assoc()) {
    $marks[] = $row;
}

echo json_encode($marks);
$conn->close();
?>
