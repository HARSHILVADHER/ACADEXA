<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

$classCode = $_GET['classCode'] ?? '';

$stmt = $conn->prepare("SELECT id, name, age, contact FROM students WHERE class_code = ? AND user_id = ?");
$stmt->bind_param("si", $classCode, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($students);

$stmt->close();
$conn->close();
?>
