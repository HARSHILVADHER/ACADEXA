<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$password = $_POST['password'] ?? '';
$user_id = $_SESSION['user_id'];

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit();
}

// Get user's password from database
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Verify password
if (password_verify($password, $user['password'])) {
    // Set session flag for income access
    $_SESSION['income_access'] = true;
    $_SESSION['income_access_time'] = time();
    echo json_encode(['success' => true, 'message' => 'Password verified']);
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect password']);
}

$conn->close();
?>