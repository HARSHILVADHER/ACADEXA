<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$date = $_GET['date'] ?? '';
if (!$user_id || !$date) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, task_for, task_text FROM task WHERE user_id = ? AND task_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}
echo json_encode($tasks);
?>