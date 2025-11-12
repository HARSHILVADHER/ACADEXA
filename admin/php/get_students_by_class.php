<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];
$class_code = $_GET['class_code'] ?? '';

$students = [];

if ($class_code) {
    $stmt = $conn->prepare("SELECT id, name FROM students WHERE user_id = ? AND class_code = ? ORDER BY name");
    $stmt->bind_param('is', $user_id, $class_code);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
}

$conn->close();
echo json_encode($students);
