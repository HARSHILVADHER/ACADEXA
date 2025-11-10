<?php
header('Content-Type: application/json');
// Include the database configuration from admin/config.php
require_once '../../admin/php/config.php';

// Get POST data
$full_name    = trim($_POST['full_name'] ?? '');
$username     = trim($_POST['username'] ?? '');
$email        = trim($_POST['email'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$institute_code = $_POST['institute_code'] ?? ''; // NEW FIELD

// Basic validation
if (!$full_name || !$username || !$email || !$phone_number) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
    exit;
}

// Check for duplicate username or email
$stmt = $conn->prepare('SELECT id FROM users WHERE username=? OR email=?');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Username or email already exists.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Insert user without password
$stmt = $conn->prepare('INSERT INTO users (full_name, username, email, phone_number, institute_code) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $full_name, $username, $email, $phone_number, $institute_code);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add user.']);
}
$stmt->close();
$conn->close(); 