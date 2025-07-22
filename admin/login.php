<?php
session_start();
require_once 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$institute_code = trim($_POST['institute_code'] ?? '');

if (empty($username) || empty($password) || empty($institute_code)) {
    echo "Username, password, and institute code are required!";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND institute_code = ?");
$stmt->bind_param("ss", $username, $institute_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $db_password = $user['password'];

    // Check if password is hashed
    $isHashed = password_get_info($db_password)['algo'] !== 0;

    if (
        ($isHashed && password_verify($password, $db_password)) ||
        (!$isHashed && $password === $db_password)
    ) {
        // Upgrade to hashed password if stored as plain text
        if (!$isHashed) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $hashed, $user['id']);
            $update->execute();
            $update->close();
        }

        // ✅ Set session for multi-user support
        $_SESSION['user_id'] = $user['id']; // renamed from 'id' to 'user_id'
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        // Assuming you have $row as the user record after verifying credentials
        if (isset($user['blocked']) && $user['blocked'] == 1) {
            echo '<div style="color:red;text-align:center;margin-top:20px;">Your account is blocked. Please contact admin.</div>';
            exit;
        }

        echo "success";
    } else {
        echo "Invalid password!";
    }
} else {
    echo "User not found or institute code incorrect!";
}

$stmt->close();
$conn->close();
?>
