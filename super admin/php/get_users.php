<?php
header('Content-Type: application/json');
require_once '../../admin/php/config.php';

try {
    // First try to create the table if it doesn't exist
    $createSql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        phone_number VARCHAR(20),
        institute_code VARCHAR(10),
        blocked TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($createSql);
    
    // Now fetch users
    $sql = "SELECT id, full_name, username, email, phone_number, blocked, institute_code FROM users";
    $result = $conn->query($sql);
    
    if ($result) {
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode(['success' => true, 'users' => $users]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to fetch users.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close(); 