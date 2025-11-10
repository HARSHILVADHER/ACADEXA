<?php
require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

$feesFilter = $_POST['fees_filter'] ?? 'all';

// Fetch upcoming fees installments (excluding paid ones)
$upcomingFees = [];
$feesWhere = "f.user_id = ?";
if ($feesFilter !== 'all') {
    $feesWhere .= " AND f.class_code = ?";
}
$sql = "SELECT f.student_id, f.student_name, f.class_code, f.installments 
        FROM fees_structure f 
        WHERE $feesWhere ORDER BY f.student_name LIMIT 50";
$stmt = $conn->prepare($sql);
if ($feesFilter !== 'all') {
    $stmt->bind_param("is", $user_id, $feesFilter);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $installments = json_decode($row['installments'], true);
    if ($installments) {
        foreach ($installments as $index => $installment) {
            if (strtotime($installment['due_date']) >= strtotime('today')) {
                // Check if this installment is already paid
                $paidCheck = $conn->prepare("SELECT id FROM paid_fees WHERE student_id = ? AND installment_index = ? AND user_id = ? AND is_paid = 1");
                $paidCheck->bind_param("iii", $row['student_id'], $index, $user_id);
                $paidCheck->execute();
                $paidResult = $paidCheck->get_result();
                
                // Only add to upcoming fees if not paid
                if ($paidResult->num_rows == 0) {
                    $upcomingFees[] = [
                        'student_name' => $row['student_name'],
                        'class_code' => $row['class_code'],
                        'installment_no' => $index + 1,
                        'due_date' => $installment['due_date'],
                        'amount' => $installment['amount']
                    ];
                }
                $paidCheck->close();
            }
        }
    }
}
$stmt->close();

// Sort by due date (earliest first)
usort($upcomingFees, function($a, $b) {
    return strtotime($a['due_date']) - strtotime($b['due_date']);
});

// Limit to 10 after sorting
$upcomingFees = array_slice($upcomingFees, 0, 10);

$conn->close();

echo json_encode(['fees' => $upcomingFees]);
?>