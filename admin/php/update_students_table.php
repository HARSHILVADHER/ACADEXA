<?php
require_once 'config.php';

// Update students table structure
$sql = "ALTER TABLE students 
        ADD COLUMN IF NOT EXISTS dob DATE,
        ADD COLUMN IF NOT EXISTS medium VARCHAR(50),
        ADD COLUMN IF NOT EXISTS roll_no VARCHAR(20),
        ADD COLUMN IF NOT EXISTS std VARCHAR(10),
        ADD COLUMN IF NOT EXISTS parent_contact VARCHAR(15),
        ADD COLUMN IF NOT EXISTS student_contact VARCHAR(15),
        ADD COLUMN IF NOT EXISTS group_name VARCHAR(50)";

if ($conn->query($sql) === TRUE) {
    echo "Students table updated successfully";
} else {
    echo "Error updating table: " . $conn->error;
}

$conn->close();
?>