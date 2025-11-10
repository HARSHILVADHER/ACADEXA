<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode([]);
    exit();
}

$classCode = $_GET['classCode'] ?? '';
if (empty($classCode)) {
    echo json_encode([]);
    exit();
}

try {
    $sql = "SELECT id, name, roll_no, parent_contact FROM students WHERE class_code = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $classCode, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'roll_no' => $row['roll_no'] ?? '',
            'parent_contact' => $row['parent_contact'] ?? '',
            'grade' => '',
            'email' => ''
        ];
    }
    
    echo json_encode($students);
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([]);
}

$conn->close();
?>
