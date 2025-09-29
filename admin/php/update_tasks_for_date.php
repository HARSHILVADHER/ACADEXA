<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$old_date = $_POST['old_task_date'] ?? '';
$new_date = $_POST['task_date'] ?? '';
$for = $_POST['task_for'] ?? '';
$tasks = $_POST['task_text'] ?? [];

if ($user_id && $old_date && $new_date && $for && is_array($tasks)) {
    // Delete old tasks for this user/old_date/role
    $stmt = $conn->prepare("DELETE FROM task WHERE user_id = ? AND task_date = ? AND task_for = ?");
    $stmt->bind_param("iss", $user_id, $old_date, $for);
    $stmt->execute();
    $stmt->close();

    // Insert new/updated tasks for the new date
    $stmt = $conn->prepare("INSERT INTO task (user_id, task_date, task_for, task_text) VALUES (?, ?, ?, ?)");
    foreach ($tasks as $task) {
        if (trim($task) !== '') {
            $stmt->bind_param("isss", $user_id, $new_date, $for, $task);
            $stmt->execute();
        }
    }
    $stmt->close();
    echo "Tasks updated!";
} else {
    echo "Please fill all fields.";
}
?>