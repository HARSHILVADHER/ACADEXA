<?php
session_start();
require_once 'config.php';

header('Content-Type: text/plain');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo 'User not authenticated';
    exit();
}

$faculty_id = $_POST['faculty_id'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$email = $_POST['email'] ?? '';

if (empty($faculty_id) || empty($contact_number) || empty($email)) {
    echo 'All fields are required';
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Invalid email format';
    exit();
}

try {
    // Verify the faculty belongs to the current user
    $checkStmt = $conn->prepare("SELECT id FROM faculty WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $faculty_id, $user_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        echo 'Faculty not found or access denied';
        exit();
    }
    $checkStmt->close();
    
    // Update faculty contact and email
    $updateStmt = $conn->prepare("UPDATE faculty SET contact_number = ?, email = ? WHERE id = ? AND user_id = ?");
    $updateStmt->bind_param("ssii", $contact_number, $email, $faculty_id, $user_id);
    
    if ($updateStmt->execute()) {
        if ($updateStmt->affected_rows > 0) {
            echo 'success';
        } else {
            echo 'No changes made';
        }
    } else {
        echo 'Error updating faculty: ' . $conn->error;
    }
    
    $updateStmt->close();
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

$conn->close();
?>