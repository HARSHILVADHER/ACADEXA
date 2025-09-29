<?php
header('Content-Type: application/json');

// Enable error reporting (optional for debugging)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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

    // Check if already exists for same student/date/class
    $check = $conn->query("SELECT * FROM attendance WHERE student_id = $student_id AND date = '$date' AND class_code = '$classCode'");
    if ($check && $check->num_rows > 0) {
        // Update existing
        $conn->query("UPDATE attendance SET status = '$status', student_name = '$student_name' WHERE student_id = $student_id AND date = '$date' AND class_code = '$classCode'");
    } else {
        // Insert new
        $conn->query("INSERT INTO attendance (student_id, student_name, class_code, date, status) VALUES ($student_id, '$student_name', '$classCode', '$date', '$status')");
    }
}


echo json_encode(["success" => true]);
$conn->close();
?>
