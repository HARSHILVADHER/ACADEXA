<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error']);
    exit();
}

$user_id = $_SESSION['user_id'];
$file = $_FILES['csvFile']['tmp_name'];
$imported_count = 0;
$errors = [];

try {
    if (($handle = fopen($file, "r")) !== FALSE) {
        // Skip header row
        $header = fgetcsv($handle, 1000, ",");
        
        // Expected columns: Name,DOB,Medium,Roll No,Std,Parent Contact,Student Contact,Email,Class Code,Group
        $expected_columns = ['Name', 'DOB', 'Medium', 'Roll No', 'Std', 'Parent Contact', 'Student Contact', 'Email', 'Class Code', 'Group'];
        
        // Validate header
        if (count(array_intersect($header, $expected_columns)) < 8) { // At least 8 required columns
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSV format. Please use the sample CSV format.']);
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO students (name, dob, medium, roll_no, std, parent_contact, student_contact, email, class_code, group_name, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $row_number = 1;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row_number++;
            
            // Skip empty rows
            if (empty(array_filter($data))) continue;
            
            // Map CSV columns to variables
            $name = trim($data[0] ?? '');
            $dob = trim($data[1] ?? '');
            $medium = trim($data[2] ?? '');
            $roll_no = trim($data[3] ?? '');
            $std = trim($data[4] ?? '');
            $parent_contact = trim($data[5] ?? '');
            $student_contact = trim($data[6] ?? '');
            $email = trim($data[7] ?? '');
            $class_code = trim($data[8] ?? '');
            $group_name = trim($data[9] ?? '') ?: null;
            
            // Validate required fields
            if (empty($name) || empty($dob) || empty($medium) || empty($roll_no) || empty($std) || empty($parent_contact) || empty($email) || empty($class_code)) {
                $errors[] = "Row $row_number: Missing required fields";
                continue;
            }
            
            // Validate date format
            $date_obj = DateTime::createFromFormat('Y-m-d', $dob);
            if (!$date_obj || $date_obj->format('Y-m-d') !== $dob) {
                $errors[] = "Row $row_number: Invalid date format for DOB (use YYYY-MM-DD)";
                continue;
            }
            
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row $row_number: Invalid email format";
                continue;
            }
            
            // Check if group is required for std 11 or 12
            if (($std == '11' || $std == '12') && empty($group_name)) {
                $errors[] = "Row $row_number: Group is required for standard 11 and 12";
                continue;
            }
            
            try {
                $stmt->bind_param("ssssssssssi", $name, $dob, $medium, $roll_no, $std, $parent_contact, $student_contact, $email, $class_code, $group_name, $user_id);
                
                if ($stmt->execute()) {
                    $imported_count++;
                } else {
                    $errors[] = "Row $row_number: Database error - " . $stmt->error;
                }
            } catch (Exception $e) {
                $errors[] = "Row $row_number: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not read CSV file']);
        exit();
    }
    
    if ($imported_count > 0) {
        $message = "Successfully imported $imported_count students";
        if (!empty($errors)) {
            $message .= ". " . count($errors) . " rows had errors.";
        }
        echo json_encode(['status' => 'success', 'message' => $message, 'count' => $imported_count, 'errors' => $errors]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No students were imported. Please check your CSV format.', 'errors' => $errors]);
    }
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error processing CSV: ' . $e->getMessage()]);
}

$conn->close();
?>