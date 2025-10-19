<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$className = $_GET['class'] ?? '';
$userId = $_SESSION['user_id'];

if (empty($className)) {
    echo json_encode(['success' => false, 'message' => 'Class name is required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, subject_name, subject_code FROM subjects WHERE class_name = ? AND user_id = ? ORDER BY subject_name");
$stmt->bind_param("si", $className, $userId);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

echo json_encode(['success' => true, 'subjects' => $subjects]);

$stmt->close();
$conn->close();
?>