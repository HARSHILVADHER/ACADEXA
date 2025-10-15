<?php
header('Content-Type: application/json');
session_start();
require_once 'config.php';

try {
    $classCode = $_GET['classCode'] ?? '';
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode([]);
        exit;
    }

    if ($classCode) {
        $stmt = $conn->prepare("SELECT id, name, roll_no, std, group_name, parent_contact, email FROM students WHERE class_code = ? AND user_id = ? ORDER BY roll_no ASC");
        $stmt->bind_param("si", $classCode, $user_id);
    } else {
        $stmt = $conn->prepare("SELECT id, name, roll_no, std, group_name, parent_contact, email FROM students WHERE user_id = ? ORDER BY class_code, roll_no ASC");
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode($students);
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode([]);
}
?>