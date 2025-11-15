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

$stmt = $conn->prepare("SELECT DISTINCT student_roll_no as id, student_name as name FROM marks WHERE class_code = ? AND user_id = ? ORDER BY student_name");
$stmt->bind_param("si", $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);
$conn->close();
?>
