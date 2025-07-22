<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$exam_id = $_POST['exam_id'] ?? null;

if (!$user_id || !$exam_id) {
    http_response_code(400);
    echo "Invalid request.";
    exit();
}

// Only allow deleting exams belonging to this user
$sql = "DELETE FROM exam WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $exam_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "success";
} else {
    echo "fail";
}
$stmt->close();
$conn->close();
?>