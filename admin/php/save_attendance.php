<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$username = "root";
$password = "";
$database = "acadexa";

// Connect to database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "DB connection failed"]);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["success" => false, "error" => "User not authenticated"]);
    exit;
}

// Add user_id column if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM attendance LIKE 'user_id'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE attendance ADD COLUMN user_id INT DEFAULT NULL");
}

// Read raw input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['classCode']) || !isset($data['date']) || !isset($data['attendance'])) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

$classCode = $conn->real_escape_string($data['classCode']);
$date = $conn->real_escape_string($data['date']);
$attendanceRecords = $data['attendance'];

// Insert each attendance record
foreach ($attendanceRecords as $record) {
    $student_id = (int)$record['student_id'];
    $student_name = $conn->real_escape_string($record['student_name']);
    $status = $conn->real_escape_string($record['status']);

    // Check if already exists for same student/date/class/user
    $check = $conn->query("SELECT * FROM attendance WHERE student_id = $student_id AND date = '$date' AND class_code = '$classCode' AND user_id = $user_id");
    if ($check && $check->num_rows > 0) {
        // Update existing
        $conn->query("UPDATE attendance SET status = '$status', student_name = '$student_name' WHERE student_id = $student_id AND date = '$date' AND class_code = '$classCode' AND user_id = $user_id");
    } else {
        // Insert new
        $conn->query("INSERT INTO attendance (student_id, student_name, class_code, date, status, user_id) VALUES ($student_id, '$student_name', '$classCode', '$date', '$status', $user_id)");
    }
}

echo json_encode(["success" => true]);
$conn->close();
?>
