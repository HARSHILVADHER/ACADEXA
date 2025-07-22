<?php

session_start();
require_once 'config.php';

$name = $_POST['name'] ?? '';
$age = $_POST['age'] ?? '';
$grade = $_POST['grade'] ?? '';
$contact = $_POST['contact'] ?? '';
$email = $_POST['email'] ?? '';
$classCode = $_POST['classCode'] ?? '';
$user_id = $_SESSION['user_id'] ?? null; // Get the logged-in user's ID

if ($name && $age && $grade && $contact && $email && $classCode && $user_id) {
  $stmt = $conn->prepare("INSERT INTO students (name, age, grade, contact, email, class_code, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sissssi", $name, $age, $grade, $contact, $email, $classCode, $user_id);
  if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
  }
  $stmt->close();
} else {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
}

$conn->close();
?>