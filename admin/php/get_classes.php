<?php
require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT code, name FROM classes WHERE user_id = ? ORDER BY code";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

echo json_encode($classes);
$stmt->close();
$conn->close();
?>
