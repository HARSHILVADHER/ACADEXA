<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once 'config.php';

// Create uploads directory if it doesn't exist
$uploadDir = '../../uploads/admin/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'No image uploaded']);
    exit();
}

$file = $_FILES['image'];

// Validate file
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload error occurred']);
    exit();
}

// Check file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed']);
    exit();
}

// Check file size (5MB max)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum 5MB allowed']);
    exit();
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'admin_' . time() . '_' . uniqid() . '.' . $extension;
$filepath = $uploadDir . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save image']);
    exit();
}

// Save to database - check if profile_image column exists in users table
$imagePath = 'uploads/admin/' . $filename;
$user_id = $_SESSION['user_id'];

$result = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL");
}

$stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
$stmt->bind_param("si", $imagePath, $user_id);

if ($stmt->execute()) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host . '/ACADEXA/';
    
    echo json_encode([
        'success' => true,
        'message' => 'Admin profile image updated successfully',
        'image_url' => $baseUrl . $imagePath
    ]);
} else {
    unlink($filepath);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>