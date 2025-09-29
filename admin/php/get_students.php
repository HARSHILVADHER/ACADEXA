<?php
include 'config.php';

$classCode = $_GET['classCode'] ?? '';

$sql = "SELECT id, name, age, contact FROM students WHERE class_code='" . $conn->real_escape_string($classCode) . "'";
$result = $conn->query($sql);

$students = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($students);

$conn->close();
?>
