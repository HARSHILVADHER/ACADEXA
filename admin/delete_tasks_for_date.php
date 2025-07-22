<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$date = $_POST['old_task_date'] ?? '';
$for = $_POST['task_for'] ?? '';

if ($user_id && $date && $for) {
    $stmt = $conn->prepare("DELETE FROM task WHERE user_id = ? AND task_date = ? AND task_for = ?");
    $stmt->bind_param("iss", $user_id, $date, $for);
    $stmt->execute();
    $stmt->close();
    echo "Tasks marked as completed and deleted!";
} else {
    echo "Please fill all fields.";
}
?>