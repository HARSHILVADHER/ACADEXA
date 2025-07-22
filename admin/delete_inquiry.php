<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$inquiry_id = $_POST['inquiry_id'] ?? null;

if (!$user_id || !$inquiry_id) {
    http_response_code(400);
    echo "Invalid request.";
    exit();
}

// Only delete if the inquiry belongs to the logged-in user
$sql = "DELETE FROM inquiry WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $inquiry_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "success";
} else {
    echo "fail";
}
$stmt->close();
$conn->close();
?>