<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method";
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$faculty_id = $_POST['faculty_id'] ?? '';
$dob = $_POST['dob'] ?? null;
$contact_number = $_POST['contact_number'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? '';

// Debug: Log received data
error_log("Received data: name=$name, faculty_id=$faculty_id, contact=$contact_number, email=$email, subject=$subject");

// Validate required fields
if (empty($name) || empty($faculty_id) || empty($contact_number) || empty($email) || empty($subject)) {
    echo "All required fields must be filled. Missing: ";
    if (empty($name)) echo "name ";
    if (empty($faculty_id)) echo "faculty_id ";
    if (empty($contact_number)) echo "contact ";
    if (empty($email)) echo "email ";
    if (empty($subject)) echo "subject ";
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format";
    exit();
}

try {
    // Check if faculty table exists, if not create it
    $table_check = $conn->query("SHOW TABLES LIKE 'faculty'");
    if ($table_check->num_rows == 0) {
        $create_table = "CREATE TABLE faculty (
            id INT AUTO_INCREMENT PRIMARY KEY,
            faculty_id VARCHAR(20) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            dob DATE,
            contact_number VARCHAR(20),
            email VARCHAR(100) UNIQUE NOT NULL,
            subject VARCHAR(100) NOT NULL,
            user_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        $conn->query($create_table);
    }
    
    // Check if faculty_id already exists for this user
    $check_stmt = $conn->prepare("SELECT id FROM faculty WHERE faculty_id = ? AND user_id = ?");
    $check_stmt->bind_param("si", $faculty_id, $user_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        echo "Faculty ID already exists";
        exit();
    }
    $check_stmt->close();
    
    // Check if email already exists
    $email_check = $conn->prepare("SELECT id FROM faculty WHERE email = ? AND user_id = ?");
    $email_check->bind_param("si", $email, $user_id);
    $email_check->execute();
    if ($email_check->get_result()->num_rows > 0) {
        echo "Email already exists";
        exit();
    }
    $email_check->close();
    
    // Insert faculty member
    $stmt = $conn->prepare("INSERT INTO faculty (faculty_id, name, dob, contact_number, email, subject, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $faculty_id, $name, $dob, $contact_number, $email, $subject, $user_id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error adding faculty member";
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>