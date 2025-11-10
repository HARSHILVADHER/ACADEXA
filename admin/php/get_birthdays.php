<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

try {
    $today = date('m-d');
    $user_id = $_SESSION['user_id'];
    
    // Get student birthdays for current user only
    $query = "SELECT name, email, dob FROM students WHERE DATE_FORMAT(dob, '%m-%d') = ? AND user_id = ? ORDER BY name";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $today, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    
    // Get faculty birthdays for current user only
    $query = "SELECT name, email, dob FROM faculty WHERE DATE_FORMAT(dob, '%m-%d') = ? AND user_id = ? ORDER BY name";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $today, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'students' => $students,
        'faculty' => $faculty,
        'total_count' => count($students) + count($faculty)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>