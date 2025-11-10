<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

// Check if user has income access
if (!isset($_SESSION['income_access']) || !$_SESSION['income_access']) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$source = trim($_POST['source'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);
$category = trim($_POST['category'] ?? 'Other');
$payment_method = trim($_POST['payment_method'] ?? 'Cash');
$date = $_POST['date'] ?? '';
$description = trim($_POST['description'] ?? '');

// Validation
if (empty($source)) {
    echo json_encode(['success' => false, 'message' => 'Source is required']);
    exit();
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
    exit();
}

if (empty($date)) {
    echo json_encode(['success' => false, 'message' => 'Date is required']);
    exit();
}

// Validate date format
if (!DateTime::createFromFormat('Y-m-d', $date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit();
}

try {
    // Insert income record
    $stmt = $conn->prepare("INSERT INTO income (user_id, source, amount, category, payment_method, date, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdssss", $user_id, $source, $amount, $category, $payment_method, $date, $description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Income added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add income']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>