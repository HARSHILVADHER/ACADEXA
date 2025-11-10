<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$class_code = $_GET['class_code'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

if (empty($class_code) || empty($start_date) || empty($end_date)) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT 
    COUNT(CASE WHEN a.status = 'present' THEN 1 END) as total_present,
    COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as total_absent,
    COUNT(DISTINCT a.date) as total_days
FROM attendance a
INNER JOIN classes c ON a.class_code = c.code
WHERE a.class_code = ? 
    AND c.user_id = ? 
    AND a.date BETWEEN ? AND ?");

$stmt->bind_param('siss', $class_code, $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

echo json_encode([
    'success' => true,
    'total_present' => $stats['total_present'] ?? 0,
    'total_absent' => $stats['total_absent'] ?? 0,
    'total_days' => $stats['total_days'] ?? 0,
    'class_code' => $class_code,
    'start_date' => $start_date,
    'end_date' => $end_date
]);

$stmt->close();
$conn->close();
?>
