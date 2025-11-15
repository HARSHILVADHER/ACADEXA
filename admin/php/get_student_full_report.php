<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$student_roll = $_GET['student_roll'] ?? '';
$class_code = $_GET['class_code'] ?? '';

$conn = new mysqli('localhost', 'root', '', 'acadexa');

if($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get institute name (use username or name field)
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$institute_name = $user['username'] ?? 'Institute';

// Get student details
$stmt = $conn->prepare("SELECT DISTINCT student_name, student_roll_no FROM marks WHERE student_roll_no = ? AND class_code = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ssi", $student_roll, $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if(!$student) {
    echo json_encode(['error' => 'Student not found']);
    $conn->close();
    exit;
}

$student['email'] = 'N/A';

// Get class name
$stmt = $conn->prepare("SELECT name FROM classes WHERE code = ? AND user_id = ?");
$stmt->bind_param("si", $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();

// Get all exam marks with dates
$stmt = $conn->prepare("SELECT m.exam_name, e.exam_date, m.actual_marks, m.total_marks 
                       FROM marks m 
                       LEFT JOIN exam e ON m.exam_name = e.exam_name AND m.class_code = e.code AND e.user_id = ?
                       WHERE m.student_roll_no = ? AND m.class_code = ? AND m.user_id = ? 
                       ORDER BY COALESCE(e.exam_date, '9999-12-31') ASC");
$stmt->bind_param("issi", $user_id, $student_roll, $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$exams = [];
while($row = $result->fetch_assoc()) {
    $exams[] = $row;
}

// Get class ranking
$rank = 1;
$total_students = 1;

$stmt = $conn->prepare("SELECT student_roll_no, SUM(actual_marks) as total 
                       FROM marks 
                       WHERE class_code = ? AND user_id = ? 
                       GROUP BY student_roll_no 
                       ORDER BY total DESC");
if($stmt) {
    $stmt->bind_param("si", $class_code, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $total_students = 0;
    while($row = $result->fetch_assoc()) {
        $total_students++;
        if($row['student_roll_no'] == $student_roll) {
            $rank = $total_students;
        }
    }
    if($total_students == 0) {
        $total_students = 1;
    }
}

// Get attendance data using student_id
$attendance = ['total_days' => 0, 'present_days' => 0, 'absent_days' => 0];

$stmt = $conn->prepare("SELECT COUNT(*) as total_days, 
                       SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
                       SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days
                       FROM attendance 
                       WHERE student_id = ? AND class_code = ? AND user_id = ?");
$stmt->bind_param("isi", $student_roll, $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$att = $result->fetch_assoc();
if($att && $att['total_days'] > 0) {
    $attendance = $att;
}

echo json_encode([
    'institute_name' => $institute_name,
    'class_name' => $class['name'] ?? '',
    'student' => $student,
    'exams' => $exams,
    'rank' => $rank,
    'total_students' => $total_students,
    'attendance' => $attendance
]);

$conn->close();
?>
