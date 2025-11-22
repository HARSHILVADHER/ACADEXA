<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['logo' => null]);
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'acadexa');

if($conn->connect_error) {
    echo json_encode(['logo' => null]);
    exit;
}

$stmt = $conn->prepare("SELECT logo_path FROM user_logos WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $logo_path = $row['logo_path'];
    // Return path relative to admin folder (where HTML files are)
    echo json_encode(['logo' => '../' . $logo_path]);
} else {
    echo json_encode(['logo' => null]);
}

$conn->close();
?>
