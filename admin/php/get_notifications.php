<?php
require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Get birthday count (today)
$birthdayCount = 0;
$sql = "SELECT COUNT(*) as count FROM students WHERE user_id = ? AND DATE_FORMAT(dob, '%m-%d') = DATE_FORMAT(?, '%m-%d')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $birthdayCount = $row['count'];
}
$stmt->close();

// Get faculty birthday count (today)
$sql = "SELECT COUNT(*) as count FROM faculty WHERE user_id = ? AND DATE_FORMAT(dob, '%m-%d') = DATE_FORMAT(?, '%m-%d')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $birthdayCount += $row['count'];
}
$stmt->close();

// Get fees due count (today and tomorrow)
$feesCount = 0;
$sql = "SELECT f.student_id, f.installments FROM fees_structure f WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $installments = json_decode($row['installments'], true);
    if ($installments) {
        foreach ($installments as $index => $installment) {
            $dueDate = $installment['due_date'];
            if ($dueDate === $today || $dueDate === $tomorrow) {
                // Check if not paid
                $paidCheck = $conn->prepare("SELECT id FROM paid_fees WHERE student_id = ? AND installment_index = ? AND user_id = ? AND is_paid = 1");
                $paidCheck->bind_param("iii", $row['student_id'], $index, $user_id);
                $paidCheck->execute();
                $paidResult = $paidCheck->get_result();
                if ($paidResult->num_rows == 0) {
                    $feesCount++;
                }
                $paidCheck->close();
            }
        }
    }
}
$stmt->close();

// Get inquiry followup count (today)
$inquiryCount = 0;
$sql = "SELECT COUNT(*) as count FROM inquiry WHERE user_id = ? AND followup_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $inquiryCount = $row['count'];
}
$stmt->close();

$conn->close();

echo json_encode([
    'birthdays' => $birthdayCount,
    'fees' => $feesCount,
    'inquiries' => $inquiryCount
]);
?>
