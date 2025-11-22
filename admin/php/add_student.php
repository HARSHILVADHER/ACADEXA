<?php

session_start();
require_once 'config.php';

$name = $_POST['name'] ?? '';
$dob = $_POST['dob'] ?? '';
$medium = $_POST['medium'] ?? '';
$roll_no = $_POST['roll_no'] ?? '';
$std = $_POST['std'] ?? '';
$parent_contact = $_POST['parent_contact'] ?? '';
$student_contact = $_POST['student_contact'] ?? '';
$email = $_POST['email'] ?? '';
$classCode = $_POST['classCode'] ?? '';
$group_name = $_POST['group_name'] ?? '';
$date_of_joining = $_POST['date_of_joining'] ?? '';
// Convert empty string to null for database storage
if (empty($group_name)) {
    $group_name = null;
}
$user_id = $_SESSION['user_id'] ?? null;

// Debug: Log the received data
error_log("Received data: " . print_r($_POST, true));

if ($name && $dob && $medium && $roll_no && $std && $parent_contact && $email && $classCode && $date_of_joining && $user_id) {
  // Check for duplicates - only check roll_no + class_code combination
  $checkStmt = $conn->prepare("SELECT id FROM students WHERE roll_no = ? AND class_code = ? AND user_id = ?");
  $checkStmt->bind_param("ssi", $roll_no, $classCode, $user_id);
  $checkStmt->execute();
  $checkStmt->store_result();
  
  if ($checkStmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Student with roll number $roll_no already exists in class $classCode"]);
    $checkStmt->close();
  } else {
    $checkStmt->close();
    $stmt = $conn->prepare("INSERT INTO students (name, dob, medium, roll_no, std, parent_contact, student_contact, email, class_code, group_name, date_of_joining, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssi", $name, $dob, $medium, $roll_no, $std, $parent_contact, $student_contact, $email, $classCode, $group_name, $date_of_joining, $user_id);
    if ($stmt->execute()) {
      echo json_encode(["status" => "success"]);
    } else {
      echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    $stmt->close();
  }
} else {
  echo json_encode(["status" => "error", "message" => "Missing required fields"]);
}

$conn->close();
?>