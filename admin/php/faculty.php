<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../../login.html");
    exit();
}

// Fetch subjects grouped by class
$subjects = [];
$sql = "SELECT class_name, subject_name, subject_code FROM subjects WHERE user_id = ? ORDER BY class_name, subject_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
$stmt->close();

// Group subjects by class
$groupedSubjects = [];
foreach ($subjects as $subject) {
    $className = $subject['class_name'];
    if (!isset($groupedSubjects[$className])) {
        $groupedSubjects[$className] = [];
    }
    $groupedSubjects[$className][] = $subject;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management - Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
