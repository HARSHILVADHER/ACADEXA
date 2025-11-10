<?php
header('Content-Type: application/json');
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode([]);
    exit();
}

require_once 'config.php';

$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

if (empty($from_date) || empty($to_date)) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT 
            student_name,
            student_mobile,
            father_mobile,
            school_name,
            std,
            medium,
            group_name,
            reference_by,
            interest_level,
            followup_date,
            followup_time,
            notes,
            created_at
        FROM inquiry 
        WHERE user_id = ? 
        AND DATE(created_at) BETWEEN ? AND ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $from_date, $to_date);
$stmt->execute();
$result = $stmt->get_result();

$inquiries = [];
while ($row = $result->fetch_assoc()) {
    $inquiries[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($inquiries);
?>
