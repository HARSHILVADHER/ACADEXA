<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['exams' => [], 'subjects' => []]);
    exit();
}

$user_id = $_SESSION['user_id'];
$class_name = $_GET['class_name'] ?? '';
$class_code = $_GET['class_code'] ?? '';

$response = ['exams' => [], 'subjects' => []];

if ($class_name && $class_code) {
    // Fetch exams by class code
    $stmt = $conn->prepare("SELECT id, code, exam_name FROM exam WHERE user_id = ? AND code = ? ORDER BY exam_name");
    $stmt->bind_param('is', $user_id, $class_code);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['exams'][] = $row;
    }
    $stmt->close();
    
    // Fetch subjects
    $stmt = $conn->prepare("SELECT subject_code, subject_name FROM subjects WHERE class_name = ? AND user_id = ? ORDER BY subject_name");
    $stmt->bind_param('si', $class_name, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['subjects'][] = $row;
    }
    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>
