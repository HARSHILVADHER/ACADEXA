<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
$classCode = $_GET['classCode'] ?? '';

echo json_encode([
    'debug' => [
        'user_id' => $user_id,
        'classCode' => $classCode,
        'session' => $_SESSION
    ]
]);

if (!$user_id) {
    echo json_encode(['error' => 'No user session', 'user_id' => $user_id]);
    exit();
}

if (empty($classCode)) {
    echo json_encode(['error' => 'No class code provided', 'classCode' => $classCode]);
    exit();
}

// Test basic query
$sql = "SELECT id, name FROM students WHERE class_code = ? AND user_id = ? LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $classCode, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode([
    'success' => true,
    'count' => count($students),
    'students' => $students
]);

$stmt->close();
$conn->close();
?>