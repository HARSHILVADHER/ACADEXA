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
                           WHERE e.exam_name LIKE ? AND e.user_id = ?");
    $search = "%$exam_name%";
    $stmt->bind_param("si", $search, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'No exam found']);
    }
} elseif(isset($_POST['exam_id'])) {
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
