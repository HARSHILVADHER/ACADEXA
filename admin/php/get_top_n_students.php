<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$exam_ids = isset($_POST['exam_ids']) ? explode(',', $_POST['exam_ids']) : [];
$top_n = isset($_POST['top_n']) ? intval($_POST['top_n']) : 20;

if(empty($exam_ids)) {
    echo json_encode([]);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'acadexa');

if($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($exam_ids), '?'));
$types = str_repeat('i', count($exam_ids));

$stmt = $conn->prepare("SELECT e.exam_name, e.code, e.total_marks FROM exam e WHERE e.id IN ($placeholders) AND e.user_id = ?");
$params = array_merge($exam_ids, [$user_id]);
$stmt->bind_param($types . 'i', ...$params);
$stmt->execute();
$result = $stmt->get_result();

$students_data = [];

while($exam = $result->fetch_assoc()) {
    $exam_name = $exam['exam_name'];
    $class_code = $exam['code'];
    $total_marks = $exam['total_marks'];
    
    $marks_stmt = $conn->prepare("SELECT student_name, student_roll_no, actual_marks, class_code FROM marks WHERE exam_name = ? AND class_code = ? AND user_id = ?");
    $marks_stmt->bind_param("ssi", $exam_name, $class_code, $user_id);
    $marks_stmt->execute();
    $marks_result = $marks_stmt->get_result();
    
    while($mark = $marks_result->fetch_assoc()) {
        $key = $mark['student_roll_no'] . '_' . $mark['class_code'];
        
        if(!isset($students_data[$key])) {
            $students_data[$key] = [
                'student_name' => $mark['student_name'],
                'roll_no' => $mark['student_roll_no'],
                'class_code' => $mark['class_code'],
                'total_marks' => 0,
                'max_marks' => 0
            ];
        }
        
        $students_data[$key]['total_marks'] += intval($mark['actual_marks']);
        $students_data[$key]['max_marks'] += intval($total_marks);
    }
}

foreach($students_data as &$student) {
    $student['percentage'] = $student['max_marks'] > 0 ? round(($student['total_marks'] / $student['max_marks']) * 100, 2) : 0;
}

usort($students_data, function($a, $b) {
    return $b['percentage'] <=> $a['percentage'];
});

$top_students = array_slice($students_data, 0, $top_n);

echo json_encode($top_students);
$conn->close();
?>
