<?php
require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode([]);
    exit();
}

$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

if (!$from_date || !$to_date) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT 
    pf.student_id,
    fs.student_name,
    fs.student_roll_no,
    fs.class_code,
    pf.amount,
    pf.due_date,
    pf.paid_date,
    pf.payment_mode
FROM paid_fees pf
JOIN fees_structure fs ON pf.student_id = fs.student_id AND pf.user_id = fs.user_id
WHERE pf.user_id = ? 
AND pf.is_paid = 1
AND pf.paid_date BETWEEN ? AND ?
ORDER BY pf.paid_date DESC, fs.student_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $from_date, $to_date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'student_name' => $row['student_name'],
        'student_roll_no' => $row['student_roll_no'] ?? 'N/A',
        'class_code' => $row['class_code'],
        'amount' => $row['amount'],
        'due_date' => $row['due_date'] ? date('d-m-Y', strtotime($row['due_date'])) : 'N/A',
        'paid_date' => date('d-m-Y', strtotime($row['paid_date'])),
        'payment_mode' => ucfirst($row['payment_mode'] ?? 'N/A')
    ];
}

echo json_encode($data);
$stmt->close();
$conn->close();
?>
