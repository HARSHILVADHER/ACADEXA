<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== 0) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$title = $_POST['title'] ?? '';
$code = $_POST['class_code'] ?? '';
$subject = $_POST['subject'] ?? '';
$type = $_POST['type'] ?? '';
$description = $_POST['description'] ?? '';

if (empty($title) || empty($code) || empty($subject) || empty($type)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Add user_id column if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM study_materials LIKE 'user_id'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE study_materials ADD COLUMN user_id INT DEFAULT NULL");
}

$fileName = $_FILES['material_file']['name'];
$fileType = $_FILES['material_file']['type'];
$fileTmpPath = $_FILES['material_file']['tmp_name'];
$fileData = file_get_contents($fileTmpPath);

if (!$fileData) {
    echo json_encode(['success' => false, 'message' => 'Could not read file']);
    exit;
}

$sql = "INSERT INTO study_materials (title, code, subject, type, description, file_name, file_type, file_data, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare failed']);
    exit;
}

$stmt->bind_param("ssssssssi", $title, $code, $subject, $type, $description, $fileName, $fileType, $fileData, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database insert failed']);
}

$stmt->close();
?>