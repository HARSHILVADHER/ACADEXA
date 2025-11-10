<?php
require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode([]);
    exit();
}

$class_code = $_POST['class_code'] ?? '';
$status_filter = $_POST['status'] ?? 'all';

if (!$class_code) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT 
    fs.student_id,
    fs.student_name,
    fs.student_roll_no,
    fs.class_code,
    fs.installments
FROM fees_structure fs
WHERE fs.user_id = ? AND fs.class_code = ?
ORDER BY fs.student_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $class_code);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $installments = json_decode($row['installments'], true);
    
    if ($installments && is_array($installments)) {
        foreach ($installments as $index => $installment) {
            // Check if paid and get payment mode from paid_fees table
            $paidCheck = $conn->prepare("SELECT paid_date, payment_mode FROM paid_fees WHERE student_id = ? AND installment_index = ? AND user_id = ? AND is_paid = 1");
            $paidCheck->bind_param("iii", $row['student_id'], $index, $user_id);
            $paidCheck->execute();
            $paidResult = $paidCheck->get_result();
            
            $status = 'Pending';
            $paid_date = null;
            $payment_mode = null;
            
            if ($paidResult->num_rows > 0) {
                $paidRow = $paidResult->fetch_assoc();
                $status = 'Paid';
                $paid_date = date('d-m-Y', strtotime($paidRow['paid_date']));
                $payment_mode = ucfirst($paidRow['payment_mode'] ?? 'N/A');
            }
            
            // Apply status filter
            if ($status_filter === 'all' || 
                ($status_filter === 'paid' && $status === 'Paid') || 
                ($status_filter === 'pending' && $status === 'Pending')) {
                $data[] = [
                    'student_name' => $row['student_name'],
                    'student_roll_no' => $row['student_roll_no'] ?? 'N/A',
                    'installment_no' => ($index + 1),
                    'amount' => $installment['amount'],
                    'due_date' => date('d-m-Y', strtotime($installment['due_date'])),
                    'status' => $status,
                    'paid_date' => $paid_date,
                    'payment_mode' => $payment_mode
                ];
            }
            
            $paidCheck->close();
        }
    }
}

echo json_encode($data);
$stmt->close();
$conn->close();
?>
