<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, task_date, task_for, task_text FROM task WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    // Set color based on task_for
    switch ($row['task_for']) {
        case 'admin':
            $color = '#fef08a'; // light yellow
            break;
        case 'tutor':
            $color = '#bbf7d0'; // light green
            break;
        case 'trustee':
            $color = '#bae6fd'; // light blue
            break;
        default:
            $color = '#2563eb'; // fallback blue
    }
    $events[] = [
        'id' => $row['id'],
        'title' => ucfirst($row['task_for']) . ': ' . mb_strimwidth($row['task_text'], 0, 30, '...'),
        'start' => $row['task_date'],
        'color' => $color
    ];
}
echo json_encode($events);
?>