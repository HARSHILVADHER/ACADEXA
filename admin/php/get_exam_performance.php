<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'acadexa');

if($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$exam_id = isset($_POST['exam_id']) ? intval($_POST['exam_id']) : 0;
$user_id = $_SESSION['user_id'];

if($exam_id <= 0) {
    echo json_encode(['error' => 'Invalid exam ID']);
    exit;
}

// Get exam details
$stmt = $conn->prepare("SELECT total_marks, passing_marks, code FROM exam WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $exam_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode(['error' => 'Exam not found']);
    exit;
}

$exam = $result->fetch_assoc();
$total_marks = $exam['total_marks'];
$passing_marks = $exam['passing_marks'];
$class_code = $exam['code'];

// Get all student marks for this exam
$stmt = $conn->prepare("SELECT actual_marks FROM marks WHERE exam_name = (SELECT exam_name FROM exam WHERE id = ?) AND class_code = ? AND user_id = ?");
$stmt->bind_param("isi", $exam_id, $class_code, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$marks = [];
while($row = $result->fetch_assoc()) {
    $marks[] = intval($row['actual_marks']);
}

if(count($marks) === 0) {
    echo json_encode([
        'high_count' => 0,
        'average_count' => 0,
        'poor_count' => 0
    ]);
    exit;
}

// Calculate thresholds
$high_threshold = $total_marks * 0.75; // 75% and above
$average_threshold = $passing_marks; // Between passing marks and 75%

$high_count = 0;
$average_count = 0;
$poor_count = 0;

foreach($marks as $mark) {
    if($mark >= $high_threshold) {
        $high_count++;
    } elseif($mark >= $average_threshold) {
        $average_count++;
    } else {
        $poor_count++;
    }
}

$highest_marks = max($marks);
$lowest_marks = min($marks);
$average_marks = round(array_sum($marks) / count($marks), 2);

echo json_encode([
    'high_count' => $high_count,
    'average_count' => $average_count,
    'poor_count' => $poor_count,
    'highest_marks' => $highest_marks,
    'average_marks' => $average_marks,
    'lowest_marks' => $lowest_marks
]);

$conn->close();
?>
