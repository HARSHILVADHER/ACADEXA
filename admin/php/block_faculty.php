<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo 'unauthorized';
    exit();
}

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_POST['faculty_id'] ?? null;
    $block_status = $_POST['block_status'] ?? null;
    $user_id = $_SESSION['user_id'];
    
    if (!$faculty_id || $block_status === null) {
        echo 'error';
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE faculty SET block_status = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $block_status, $faculty_id, $user_id);
    
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