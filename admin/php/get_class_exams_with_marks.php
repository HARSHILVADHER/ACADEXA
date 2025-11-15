<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$class_codes = isset($_POST['class_codes']) ? explode(',', $_POST['class_codes']) : [];

if(empty($class_codes)) {
    echo json_encode([]);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'acadexa');

if($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($class_codes), '?'));
$types = str_repeat('s', count($class_codes));

$stmt = $conn->prepare("SELECT DISTINCT e.id, e.exam_name, e.code, e.exam_date FROM exam e WHERE e.code IN ($placeholders) AND e.user_id = ? ORDER BY e.exam_date DESC");
$params = array_merge($class_codes, [$user_id]);
$stmt->bind_param($types . 'i', ...$params);
$stmt->execute();
$result = $stmt->get_result();

$exams = [];
while($row = $result->fetch_assoc()) {
    $exam_name = $row['exam_name'];
    $class_code = $row['code'];
    
    $marks_stmt = $conn->prepare("SELECT COUNT(*) as count FROM marks WHERE exam_name = ? AND class_code = ? AND user_id = ?");
    $marks_stmt->bind_param("ssi", $exam_name, $class_code, $user_id);
    $marks_stmt->execute();
    $marks_result = $marks_stmt->get_result();
    $marks_data = $marks_result->fetch_assoc();
    
    if($marks_data['count'] > 0) {
        $exams[] = $row;
    }
}

echo json_encode($exams);
$conn->close();
?>
