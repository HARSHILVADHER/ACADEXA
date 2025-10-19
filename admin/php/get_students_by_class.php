<?php
header('Content-Type: application/json');
session_start();
require_once 'config.php';

try {
    $classCode = $_POST['class_code'] ?? $_GET['classCode'] ?? '';
    
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
        // Return different format based on request type
        if (isset($_POST['class_code'])) {
            echo json_encode(['success' => false, 'students' => []]);
        } else {
            echo json_encode([]);
        }
        exit;
    }

    $user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'];

    if ($classCode) {
        $stmt = $conn->prepare("SELECT s.id, s.name, s.roll_no, s.class_code, s.std, s.group_name, s.parent_contact, s.email,
                              CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END as has_fees
                              FROM students s 
                              LEFT JOIN fees_structure f ON s.id = f.student_id AND s.user_id = f.user_id
                              WHERE s.class_code = ? AND s.user_id = ? 
                              ORDER BY s.roll_no ASC");
        $stmt->bind_param("si", $classCode, $user_id);
    } else {
        $stmt = $conn->prepare("SELECT s.id, s.name, s.roll_no, s.class_code, s.std, s.group_name, s.parent_contact, s.email,
                              CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END as has_fees
                              FROM students s 
                              LEFT JOIN fees_structure f ON s.id = f.student_id AND s.user_id = f.user_id
                              WHERE s.user_id = ? 
                              ORDER BY s.class_code, s.roll_no ASC");
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    // Return different format based on request type
    if (isset($_POST['class_code'])) {
        // For fees.php (POST request)
        echo json_encode(['success' => true, 'students' => $students]);
    } else {
        // For addstudents.html (GET request)
        echo json_encode($students);
    }
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    if (isset($_POST['class_code'])) {
        echo json_encode(['success' => false, 'students' => []]);
    } else {
        echo json_encode([]);
    }
}
?>