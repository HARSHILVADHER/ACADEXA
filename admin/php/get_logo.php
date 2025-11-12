<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';
ob_end_clean();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['logo_path' => null]);
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT logo_path FROM user_logos WHERE user_id = ? LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo json_encode(['logo_path' => $row ? $row['logo_path'] : null]);
