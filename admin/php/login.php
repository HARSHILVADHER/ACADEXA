<?php
session_start();
require_once 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_id = trim($_POST['login_id'] ?? '');
$password = $_POST['password'] ?? '';
$institute_code = trim($_POST['institute_code'] ?? '');

if (empty($login_id) || empty($password) || empty($institute_code)) {
    echo "Email/Username, password, and institute code are required!";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND institute_code = ?");
$stmt->bind_param("sss", $login_id, $login_id, $institute_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $db_password = $user['password'];

    // Verify hashed password
    if (password_verify($password, $db_password)) {

        // ✅ Set session for multi-user support
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

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
