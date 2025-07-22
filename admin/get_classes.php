<?php
session_start(); // ✅ Start session to access user ID
require_once 'config.php';

// ✅ Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized access."]);
    exit;
}

// ✅ Use prepared statement to fetch only this user's classes
$stmt = $conn->prepare("SELECT name, code FROM classes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = [
            "name" => $row["name"],
            "code" => $row["code"]
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($classes);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query failed."]);
}

$stmt->close();
$conn->close();
?>
