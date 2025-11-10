<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once 'config.php';

// Create uploads directory if it doesn't exist
$uploadDir = '../../uploads/';
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
$filename = time() . '_' . uniqid() . '.' . $extension;
$filepath = $uploadDir . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save image']);
    exit();
}

// Save to database
$imagePath = 'uploads/' . $filename;
$student_id = $_POST['student_id'] ?? null;

if (!$student_id) {
    unlink($filepath);
    echo json_encode(['success' => false, 'message' => 'Student ID required']);
    exit();
}

// Update student profile image
$stmt = $conn->prepare("UPDATE students SET profile_image = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sii", $imagePath, $student_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host . '/ACADEXA/';
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile image updated successfully',
        'image_url' => $baseUrl . $imagePath
    ]);
} else {
    unlink($filepath);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>