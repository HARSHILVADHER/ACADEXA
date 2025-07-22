<?php
// Always return JSON
header('Content-Type: application/json');

// Validate input
$email = trim($_POST['email'] ?? '');
$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$institute_code = trim($_POST['institute_code'] ?? '');

if (!$email || !$name || !$username || !$institute_code) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

require_once '../../admin/config.php';

// Fetch password from DB
$stmt = $conn->prepare('SELECT password FROM users WHERE username=? AND email=? LIMIT 1');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->bind_result($password);
if ($stmt->fetch()) {
    // Password found
} else {
    echo json_encode(['success' => false, 'error' => 'User not found or password missing.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();
$conn->close();

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'acadexa.official@gmail.com'; // Your Gmail address
    $mail->Password = 'khmm crrq upgc cuge'; // Use Gmail App Password, not your Gmail password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Sender & recipient
    $mail->setFrom('acadexa.official@gmail.com', 'Acadexa');
    $mail->addAddress($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to Acadexa';
    $mail->Body = nl2br("Welcome to Acadexa\n\nDear $name,\n\nYour username: $username<br>Password: $password<br>Institute code: $institute_code<br><br>This information is provided for your access to the Acadexa platform.");
    $mail->AltBody = "Welcome to Acadexa\n\nDear $name,\n\nYour username: $username\nPassword: $password\nInstitute code: $institute_code\n\nThis information is provided for your access to the Acadexa platform.";

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
} 