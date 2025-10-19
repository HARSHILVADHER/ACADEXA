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

$className = $_POST['className'] ?? '';
$subjectId = $_POST['subjectId'] ?? '';
$facultyId = $_POST['facultyId'] ?? '';
$dayOfWeek = $_POST['dayOfWeek'] ?? '';
$startTime = $_POST['startTime'] ?? '';
$endTime = $_POST['endTime'] ?? '';
$copyDays = json_decode($_POST['copyDays'] ?? '[]', true);
$userId = $_SESSION['user_id'];

if (empty($className) || empty($subjectId) || empty($facultyId) || empty($dayOfWeek) || empty($startTime) || empty($endTime)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate time format
if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $startTime) || !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $endTime)) {
    echo json_encode(['success' => false, 'message' => 'Invalid time format']);
    exit;
}

// Get subject details
$subjectStmt = $conn->prepare("SELECT subject_name, subject_code FROM subjects WHERE id = ? AND user_id = ?");
$subjectStmt->bind_param("ii", $subjectId, $userId);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();
$subject = $subjectResult->fetch_assoc();

if (!$subject) {
    echo json_encode(['success' => false, 'message' => 'Subject not found']);
    exit;
}

// Get faculty name
$facultyStmt = $conn->prepare("SELECT name FROM faculty WHERE id = ? AND user_id = ?");
$facultyStmt->bind_param("ii", $facultyId, $userId);
$facultyStmt->execute();
$facultyResult = $facultyStmt->get_result();
$faculty = $facultyResult->fetch_assoc();

if (!$faculty) {
    echo json_encode(['success' => false, 'message' => 'Faculty not found']);
    exit;
}

$conn->autocommit(FALSE);

// Days to insert (main day + copy days)
$daysToInsert = array_merge([$dayOfWeek], $copyDays);
$daysToInsert = array_unique($daysToInsert);

// Validate time format and logic
if ($startTime >= $endTime) {
    echo json_encode(['success' => false, 'message' => 'End time must be after start time']);
    exit;
}

foreach ($daysToInsert as $day) {
    // Check for time conflicts with proper overlap detection
    $conflictStmt = $conn->prepare("
        SELECT id, subject_name, start_time, end_time FROM timetable 
        WHERE class_name = ? AND day_of_week = ? AND user_id = ?
        AND NOT (end_time <= ? OR start_time >= ?)
    ");
    $conflictStmt->bind_param("ssiss", $className, $day, $userId, $startTime, $endTime);
    $conflictStmt->execute();
    $conflictResult = $conflictStmt->get_result();
    
    if ($conflictResult->num_rows > 0) {
        $conflict = $conflictResult->fetch_assoc();
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => "Time conflict on $day with {$conflict['subject_name']} ({$conflict['start_time']}-{$conflict['end_time']})"]);
        exit;
    }
    
    // Insert timetable entry
    $insertStmt = $conn->prepare("
        INSERT INTO timetable (
            class_code, class_name, subject_name, subject_code, 
            faculty_id, faculty_name, day_of_week, start_time, 
            end_time, user_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $insertStmt->bind_param("sssssssssi", $className, $className, $subject['subject_name'], $subject['subject_code'], $facultyId, $faculty['name'], $day, $startTime, $endTime, $userId);
    
    if (!$insertStmt->execute()) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error inserting timetable entry']);
        exit;
    }
}

$conn->commit();
$conn->autocommit(TRUE);
echo json_encode(['success' => true, 'message' => 'Timetable entries added successfully']);

$conn->close();
?>