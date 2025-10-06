<?php
header('Content-Type: application/json');
require_once '../../admin/config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        phone_number VARCHAR(20),
        institute_code VARCHAR(10),
        blocked TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Users table ready']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error creating table: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>