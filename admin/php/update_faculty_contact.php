<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo 'unauthorized';
    exit();
}

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_POST['faculty_id'] ?? null;
    $contact_number = $_POST['contact_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    if (!$faculty_id) {
        echo 'error';
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE faculty SET contact_number = ?, email = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $contact_number, $email, $faculty_id, $user_id);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo 'error';
}
?>