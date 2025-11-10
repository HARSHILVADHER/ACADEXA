<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo 'error: not authenticated';
    exit();
}

$exam_name = $_POST['exam_name'] ?? null;
$class_code = $_POST['class_code'] ?? null;
$exam_date = $_POST['exam_date'] ?? null;
$total_marks = $_POST['total_marks'] ?? null;
$passing_marks = $_POST['passing_marks'] ?? null;
$marks = $_POST['marks'] ?? [];

if (!$exam_name || !$class_code || !$exam_date || !$total_marks || !$passing_marks) {
    echo 'error: missing required fields';
    exit();
}

try {
    $conn->begin_transaction();
    
    // Prepare insert/update statement
    $sql = "INSERT INTO marks (class_code, student_roll_no, student_name, exam_name, exam_date, 
            passing_marks, total_marks, actual_marks, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE actual_marks = VALUES(actual_marks), 
            exam_date = VALUES(exam_date), 
            passing_marks = VALUES(passing_marks), 
            total_marks = VALUES(total_marks)";
    
    $stmt = $conn->prepare($sql);
    
    foreach ($marks as $roll_no => $actual_marks) {
        // Skip empty marks
        if ($actual_marks === '' || $actual_marks === null) {
            continue;
        }
        
        // Validate marks
        if ($actual_marks < 0 || $actual_marks > $total_marks) {
            throw new Exception("Invalid marks for roll no: $roll_no");
        }
        
        // Get student name
        $sql_student = "SELECT name FROM students WHERE roll_no = ? AND class_code = ?";
        $stmt_student = $conn->prepare($sql_student);
        $stmt_student->bind_param("ss", $roll_no, $class_code);
        $stmt_student->execute();
        $result = $stmt_student->get_result();
        $student = $result->fetch_assoc();
        $stmt_student->close();
        
        if (!$student) {
            throw new Exception("Student not found: $roll_no");
        }
        
        $student_name = $student['name'];
        
        // Insert/Update marks
        $stmt->bind_param("sssssiiii", 
            $class_code, 
            $roll_no, 
            $student_name, 
            $exam_name, 
            $exam_date, 
            $passing_marks, 
            $total_marks, 
            $actual_marks, 
            $user_id
        );
        
        $stmt->execute();
    }
    
    $stmt->close();
    $conn->commit();
    $conn->close();
    
    echo 'success';
} catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    echo 'error: ' . $e->getMessage();
}
?>
