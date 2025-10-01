<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['class_code'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$user_id = $_SESSION['user_id'];
$class_code = $_POST['class_code'];

$stmt = $conn->prepare("SELECT s.id, s.name, s.class_code, 
    CASE WHEN f.student_id IS NOT NULL THEN 1 ELSE 0 END as has_fees
    FROM students s 
    LEFT JOIN fees_structure f ON s.id = f.student_id AND f.user_id = s.user_id
    WHERE s.class_code = ? AND s.user_id = ? 
    ORDER BY has_fees ASC, s.name");
$stmt->bind_param("si", $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'students' => $students
]);
?>