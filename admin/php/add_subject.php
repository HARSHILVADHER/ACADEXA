<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$className = $_POST['className'] ?? '';
$subjectName = $_POST['subjectName'] ?? '';
$subjectCode = $_POST['subjectCode'] ?? '';
$userId = $_SESSION['user_id'];

if (empty($className) || empty($subjectName) || empty($subjectCode)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Create subjects table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(20) NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTable);

// Check if subject code already exists for this user
$checkStmt = $conn->prepare("SELECT id FROM subjects WHERE subject_code = ? AND user_id = ?");
$checkStmt->bind_param("si", $subjectCode, $userId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Subject code already exists']);
    exit;
}

// Insert subject
$stmt = $conn->prepare("INSERT INTO subjects (class_name, subject_name, subject_code, user_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $className, $subjectName, $subjectCode, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Subject added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>