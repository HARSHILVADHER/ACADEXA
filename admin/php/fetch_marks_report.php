<?php
ob_start();
session_start();
require_once 'config.php';
ob_end_clean();

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized');
    }

    $user_id = $_SESSION['user_id'];
    $class_code = $_GET['class_code'] ?? '';
    $exam_code = $_GET['exam_code'] ?? '';
    $subject_code = $_GET['subject_code'] ?? '';

    $marks_data = [];

    if ($class_code && $exam_code) {
        $stmt = $conn->prepare("
            SELECT m.student_name, m.actual_marks, m.total_marks, m.exam_name
            FROM marks m
            WHERE m.user_id = ? AND m.class_code = ? AND m.exam_code = ?
            ORDER BY CAST(m.actual_marks AS DECIMAL(10,2)) DESC
        ");
        $stmt->bind_param('iss', $user_id, $class_code, $exam_code);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $marks_data[] = $row;
        }
        $stmt->close();
    } elseif ($class_code && $subject_code) {
        $stmt = $conn->prepare("
            SELECT m.student_name, m.actual_marks, m.total_marks, m.exam_name
            FROM marks m
            WHERE m.user_id = ? AND m.class_code = ? AND m.exam_name = ?
            ORDER BY CAST(m.actual_marks AS DECIMAL(10,2)) DESC
        ");
        $stmt->bind_param('iss', $user_id, $class_code, $subject_code);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $marks_data[] = $row;
        }
        $stmt->close();
    }

    $conn->close();
    echo json_encode(['success' => true, 'data' => $marks_data]);
    
} catch (Exception $e) {
    if (isset($conn)) $conn->close();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
