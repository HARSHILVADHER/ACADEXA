<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['exam_name'])) {
    $exam_name = $_POST['exam_name'];
    
    $stmt = $conn->prepare("SELECT e.*, c.name as class_name FROM exam e 
                           INNER JOIN classes c ON e.code = c.code AND c.user_id = e.user_id
                           WHERE e.exam_name LIKE ? AND e.user_id = ? ORDER BY e.exam_date DESC");
    $search = "%$exam_name%";
    $stmt->bind_param("si", $search, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $exams = [];
        while($row = $result->fetch_assoc()) {
            $exam_id = $row['id'];
            $exam_name_db = $row['exam_name'];
            $class_code = $row['code'];
            
            $marks_stmt = $conn->prepare("SELECT COUNT(*) as count FROM marks WHERE exam_name = ? AND class_code = ? AND user_id = ?");
            $marks_stmt->bind_param("ssi", $exam_name_db, $class_code, $user_id);
            $marks_stmt->execute();
            $marks_result = $marks_stmt->get_result();
            $marks_data = $marks_result->fetch_assoc();
            
            $row['has_marks'] = $marks_data['count'] > 0;
            $exams[] = $row;
        }
        echo json_encode($exams);
    } else {
        echo json_encode([]);
    }
} else if(isset($_POST['exam_id'])) {
    $exam_id = $_POST['exam_id'];
    
    $stmt = $conn->prepare("SELECT e.*, c.name as class_name FROM exam e 
                           INNER JOIN classes c ON e.code = c.code AND c.user_id = e.user_id
                           WHERE e.id = ? AND e.user_id = ?");
    $stmt->bind_param("ii", $exam_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'No exam found']);
    }
}

$conn->close();
?>
