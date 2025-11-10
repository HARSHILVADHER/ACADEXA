<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    die('Unauthorized access');
}

$user_id = $_SESSION['user_id'];
$student_id = $_GET['student_id'] ?? null;
$installment_index = $_GET['installment_index'] ?? null;

if (!$student_id || $installment_index === null) {
    die('Missing parameters');
}

// Get student info
$student_stmt = $conn->prepare("SELECT name, class_code FROM students WHERE id = ? AND user_id = ?");
$student_stmt->bind_param("ii", $student_id, $user_id);
$student_stmt->execute();
$student = $student_stmt->get_result()->fetch_assoc();
$student_stmt->close();

// Get payment info
$payment_stmt = $conn->prepare("SELECT * FROM paid_fees WHERE student_id = ? AND user_id = ? AND installment_index = ? AND is_paid = 1");
$payment_stmt->bind_param("iii", $student_id, $user_id, $installment_index);
$payment_stmt->execute();
$payment = $payment_stmt->get_result()->fetch_assoc();
$payment_stmt->close();

if (!$student || !$payment) {
    die('Payment record not found');
}

// Generate PDF receipt
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="receipt_' . $student_id . '_' . $installment_index . '.pdf"');

// Simple PDF generation (you can use libraries like TCPDF for better formatting)
$receipt_content = "
ACADEXA - FEE RECEIPT
=====================

Student Name: {$student['name']}
Class: {$student['class_code']}
Amount Paid: ₹{$payment['amount']}
Payment Mode: " . strtoupper($payment['payment_mode']) . "
Payment Date: {$payment['paid_date']}
Due Date: {$payment['due_date']}

Receipt ID: RCP-{$payment['id']}
Generated on: " . date('Y-m-d H:i:s') . "

Thank you for your payment!
";

echo $receipt_content;
$conn->close();
?>