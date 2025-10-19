<?php
require_once 'config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        class_name VARCHAR(100) NOT NULL,
        subject_name VARCHAR(100) NOT NULL,
        subject_code VARCHAR(20) NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_class_user (class_name, user_id),
        INDEX idx_subject_code_user (subject_code, user_id),
        UNIQUE KEY unique_subject_code_user (subject_code, user_id)
    )";
    
    $pdo->exec($sql);
    echo "Subjects table created successfully!";
    
} catch (PDOException $e) {
    echo "Error creating subjects table: " . $e->getMessage();
}
?>