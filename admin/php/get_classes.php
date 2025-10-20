<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT code, name FROM classes WHERE user_id = ? ORDER BY name");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $classes = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = [
            'code' => $row['code'],
            'name' => $row['name']
        ];
    }
    
    echo json_encode($classes);
} catch (mysqli_sql_exception $e) {
    echo json_encode([]);
}
?>