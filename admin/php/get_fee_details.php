<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once 'config.php';

if (!isset($_GET['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Student ID required']);
    exit();
}

$user_id = $_SESSION['user_id'];
$student_id = $_GET['student_id'];

$stmt = $conn->prepare("SELECT * FROM fees_structure WHERE student_id = ? AND user_id = ?");
$stmt->bind_param("ii", $student_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $fee_data = $result->fetch_assoc();
    
    // Get paid fees status
    $paid_stmt = $conn->prepare("SELECT installment_index, is_paid, payment_mode FROM paid_fees WHERE student_id = ? AND user_id = ?");
    $paid_stmt->bind_param("ii", $student_id, $user_id);
    $paid_stmt->execute();
    $paid_result = $paid_stmt->get_result();
    
    $paid_status = [];
    while ($row = $paid_result->fetch_assoc()) {
        $paid_status[$row['installment_index']] = [
            'is_paid' => $row['is_paid'],
            'payment_mode' => $row['payment_mode']
        ];
    }
    $paid_stmt->close();
    
    $fee_data['paid_status'] = $paid_status;
    
    echo json_encode([
        'success' => true,
        'fee_data' => $fee_data
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No fee structure found'
    ]);
}

$stmt->close();
$conn->close();
?>