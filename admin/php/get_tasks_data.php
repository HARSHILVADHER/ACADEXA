<?php
require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

$taskRoleFilter = $_POST['task_role_filter'] ?? '';
$taskRoleWhere = '';
$taskRoleParams = [];
$taskRoleTypes = 'i';

if ($taskRoleFilter && in_array($taskRoleFilter, ['admin', 'trustee', 'tutor'])) {
    $taskRoleWhere = " AND task_for = ?";
    $taskRoleParams[] = $taskRoleFilter;
    $taskRoleTypes .= 's';
}

// Fetch tasks for this user
$userTasks = [];
$sql = "SELECT task_text, task_date, task_for FROM task WHERE user_id = ?$taskRoleWhere ORDER BY task_date DESC LIMIT 10";
$stmt = $conn->prepare($sql);
if ($taskRoleWhere) {
    $stmt->bind_param($taskRoleTypes, $user_id, ...$taskRoleParams);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $userTasks[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['tasks' => $userTasks]);
?>