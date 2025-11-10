-- SQL Query to create marks table
CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_code VARCHAR(50) NOT NULL,
    student_roll_no VARCHAR(50) NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    exam_date DATE NOT NULL,
    passing_marks INT NOT NULL,
    total_marks INT NOT NULL,
    actual_marks INT DEFAULT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_exam (student_roll_no, exam_name, user_id),
    INDEX idx_class_code (class_code),
    INDEX idx_user_id (user_id),
    INDEX idx_exam_name (exam_name)
);
