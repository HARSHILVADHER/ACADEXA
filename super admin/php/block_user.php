<?php
header('Content-Type: application/json');
require_once '../../admin/php/config.php';
$id = intval($_POST['id'] ?? 0);
if (!$id) {
  echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
  exit;
}
// Get current status
$stmt = $conn->prepare('SELECT blocked FROM users WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($blocked);
if ($stmt->fetch() === null) {
  echo json_encode(['success' => false, 'error' => 'User not found.']);
  $stmt->close();
  $conn->close();
  exit;
}
$stmt->close();
$new_status = $blocked ? 0 : 1;
$stmt2 = $conn->prepare('UPDATE users SET blocked=? WHERE id=?');
$stmt2->bind_param('ii', $new_status, $id);
if ($stmt2->execute()) {
  echo json_encode(['success' => true, 'blocked' => $new_status]);
} else {
  echo json_encode(['success' => false, 'error' => 'Failed to update block status.']);
}
$stmt2->close();
$conn->close(); 