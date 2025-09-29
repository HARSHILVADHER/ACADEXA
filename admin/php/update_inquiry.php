<?php
// filepath: e:\XAMPP\htdocs\Acadexafinal\update_inquiry.php
require 'config.php';

$id = $_POST['inquiry_id'];
$student_name = $_POST['student_name'];
$school_name = $_POST['school_name'];
$student_mobile = $_POST['student_mobile'];
$interest_level = $_POST['interest_level'];
$followup_date = $_POST['followup_date'];
$followup_time = $_POST['followup_time'];

$stmt = $conn->prepare("UPDATE inquiry SET student_name=?, school_name=?, student_mobile=?, interest_level=?, followup_date=?, followup_time=? WHERE id=?");
$stmt->bind_param("ssssssi", $student_name, $school_name, $student_mobile, $interest_level, $followup_date, $followup_time, $id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
$stmt->close();
$conn->close();
?>