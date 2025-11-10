<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];
$classCode = $_POST['classCode'] ?? '';

if (empty($classCode)) {
    echo "Class code is required";
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // First delete all students in this class
    $stmt = $conn->prepare("DELETE FROM students WHERE class_code = ? AND user_id = ?");
    $stmt->bind_param("si", $classCode, $user_id);
    $stmt->execute();
    
    // Then delete the class
    $stmt = $conn->prepare("DELETE FROM classes WHERE code = ? AND user_id = ?");
    $stmt->bind_param("si", $classCode, $user_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        // Commit transaction
        $conn->commit();
        echo "success";
    } else {
        // Rollback transaction
        $conn->rollback();
        echo "Class not found or you don't have permission to delete it";
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?>