<?php
session_start();
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');
ob_start();

require_once 'config.php';
require_once '../../super admin/PHPMailer/Exception.php';
require_once '../../super admin/PHPMailer/PHPMailer.php';
require_once '../../super admin/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    // Check if user is authenticated
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated.');
    }
    
    // Read and decode JSON input
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);

    if (!is_array($input)) {
        throw new Exception('Invalid JSON input.');
    }

    // Basic validation
    $email = trim($input['email'] ?? '');
    $name  = trim($input['name'] ?? '');
    $type  = trim($input['type'] ?? 'User');

    if (empty($email) || empty($name)) {
        throw new Exception('Missing required fields: email and name.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address.');
    }
    
    // Verify that the user can send greeting to this person
    $user_id = $_SESSION['user_id'];
    $canSend = false;
    
    if ($type === 'student') {
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ? AND user_id = ?");
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $canSend = $result->num_rows > 0;
    } elseif ($type === 'faculty') {
        $stmt = $conn->prepare("SELECT id FROM faculty WHERE email = ? AND user_id = ?");
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $canSend = $result->num_rows > 0;
    }
    
    if (!$canSend) {
        throw new Exception('You can only send greetings to your own students and faculty.');
    }

    // Clean any buffered output
    if (ob_get_length() > 0) {
        ob_clean();
    }

    // Create and configure the mailer
    $mail = new PHPMailer(true);

    // SMTP Configuration
    $smtpHost = 'smtp.gmail.com';
    $smtpUser = 'acadexa.official@gmail.com';
    $smtpPass = 'wwzj rkoh odao lpvs';
    $smtpPort = 587;
    $smtpSecure = PHPMailer::ENCRYPTION_STARTTLS;

    // Sender details
    $fromEmail = 'acadexa.official@gmail.com';
    $fromName  = 'Acadexa Team';

    // Set up SMTP
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port       = $smtpPort;
    $mail->CharSet    = 'UTF-8';

    // Recipients
    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($email, $name);

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = "🎉 Happy Birthday, " . htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "!";
    $safeName = htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $mail->Body = "
        <div style='font-family: Arial, Helvetica, sans-serif; background:#f7fbff; padding:20px; border-radius:10px; text-align:center;'>
            <h2 style='color:#4361ee; margin-bottom:10px;'>🎂 Happy Birthday, {$safeName}!</h2>
            <p style='color:#333; font-size:15px; margin-bottom:10px;'>Wishing you a wonderful day filled with joy and success.</p>
            <p style='color:#666; font-size:13px; margin-top:12px;'>From all of us at <strong>Acadexa</strong></p>
        </div>
    ";

    $mail->AltBody = "Happy Birthday, {$name}! Wishing you a wonderful day. — Acadexa";

    // Send
    $mail->send();

    // Success response
    echo json_encode(['success' => true, 'message' => 'Greeting sent successfully.']);

} catch (Exception $e) {
    // Ensure the output buffer is cleared so no HTML leaks
    if (ob_get_length() > 0) {
        ob_clean();
    }

    // Return JSON error only
    $msg = $e->getMessage();

    // Avoid exposing server internals in production — you may want to sanitize $msg.
    echo json_encode(['success' => false, 'message' => $msg]);
}

// Ensure script ends without any extra output
exit;
