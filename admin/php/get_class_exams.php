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
    $exam_id = $row['id'];
    $exam_name = $row['exam_name'];
    
    $marks_stmt = $conn->prepare("SELECT COUNT(*) as count FROM marks WHERE exam_name = ? AND class_code = ? AND user_id = ?");
    $marks_stmt->bind_param("ssi", $exam_name, $class_code, $user_id);
    $marks_stmt->execute();
    $marks_result = $marks_stmt->get_result();
    $marks_data = $marks_result->fetch_assoc();
    
    $row['has_marks'] = $marks_data['count'] > 0;
    $exams[] = $row;
}

echo json_encode($exams);
$conn->close();
?>
