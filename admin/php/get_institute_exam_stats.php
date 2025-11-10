<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['total_exams' => 0, 'classes' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM exam e 
                       INNER JOIN classes c ON e.code = c.code AND c.user_id = e.user_id
                       WHERE e.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_exams = $result->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT * FROM classes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

echo json_encode(['total_exams' => $total_exams, 'classes' => $classes]);
$conn->close();
?>
