<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

try {
    $data = [];

    // Inquiries count
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM inquiry WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data['inquiryCount'] = $result->fetch_assoc()['total'];
    $stmt->close();

    // Students count
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM students WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data['studentCount'] = $result->fetch_assoc()['total'];
    $stmt->close();

    // Classes count
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM classes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data['classCount'] = $result->fetch_assoc()['total'];
    $stmt->close();

    // Attendance percentage
    // Check if user_id column exists in attendance table
    $result = $conn->query("SHOW COLUMNS FROM attendance LIKE 'user_id'");
    if ($result->num_rows == 0) {
        $conn->query("ALTER TABLE attendance ADD COLUMN user_id INT DEFAULT NULL");
    }
    
    $stmt = $conn->prepare("SELECT 
        COUNT(*) as total_records,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count
        FROM attendance 
        WHERE user_id = ? AND date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendanceData = $result->fetch_assoc();
    $data['attendancePercentage'] = 0;
    if ($attendanceData['total_records'] > 0) {
        $data['attendancePercentage'] = round(($attendanceData['present_count'] / $attendanceData['total_records']) * 100);
    }
    $stmt->close();

    // Recent inquiries
    $filter = $_GET['inq_filter'] ?? 'latest';
    $where = "WHERE user_id = ?";
    if ($filter === 'week') {
        $where .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($filter === 'month') {
        $where .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
    }
    
    $stmt = $conn->prepare("SELECT student_name, student_mobile, std, medium, interest_level, followup_date, followup_time FROM inquiry $where ORDER BY created_at DESC LIMIT 20");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data['inquiries'] = [];
    while ($row = $result->fetch_assoc()) {
        $data['inquiries'][] = $row;
    }
    $stmt->close();

    // Upcoming exams
    $examFilter = $_GET['exam_filter'] ?? 'latest';
    $examWhere = "e.user_id = ? AND e.exam_date >= CURDATE()";
    if ($examFilter === 'week') {
        $examWhere .= " AND e.exam_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($examFilter === 'month') {
        $examWhere .= " AND e.exam_date <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)";
    }
    
    $stmt = $conn->prepare("SELECT e.exam_name, e.exam_date, e.start_time, e.total_marks, c.name AS class_name FROM exam e JOIN classes c ON e.code = c.code WHERE $examWhere ORDER BY e.exam_date ASC LIMIT 5");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data['upcomingExams'] = [];
    while ($row = $result->fetch_assoc()) {
        $data['upcomingExams'][] = $row;
    }
    $stmt->close();

    // Tasks
    $taskRoleFilter = $_GET['task_role_filter'] ?? '';
    $taskWhere = "WHERE user_id = ?";
    $params = [$user_id];
    $types = "i";
    
    if ($taskRoleFilter && in_array($taskRoleFilter, ['admin', 'trustee', 'tutor'])) {
        $taskWhere .= " AND task_for = ?";
        $params[] = $taskRoleFilter;
        $types .= "s";
    }
    
    $stmt = $conn->prepare("SELECT task_text, task_date, task_for FROM task $taskWhere ORDER BY task_date DESC LIMIT 10");
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $data['userTasks'] = [];
    while ($row = $result->fetch_assoc()) {
        $data['userTasks'][] = $row;
    }
    $stmt->close();

    $conn->close();
    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error']);
}
?>