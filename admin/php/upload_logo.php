<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';
ob_end_clean();

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized');
    }

    if (!isset($_FILES['logo'])) {
        throw new Exception('No file uploaded');
    }

    $user_id = $_SESSION['user_id'];
    $file = $_FILES['logo'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error: ' . $file['error']);
    }
    
    if ($file['size'] > 2097152) {
        throw new Exception('File too large');
    }
    
    $upload_dir = dirname(dirname(__DIR__)) . '/uploads/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $filename = 'logo_' . $user_id . '_' . time() . '.jpg';
    $upload_path = $upload_dir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to save file');
    }
    
    $logo_path = 'uploads/' . $filename;
    
    $stmt = $conn->prepare("INSERT INTO user_logos (user_id, logo_path) VALUES (?, ?) ON DUPLICATE KEY UPDATE logo_path = VALUES(logo_path)");
    $stmt->bind_param('is', $user_id, $logo_path);
    
    if (!$stmt->execute()) {
        throw new Exception('Database error');
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode(['success' => true, 'logo_path' => $logo_path]);
    
} catch (Exception $e) {
    if (isset($conn)) $conn->close();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
