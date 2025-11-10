<?php
require_once 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    echo "All fields are required!";
    exit();
}

// Check if username already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Username already exists!";
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Generate random institute code
$institute_code = strtoupper(substr(md5(uniqid()), 0, 4));

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (username, email, password, institute_code) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $password, $institute_code);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Registration failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>