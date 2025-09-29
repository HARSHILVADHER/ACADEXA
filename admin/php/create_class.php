<?php
session_start(); // ✅ Ensure session is started
require_once 'config.php';

// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "User not authenticated!";
    exit();
}

// Get POST data
$name = $_POST['name'];
$code = $_POST['code'];
$year = $_POST['year'];
$mentor_name = $_POST['mentor_name'];

// Insert into classes table with user_id and mentor_name
$stmt = $conn->prepare("INSERT INTO classes (name, code, year, mentor_name, user_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $name, $code, $year, $mentor_name, $user_id);

if ($stmt->execute()) {
    // Sanitize table name (replace spaces and symbols)
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '_', $code);

    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS `$tableName` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            age INT NOT NULL,
            grade VARCHAR(20) NOT NULL,
            added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";

    if ($conn->query($createTableSQL) === TRUE) {
        echo "success";
    } else {
        echo "Error creating class table: " . $conn->error;
    }
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
