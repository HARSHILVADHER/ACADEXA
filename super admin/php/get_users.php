<?php
header('Content-Type: application/json');
require_once '../../admin/config.php';

$sql = "SELECT id, full_name, username, email, phone_number, blocked, institute_code FROM users";
$result = $conn->query($sql);
if ($result) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['success' => true, 'users' => $users]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch users.']);
}
$conn->close(); 