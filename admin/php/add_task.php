<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$date = $_POST['task_date'] ?? '';
$for = $_POST['task_for'] ?? '';
$tasks = $_POST['task_text'] ?? [];

if ($user_id && $date && $for && is_array($tasks)) {
    $stmt = $conn->prepare("INSERT INTO task (user_id, task_date, task_for, task_text) VALUES (?, ?, ?, ?)");
    foreach ($tasks as $task) {
        if (trim($task) !== '') {
            $stmt->bind_param("isss", $user_id, $date, $for, $task);
            $stmt->execute();
        }
    }
    $stmt->close();
    echo "Task(s) added successfully!";
} else {
    echo "Please fill all fields.";
}
?>