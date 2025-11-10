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
if (empty($faculty_id)) {
    echo 'Faculty ID is required';
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // First, verify the faculty belongs to the current user
    $checkStmt = $conn->prepare("SELECT id FROM faculty WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $faculty_id, $user_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        $conn->rollback();
        echo 'Faculty not found or access denied';
        exit();
    }
    $checkStmt->close();
    
    // Delete from related tables first to avoid foreign key constraints
    // Delete from any tables that might reference this faculty
    
    // Delete any other related records here as needed
    // Add more DELETE statements for other tables that reference faculty
    
    // Finally delete the faculty record
    $deleteStmt = $conn->prepare("DELETE FROM faculty WHERE id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $faculty_id, $user_id);
    
    if ($deleteStmt->execute()) {
        if ($deleteStmt->affected_rows > 0) {
            $conn->commit();
            echo 'success';
        } else {
            $conn->rollback();
            echo 'Faculty not found';
        }
    } else {
        $conn->rollback();
        echo 'Error deleting faculty: ' . $conn->error;
    }
    
    $deleteStmt->close();
    
} catch (Exception $e) {
    $conn->rollback();
    echo 'Error: ' . $e->getMessage();
}

$conn->close();
?>