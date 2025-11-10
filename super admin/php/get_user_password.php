<?php
header('Content-Type: application/json');
require_once '../../admin/php/config.php';

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Missing or invalid user ID.']);
    exit;
}

$stmt = $conn->prepare('SELECT password FROM users WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($password);
if ($stmt->fetch()) {
    echo json_encode(['success' => true, 'password' => $password]);
} else {
    echo json_encode(['success' => false, 'error' => 'User not found.']);
}
$stmt->close();
$conn->close(); 