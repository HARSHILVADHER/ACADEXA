<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['student_id']) || !isset($input['installment_index']) || !isset($input['payment_mode'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

$user_id = $_SESSION['user_id'];
$student_id = $input['student_id'];
$installment_index = $input['installment_index'];
$payment_mode = $input['payment_mode'];

// Create payment_modes table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS payment_modes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    installment_index INT NOT NULL,
    payment_mode ENUM('cash', 'cheque', 'online') NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_user (student_id, user_id)
)";
$conn->query($create_table_sql);

// Check if payment mode already exists for this installment
$check_stmt = $conn->prepare("SELECT id FROM payment_modes WHERE student_id = ? AND installment_index = ? AND user_id = ?");
$check_stmt->bind_param("iii", $student_id, $installment_index, $user_id);
$check_stmt->execute();
$exists = $check_stmt->get_result();

if ($exists->num_rows > 0) {
    // Update existing
    $update_stmt = $conn->prepare("UPDATE payment_modes SET payment_mode = ? WHERE student_id = ? AND installment_index = ? AND user_id = ?");
    $update_stmt->bind_param("siii", $payment_mode, $student_id, $installment_index, $user_id);
    $success = $update_stmt->execute();
    $update_stmt->close();
} else {
    // Insert new
    $insert_stmt = $conn->prepare("INSERT INTO payment_modes (student_id, installment_index, payment_mode, user_id) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("iisi", $student_id, $installment_index, $payment_mode, $user_id);
    $success = $insert_stmt->execute();
    $insert_stmt->close();
}

$check_stmt->close();

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Payment mode saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving payment mode']);
}

$conn->close();
?>