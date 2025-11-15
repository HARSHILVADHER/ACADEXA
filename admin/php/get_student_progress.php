<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$student_roll = $_GET['student_roll'] ?? '';
$class_code = $_GET['class_code'] ?? '';

$conn = new mysqli('localhost', 'root', '', 'acadexa');

// Get student details
$stmt = $conn->prepare("SELECT DISTINCT student_name, student_roll_no FROM marks WHERE student_roll_no = ? AND class_code = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ssi", $student_roll, $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if(!$student) {
    echo json_encode(['error' => 'Student not found']);
    exit;
}

// Get all exam marks
$stmt = $conn->prepare("SELECT exam_name, exam_date, actual_marks, total_marks FROM marks WHERE student_roll_no = ? AND class_code = ? AND user_id = ? ORDER BY exam_date ASC");
$stmt->bind_param("ssi", $student_roll, $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$exams = [];
while($row = $result->fetch_assoc()) {
    $exams[] = $row;
}

// Get class ranking
$stmt = $conn->prepare("SELECT student_roll_no, SUM(actual_marks) as total FROM marks WHERE class_code = ? AND user_id = ? GROUP BY student_roll_no ORDER BY total DESC");
$stmt->bind_param("si", $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rank = 0;
$position = 0;
while($row = $result->fetch_assoc()) {
    $position++;
    if($row['student_roll_no'] == $student_roll) {
        $rank = $position;
        break;
    }
}

echo json_encode([
    'student' => $student,
    'exams' => $exams,
    'rank' => $rank,
    'total_students' => $position
]);

$conn->close();
?>
