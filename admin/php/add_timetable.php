<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$subjectId = (int)($_POST['subjectId'] ?? 0);
$className = trim($_POST['className'] ?? '');
$facultyName = trim($_POST['facultyName'] ?? '');
$dayOfWeek = trim($_POST['dayOfWeek'] ?? '');
$startTime = trim($_POST['startTime'] ?? '');
$endTime = trim($_POST['endTime'] ?? '');
$userId = $_SESSION['user_id'];

if (!$subjectId || empty($className) || empty($facultyName) || empty($dayOfWeek) || empty($startTime) || empty($endTime)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if ($startTime >= $endTime) {
    echo json_encode(['success' => false, 'message' => 'End time must be after start time']);
    exit;
}

try {
    // Check if subject belongs to admin
    $stmt = $conn->prepare("SELECT id FROM subjects WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $subjectId, $userId);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Invalid subject']);
        exit;
    }

    // Check for time conflicts
    $stmt = $conn->prepare("SELECT id FROM timetable WHERE class_name = ? AND day_of_week = ? AND user_id = ? AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))");
    $stmt->bind_param('ssissss', $className, $dayOfWeek, $userId, $startTime, $startTime, $endTime, $endTime);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Time slot conflicts with existing entry']);
        exit;
    }

    // Get subject details
    $stmt = $conn->prepare("SELECT subject_name, subject_code FROM subjects WHERE id = ?");
    $stmt->bind_param('i', $subjectId);
    $stmt->execute();
    $subjectData = $stmt->get_result()->fetch_assoc();
    
    $stmt = $conn->prepare("INSERT INTO timetable (subject_id, subject_name, subject_code, class_name, faculty_name, day_of_week, start_time, end_time, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssssi', $subjectId, $subjectData['subject_name'], $subjectData['subject_code'], $className, $facultyName, $dayOfWeek, $startTime, $endTime, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Timetable entry added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add timetable entry']);
    }
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>