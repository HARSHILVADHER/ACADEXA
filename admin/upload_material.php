<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
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

$fileName = $_FILES['material_file']['name'];
$fileType = $_FILES['material_file']['type'];
$fileTmpPath = $_FILES['material_file']['tmp_name'];
$fileData = file_get_contents($fileTmpPath);

if (!$fileData) {
    echo json_encode(['success' => false, 'message' => 'Could not read file']);
    exit;
}

$sql = "INSERT INTO study_materials (title, code, subject, type, description, file_name, file_type, file_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare failed']);
    exit;
}

$stmt->bind_param("ssssssss", $title, $code, $subject, $type, $description, $fileName, $fileType, $fileData);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database insert failed']);
}

$stmt->close();
?>