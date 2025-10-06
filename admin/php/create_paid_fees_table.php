<?php
require_once 'config.php';

// Create paid_fees table
$sql = "CREATE TABLE IF NOT EXISTS paid_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    user_id INT NOT NULL,
    installment_index INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    due_date DATE NOT NULL,
    payment_mode ENUM('cash', 'cheque', 'online') NOT NULL,
    is_paid TINYINT(1) DEFAULT 0,
    paid_date TIMESTAMP NULL DEFAULT NULL,
    receipt_generated TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_user (student_id, user_id),
    INDEX idx_payment_status (is_paid),
    UNIQUE KEY unique_student_installment (student_id, user_id, installment_index)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table paid_fees created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>