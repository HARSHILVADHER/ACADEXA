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

$stmt = $conn->prepare("SELECT header_logo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['logo' => $row['header_logo']]);
} else {
    echo json_encode(['logo' => null]);
}

$conn->close();
?>
