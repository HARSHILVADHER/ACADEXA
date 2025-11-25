<?php
header('Content-Type: application/json');
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$class_name = $_GET['class_name'] ?? '';

if (!$user_id || !$class_name) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT id, subject_name, subject_code FROM subjects WHERE user_id = ? AND class_name = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([]);
    exit();
}
$stmt->bind_param("is", $user_id, $class_name);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

echo json_encode($subjects);
$stmt->close();
$conn->close();
?>
