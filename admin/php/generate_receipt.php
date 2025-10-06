<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$student_id = $input['student_id'] ?? null;
$installment_index = $input['installment_index'] ?? null;
$payment_mode = $input['payment_mode'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$student_id || $installment_index === null || !$payment_mode) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

// Get fee structure
$fee_stmt = $conn->prepare("SELECT installments FROM fees_structure WHERE student_id = ? AND user_id = ?");
$fee_stmt->bind_param("ii", $student_id, $user_id);
$fee_stmt->execute();
$fee_result = $fee_stmt->get_result()->fetch_assoc();
$fee_stmt->close();

if (!$fee_result) {
    echo json_encode(['success' => false, 'message' => 'Fee structure not found']);
    exit();
}

$installments = json_decode($fee_result['installments'], true);
if (!isset($installments[$installment_index])) {
    echo json_encode(['success' => false, 'message' => 'Invalid installment index']);
    exit();
}

$installment = $installments[$installment_index];

// Insert or update paid_fees record
$check_stmt = $conn->prepare("SELECT id, is_paid FROM paid_fees WHERE student_id = ? AND user_id = ? AND installment_index = ?");
$check_stmt->bind_param("iii", $student_id, $user_id, $installment_index);
$check_stmt->execute();
$existing = $check_stmt->get_result()->fetch_assoc();
$check_stmt->close();

if ($existing) {
    // Update existing record
    $update_stmt = $conn->prepare("UPDATE paid_fees SET payment_mode = ?, is_paid = 1, paid_date = NOW(), receipt_generated = 1 WHERE id = ?");
    $update_stmt->bind_param("si", $payment_mode, $existing['id']);
    $success = $update_stmt->execute();
    $update_stmt->close();
} else {
    // Insert new record
    $insert_stmt = $conn->prepare("INSERT INTO paid_fees (student_id, user_id, installment_index, amount, due_date, payment_mode, is_paid, paid_date, receipt_generated) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), 1)");
    $insert_stmt->bind_param("iiidss", $student_id, $user_id, $installment_index, $installment['amount'], $installment['due_date'], $payment_mode);
    $success = $insert_stmt->execute();
    $insert_stmt->close();
}

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Receipt generated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error generating receipt']);
}

$conn->close();
?>