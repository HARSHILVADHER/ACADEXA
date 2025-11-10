<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$classes = isset($_POST['classes']) ? explode(',', $_POST['classes']) : [];

if(empty($classes)) {
    echo json_encode([]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($classes), '?'));
$types = str_repeat('s', count($classes)) . 'i';

// For now, return sample data since exam_results table may not exist
// This query would work if you have exam results stored
$query = "SELECT s.name as student_name, s.roll_no, s.class_code, 
          0 as total_marks, 0 as percentage
          FROM students s
          INNER JOIN classes c ON s.class_code = c.code AND c.user_id = s.user_id
          WHERE s.class_code IN ($placeholders) AND s.user_id = ?
          ORDER BY s.name
          LIMIT 20";

$stmt = $conn->prepare($query);
$params = array_merge($classes, [$user_id]);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);
$conn->close();
?>
