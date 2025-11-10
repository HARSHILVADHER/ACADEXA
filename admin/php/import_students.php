<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error']);
    exit;
}

$classCode = $_POST['classCode'] ?? '';
if (!$classCode) {
    echo json_encode(['status' => 'error', 'message' => 'Class code is required']);
    exit;
}

$file = $_FILES['csvFile']['tmp_name'];
$handle = fopen($file, 'r');

if (!$handle) {
    echo json_encode(['status' => 'error', 'message' => 'Could not read file']);
    exit;
}

$header = fgetcsv($handle); // Skip header row
$imported = 0;
$skipped = 0;
$errors = [];

while (($data = fgetcsv($handle)) !== FALSE) {
    $rowNum = $imported + $skipped + 2;
    
    if (count($data) < 8) {
        $errors[] = "Row $rowNum: Insufficient columns";
        $skipped++;
        continue;
    }
    
    $name = trim($data[0]);
    $dob = trim($data[1]);
    $medium = trim($data[2]);
    $roll_no = trim($data[3]);
    $std = trim($data[4]);
    $parent_contact = trim($data[5]);
    $student_contact = trim($data[6]) ?: null;
    $email = trim($data[7]);
    $group_name = isset($data[8]) ? trim($data[8]) : null;
    
    if (empty($group_name)) $group_name = null;
    
    if (!$name || !$dob || !$medium || !$roll_no || !$std || !$parent_contact || !$email) {
        $errors[] = "Row $rowNum: Missing required fields";
        $skipped++;
        continue;
    }
    
    // Check for duplicates - only check roll_no + class_code combination
    $checkStmt = $conn->prepare("SELECT id FROM students WHERE roll_no = ? AND class_code = ? AND user_id = ?");
    $checkStmt->bind_param("ssi", $roll_no, $classCode, $user_id);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        $errors[] = "Row $rowNum: Student with roll number $roll_no already exists in class $classCode";
        $checkStmt->close();
        $skipped++;
        continue;
    }
    $checkStmt->close();
    
    $stmt = $conn->prepare("INSERT INTO students (name, dob, medium, roll_no, std, parent_contact, student_contact, email, class_code, group_name, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssi", $name, $dob, $medium, $roll_no, $std, $parent_contact, $student_contact, $email, $classCode, $group_name, $user_id);
    
    if ($stmt->execute()) {
        $imported++;
    } else {
        $errors[] = "Row $rowNum: " . $stmt->error;
        $skipped++;
    }
    $stmt->close();
}

fclose($handle);

if ($imported > 0) {
    $message = "Successfully imported $imported students";
    if ($skipped > 0) {
        $message .= ". Skipped $skipped duplicate/invalid records";
    }
    if (!empty($errors)) {
        $message .= ". First 3 issues: " . implode('; ', array_slice($errors, 0, 3));
    }
    echo json_encode(['status' => 'success', 'count' => $imported, 'skipped' => $skipped, 'message' => $message]);
} else {
    $message = 'No students imported';
    if ($skipped > 0) {
        $message .= ". $skipped records skipped (duplicates/errors)";
    }
    if (!empty($errors)) {
        $message .= ". Issues: " . implode('; ', array_slice($errors, 0, 5));
    }
    echo json_encode(['status' => 'error', 'message' => $message]);
}

$conn->close();
?>