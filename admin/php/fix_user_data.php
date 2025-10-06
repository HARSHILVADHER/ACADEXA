<?php
require_once 'config.php';

echo "<h2>Fixing Multi-User Data Isolation</h2>";

// Add user_id to study_materials if not exists
$result = $conn->query("SHOW COLUMNS FROM study_materials LIKE 'user_id'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE study_materials ADD COLUMN user_id INT DEFAULT NULL");
    echo "<p>✓ Added user_id column to study_materials</p>";
}

// Add user_id to attendance if not exists
$result = $conn->query("SHOW COLUMNS FROM attendance LIKE 'user_id'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE attendance ADD COLUMN user_id INT DEFAULT NULL");
    echo "<p>✓ Added user_id column to attendance</p>";
}

// Update existing study_materials with user_id based on class ownership
$conn->query("UPDATE study_materials sm 
              JOIN classes c ON sm.code = c.code 
              SET sm.user_id = c.user_id 
              WHERE sm.user_id IS NULL");
echo "<p>✓ Updated study_materials with user_id</p>";

// Update existing attendance with user_id based on student ownership
$conn->query("UPDATE attendance a 
              JOIN students s ON a.student_id = s.id 
              SET a.user_id = s.user_id 
              WHERE a.user_id IS NULL");
echo "<p>✓ Updated attendance with user_id</p>";

echo "<p><strong>Multi-user data isolation setup complete!</strong></p>";
echo "<p><a href='../dashboard.html'>← Back to Dashboard</a></p>";

$conn->close();
?>