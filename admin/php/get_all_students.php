<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT s.id, s.name, s.dob, s.medium, s.roll_no, s.std, s.parent_contact, s.student_contact, s.email, s.class_code, s.group_name, c.name as class_name FROM students s LEFT JOIN classes c ON s.class_code = c.code AND s.user_id = c.user_id WHERE s.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($students);
} catch (Exception $e) {
    echo json_encode([]);
}
?>