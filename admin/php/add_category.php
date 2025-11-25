<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$category_name = trim($_POST['category_name'] ?? '');
$category_type = $_POST['category_type'] ?? '';

if (empty($category_name) || empty($category_type)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if (!in_array($category_type, ['income', 'expense'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid category type']);
    exit();
}

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS income_expense_category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    category_type ENUM('income', 'expense') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_category_type (category_type),
    UNIQUE KEY unique_user_category (user_id, category_name, category_type)
)");

$stmt = $conn->prepare("INSERT INTO income_expense_category (user_id, category_name, category_type) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $category_name, $category_type);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Category added successfully']);
} else {
    if ($conn->errno == 1062) {
        echo json_encode(['success' => false, 'message' => 'Category already exists']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add category']);
    }
}

$stmt->close();
$conn->close();
?>
