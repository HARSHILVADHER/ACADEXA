<?php
require_once 'config.php';

// Create fees_structure table
$sql = "CREATE TABLE IF NOT EXISTS fees_structure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    student_roll_no INT NOT NULL,
    class_code VARCHAR(50) NOT NULL,
    decided_fees DECIMAL(10,2) NOT NULL,
    installments JSON,
    notes TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student_user (student_id, user_id),
    INDEX idx_user_class (user_id, class_code)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table fees_structure created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>