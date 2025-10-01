<?php
session_start();
require_once 'config.php';

echo "<h2>Database Connection Test</h2>";

// Test connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
    exit();
} else {
    echo "<p style='color: green;'>Database connected successfully!</p>";
}

// Check if fees_structure table exists
$result = $conn->query("SHOW TABLES LIKE 'fees_structure'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>fees_structure table exists!</p>";
    
    // Show table structure
    $structure = $conn->query("DESCRIBE fees_structure");
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . ($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Count records
    $count = $conn->query("SELECT COUNT(*) as total FROM fees_structure");
    $total = $count->fetch_assoc()['total'];
    echo "<p>Total records in fees_structure: $total</p>";
    
} else {
    echo "<p style='color: red;'>fees_structure table does not exist!</p>";
    echo "<p>Creating table...</p>";
    
    $create_sql = "CREATE TABLE IF NOT EXISTS fees_structure (
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
    
    if ($conn->query($create_sql) === TRUE) {
        echo "<p style='color: green;'>Table created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error creating table: " . $conn->error . "</p>";
    }
}

// Test students table
$students_result = $conn->query("SELECT COUNT(*) as total FROM students");
if ($students_result) {
    $students_total = $students_result->fetch_assoc()['total'];
    echo "<p>Total students in database: $students_total</p>";
} else {
    echo "<p style='color: red;'>Error accessing students table: " . $conn->error . "</p>";
}

$conn->close();
?>

<p><a href="fees.php">← Back to Fees Structure</a></p>