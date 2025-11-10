<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';

header('Content-Type: application/json');

try {
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $id = $_POST['id'];
        
        $sql = "DELETE FROM study_materials WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id, $user_id);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Material deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed or unauthorized']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>