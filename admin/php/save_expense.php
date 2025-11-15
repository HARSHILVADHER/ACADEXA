<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['records']) || !is_array($input['records'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$saved = 0;

foreach ($input['records'] as $data) {
    if (!isset($data['date'], $data['source'], $data['category'], $data['payment_method'], $data['amount'])) {
        continue;
    }
    
    $date = $data['date'];
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $dateObj = date_create($date);
        if ($dateObj) {
            $date = date_format($dateObj, 'Y-m-d');
        } else {
            continue;
        }
    }
    
    $checkStmt = $conn->prepare("SELECT id FROM expense WHERE user_id = ? AND date = ? AND source = ? AND category = ? AND payment_method = ? AND amount = ?");
    $checkStmt->bind_param("issssd", $user_id, $date, $data['source'], $data['category'], $data['payment_method'], $data['amount']);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        continue;
    }
    $checkStmt->close();
    
    $stmt = $conn->prepare("INSERT INTO expense (user_id, date, source, description, category, payment_method, amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssd", $user_id, $date, $data['source'], $data['description'], $data['category'], $data['payment_method'], $data['amount']);
    
    if ($stmt->execute()) {
        $saved++;
    }
    $stmt->close();
}

$conn->close();

if ($saved > 0) {
    echo json_encode(['success' => true, 'saved' => $saved]);
} else {
    echo json_encode(['success' => false, 'message' => 'No records saved']);
}
