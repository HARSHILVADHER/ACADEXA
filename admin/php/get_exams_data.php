<?php
require_once 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

$examFilter = $_POST['exam_filter'] ?? 'latest';
$examWhere = "e.user_id = ? AND e.exam_date >= CURDATE()";
if ($examFilter === 'week') {
    $examWhere .= " AND e.exam_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
} elseif ($examFilter === 'month') {
    $examWhere .= " AND e.exam_date <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)";
}

$upcomingExams = [];
$sql = "SELECT e.exam_name, e.exam_date, e.start_time, e.total_marks, c.name AS class_name
        FROM exam e
        JOIN classes c ON e.code = c.code
        WHERE $examWhere
        ORDER BY e.exam_date ASC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $upcomingExams[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['exams' => $upcomingExams]);
?>