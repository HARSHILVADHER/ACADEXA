<?php
// --- Enable error reporting for debugging ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Always return JSON, even on fatal error ---
ob_start(); // Start output buffering to prevent unwanted output
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        ob_end_clean(); // Remove any buffered output
        echo json_encode(['success' => false, 'error' => $error['message']]);
    }
});

header('Content-Type: application/json');

require_once '../../admin/php/config.php'; // Adjust path as needed

// --- Validate input ---
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
    exit;
}

$user_id = intval($_POST['id']);

$conn->begin_transaction();

try {
    // 1. Delete related data from each class owned by the user
    $classRes = $conn->query("SELECT id, code FROM classes WHERE user_id = $user_id");

if (!$classRes) {
    throw new Exception("Failed to fetch classes: " . $conn->error);
}

while ($row = $classRes->fetch_assoc()) {
    $class_id = $row['id'];
    $class_code = $conn->real_escape_string($row['code']);
    $conn->query("DELETE FROM exam WHERE class_id = $class_id");
    $conn->query("DELETE FROM attendance WHERE class_code = '$class_code'");
    $conn->query("DELETE FROM students WHERE class_code = '$class_code'");
}


    // 2. Delete all classes owned by this user
    $conn->query("DELETE FROM classes WHERE user_id = $user_id");

    // 3. Delete all tasks for this user
    $conn->query("DELETE FROM task WHERE user_id = $user_id");

    // 4. Delete attendance records where this user is a student
    $conn->query("DELETE FROM attendance WHERE student_id = $user_id");

    // 5. Delete from other user-related tables
    $conn->query("DELETE FROM inquiry WHERE user_id = $user_id");
    $conn->query("DELETE FROM profile WHERE user_id = $user_id");
    $conn->query("DELETE FROM students WHERE user_id = $user_id");

    // 6. Finally, delete the user
    $conn->query("DELETE FROM users WHERE id = $user_id");

    $conn->commit();
    ob_end_clean(); // Clean buffer before sending JSON
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    ob_end_clean(); // Clean buffer before sending JSON
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
