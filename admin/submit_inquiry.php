<?php
session_start(); // Add this to enable session
require_once 'config.php';

// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "User not authenticated!";
    exit();
}

// Get form data safely
$student_name = $_POST['student_name'];
$student_mobile = $_POST['student_mobile'];
$father_mobile = $_POST['father_mobile'];
$school_name = $_POST['school_name'];
$percentage = $_POST['percentage'];
$std = $_POST['std'];
$medium = $_POST['medium'];
$group_name = $_POST['group_name'];
$reference_by = $_POST['reference_by'];
$interest_level = $_POST['interest_level'];
$followup_date = $_POST['followup_date'];
$followup_time = $_POST['followup_time'];
$notes = $_POST['notes'];

// Insert into database with user_id
$sql = "INSERT INTO inquiry 
(student_name, student_mobile, father_mobile, school_name, percentage, std, medium, group_name, reference_by, interest_level, followup_date, followup_time, notes, user_id)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssssssssi", 
    $student_name, $student_mobile, $father_mobile, $school_name, $percentage, $std, $medium, $group_name, $reference_by, $interest_level, $followup_date, $followup_time, $notes, $user_id
);

if ($stmt->execute()) {
    header("Location: inquiry.php"); // redirect to form after success
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
