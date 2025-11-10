<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo 'error: not authenticated';
    exit();
}

$exam_id = $_POST['exam_id'] ?? null;
$exam_date = $_POST['exam_date'] ?? null;
$start_time = $_POST['start_time'] ?? null;
$end_time = $_POST['end_time'] ?? null;
$total_marks = $_POST['total_marks'] ?? null;
$passing_marks = $_POST['passing_marks'] ?? null;

if (!$exam_id || !$exam_date || !$start_time || !$end_time || !$total_marks || !$passing_marks) {
    echo 'error: missing fields';
    exit();
}

try {
    $sql = "UPDATE exam SET exam_date = ?, start_time = ?, end_time = ?, total_marks = ?, passing_marks = ? 
            WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiiii", $exam_date, $start_time, $end_time, $total_marks, $passing_marks, $exam_id, $user_id);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: update failed';
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo 'error: ' . $e->getMessage();
}
?>
