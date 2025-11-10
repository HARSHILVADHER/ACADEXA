<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Not authenticated', 'decided' => 0, 'paid' => 0, 'pending' => 0]);
    exit();
}

$totalDecided = 0;
$totalPaid = 0;

try {
    // Get total decided fees
    $sql = "SELECT COALESCE(SUM(decided_fees), 0) as total FROM fees_structure WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $totalDecided = floatval($row['total']);
        }
        $stmt->close();
    }

    // Get total paid fees
    $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM paid_fees WHERE user_id = ? AND is_paid = 1";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $totalPaid = floatval($row['total']);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Silent error handling
}

$totalPending = $totalDecided - $totalPaid;

$conn->close();

echo json_encode([
    'decided' => $totalDecided,
    'paid' => $totalPaid,
    'pending' => $totalPending
]);
?>
