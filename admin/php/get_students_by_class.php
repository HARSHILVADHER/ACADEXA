<?php
header('Content-Type: application/json');
session_start();
require_once 'config.php';

try {
    $classCode = $_POST['class_code'] ?? $_GET['classCode'] ?? $_GET['class_code'] ?? '';
    
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
        if (isset($_POST['class_code'])) {
            echo json_encode(['success' => false, 'students' => []]);
        } else {
            echo json_encode([]);
        }
        exit;
    }

    $user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'];

    if ($classCode) {
        $stmt = $conn->prepare("SELECT s.id, s.name, s.roll_no, s.class_code 
                              FROM students s 
                              INNER JOIN classes c ON s.class_code = c.code
                              WHERE s.class_code = ? AND c.user_id = ? 
                              ORDER BY s.roll_no ASC");
        $stmt->bind_param("si", $classCode, $user_id);
    } else {
        $stmt = $conn->prepare("SELECT s.id, s.name, s.roll_no, s.class_code 
                              FROM students s 
                              INNER JOIN classes c ON s.class_code = c.code
                              WHERE c.user_id = ? 
                              ORDER BY s.class_code, s.roll_no ASC");
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    if (isset($_POST['class_code'])) {
        echo json_encode(['success' => true, 'students' => $students]);
    } else {
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