<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT class_name, subject_name FROM subjects WHERE user_id = ? ORDER BY class_name, subject_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$grouped = [];
while ($row = $result->fetch_assoc()) {
    $className = $row['class_name'];
    if (!isset($grouped[$className])) {
        $grouped[$className] = [];
    }
    $grouped[$className][] = $row['subject_name'];
}

echo json_encode($grouped);
$stmt->close();
$conn->close();
?>
