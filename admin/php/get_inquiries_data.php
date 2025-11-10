<?php
require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

$filter = $_POST['inq_filter'] ?? 'latest';
$where = "WHERE user_id = ?";
if ($filter === 'week') {
    $where .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filter === 'month') {
    $where .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
}

$inquiries = [];
$sqlInq = "SELECT student_name, student_mobile, school_name, std, medium, group_name, reference_by, interest_level, followup_date, followup_time, notes, created_at FROM inquiry $where ORDER BY created_at DESC LIMIT 20";
$stmt = $conn->prepare($sqlInq);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultInq = $stmt->get_result();
if ($resultInq) {
    while ($row = $resultInq->fetch_assoc()) {
        $inquiries[] = $row;
    }
}
$stmt->close();
$conn->close();

echo json_encode(['inquiries' => $inquiries]);
?>