<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("User not authenticated.");
}

$class_id = $_POST['class_id'] ?? '';
$exam_name = $_POST['exam_name'] ?? '';
$exam_date = $_POST['exam_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$total_marks = $_POST['total_marks'] ?? '';
$passing_marks = $_POST['passing_marks'] ?? '';
$notes = $_POST['notes'] ?? '';

if (
    !$class_id || !$exam_name || !$exam_date ||
    !$start_time || !$end_time || !$total_marks || !$passing_marks
) {
    die("All required fields must be filled.");
}

$sql = "INSERT INTO exam (user_id, class_id, exam_name, exam_date, start_time, end_time, total_marks, passing_marks, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iissssiss",
    $user_id,
    $class_id,
    $exam_name,
    $exam_date,
    $start_time,
    $end_time,
    $total_marks,
    $passing_marks,
    $notes
);

if ($stmt->execute()) {
    header("Location: create_exam.php?success=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>