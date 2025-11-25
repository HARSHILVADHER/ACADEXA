<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("User not authenticated.");
}

$class_code = $_POST['class_code'] ?? '';
$exam_name = $_POST['exam_name'] ?? '';
$subject = $_POST['subject'] ?? '';
$exam_date = $_POST['exam_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$total_marks = $_POST['total_marks'] ?? '';
$passing_marks = $_POST['passing_marks'] ?? '';
$notes = $_POST['notes'] ?? '';

if (
    !$class_code || !$exam_name || !$subject || !$exam_date ||
    !$start_time || !$end_time || !$total_marks || !$passing_marks
) {
    die("All required fields must be filled.");
}

$sql = "INSERT INTO exam (user_id, code, exam_name, subject, exam_date, start_time, end_time, total_marks, passing_marks, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "issssssiis",
    $user_id,
    $class_code,
    $exam_name,
    $subject,
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