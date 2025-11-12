<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['exams' => [], 'subjects' => []]);
    exit();
}

$user_id = $_SESSION['user_id'];
$class_code = $_GET['class_code'] ?? '';
$class_name = $_GET['class_name'] ?? '';

$response = ['exams' => [], 'subjects' => []];

if ($class_code) {
    // Fetch exams
    $stmt = $conn->prepare("SELECT code, exam_name FROM exam WHERE user_id = ? AND code LIKE ? ORDER BY exam_name");
    $pattern = $class_code . '%';
    $stmt->bind_param('is', $user_id, $pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['exams'][] = $row;
    }
    $stmt->close();
    
    // Fetch subjects
    $stmt = $conn->prepare("SELECT subject_code, subject_name FROM subjects WHERE user_id = ? AND class_name = ? ORDER BY subject_name");
    $stmt->bind_param('is', $user_id, $class_name);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['subjects'][] = $row;
    }
    $stmt->close();
}

$conn->close();
echo json_encode($response);
