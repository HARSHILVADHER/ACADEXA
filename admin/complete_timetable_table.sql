-- Complete Timetable Table - Single table with all details
-- Drop existing table and create new one

DROP TABLE IF EXISTS timetable;

CREATE TABLE timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_code VARCHAR(20) NOT NULL,
    class_name VARCHAR(50) NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(20) NOT NULL,
    faculty_id INT NOT NULL,
    faculty_name VARCHAR(100) NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room_no VARCHAR(50) NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_class (user_id, class_name),
    INDEX idx_faculty (faculty_id),
    INDEX idx_day_time (day_of_week, start_time),
    UNIQUE KEY unique_time_slot (class_name, day_of_week, start_time, user_id)
);