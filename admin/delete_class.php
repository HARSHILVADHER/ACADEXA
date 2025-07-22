<?php
// filepath: e:\XAMPP\htdocs\Acadexafinal\delete_class.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['classCode'])) {
    $classCode = $_POST['classCode'];

    // Get the class id from the class code
    $stmt = $conn->prepare("SELECT id FROM classes WHERE code = ?");
    $stmt->bind_param("s", $classCode);
    $stmt->execute();
    $stmt->bind_result($classId);
    if ($stmt->fetch()) {
        $stmt->close();

        // 1. Delete exams with this class_id
        $stmtExam = $conn->prepare("DELETE FROM exam WHERE class_id = ?");
        $stmtExam->bind_param("i", $classId);
        $stmtExam->execute();
        $stmtExam->close();

        // 2. Delete students with this class_code
        $stmt1 = $conn->prepare("DELETE FROM students WHERE class_code = ?");
        $stmt1->bind_param("s", $classCode);
        $stmt1->execute();
        $stmt1->close();

        // 3. Delete the class
        $stmt2 = $conn->prepare("DELETE FROM classes WHERE id = ?");
        $stmt2->bind_param("i", $classId);
        $stmt2->execute();
        $stmt2->close();

        echo "success";
    } else {
        echo "Class not found";
    }
} else {
    echo "Invalid request";
}
?>