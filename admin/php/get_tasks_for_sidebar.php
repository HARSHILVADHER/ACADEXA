<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$role = $_GET['role'] ?? '';

if (!$user_id || !$role) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT task_date, task_text FROM task WHERE user_id = ? AND task_for = ? ORDER BY task_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $role);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}
echo json_encode($tasks);
?>