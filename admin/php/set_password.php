<?php
session_start();
require_once 'config.php';

$login_id = trim($_POST['login_id'] ?? '');
$password = $_POST['password'] ?? '';
$institute_code = trim($_POST['institute_code'] ?? '');

if (empty($login_id) || empty($password) || empty($institute_code)) {
    echo "All fields are required!";
    exit();
}

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE (email = ? OR username = ?) AND institute_code = ?");
$stmt->bind_param("ssss", $hashed_password, $login_id, $login_id, $institute_code);

if ($stmt->execute()) {
    // Get user data for session
    $stmt2 = $conn->prepare("SELECT id, username, email FROM users WHERE (email = ? OR username = ?) AND institute_code = ?");
    $stmt2->bind_param("sss", $login_id, $login_id, $institute_code);
    $stmt2->execute();
    $result = $stmt2->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        echo "success";
    } else {
        echo "Error setting up session!";
    }
    $stmt2->close();
} else {
    echo "Failed to set password!";
}

$stmt->close();
$conn->close();
?>