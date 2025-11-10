<?php
header('Content-Type: application/json');
require_once 'config.php';

$login_id = trim($_POST['login_id'] ?? '');
$institute_code = trim($_POST['institute_code'] ?? '');

if (empty($login_id) || empty($institute_code)) {
    echo json_encode(['success' => false, 'error' => 'Email/Username and Institute Code are required!']);
    exit();
}

$stmt = $conn->prepare("SELECT id, password FROM users WHERE (email = ? OR username = ?) AND institute_code = ?");
$stmt->bind_param("sss", $login_id, $login_id, $institute_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $has_password = !empty($user['password']);
    
    echo json_encode([
        'success' => true, 
        'has_password' => $has_password
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'User not found or Institute Code incorrect!']);
}

$stmt->close();
$conn->close();
?>