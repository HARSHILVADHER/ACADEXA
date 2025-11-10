<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    require_once 'config.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Email is required!']);
    exit();
}

// Check if email exists in users table
$stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Email not found!']);
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Generate 6-digit verification code
$code = sprintf('%06d', mt_rand(0, 999999));

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../super admin/PHPMailer/PHPMailer.php';
require_once '../../super admin/PHPMailer/SMTP.php';
require_once '../../super admin/PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'acadexa.official@gmail.com';
    $mail->Password = 'wwzj rkoh odao lpvs';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Sender & recipient
    $mail->setFrom('acadexa.official@gmail.com', 'Acadexa');
    $mail->addAddress($email, $user['username']);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Code - Acadexa';
    
    $htmlBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Password Reset - Acadexa</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        <!-- Header -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #4473d8 0%, #4473d8 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
                                <h1 style="color: #ffffff; margin: 0; font-size: 32px; font-weight: bold;">Acadexa</h1>
                                <p style="color: #bbdefb; margin: 10px 0 0 0; font-size: 16px;">Password Reset Request</p>
                            </td>
                        </tr>
                        <!-- Content -->
                        <tr>
                            <td style="padding: 40px 30px;">
                                <h2 style="color: #4473d8; margin: 0 0 20px 0; font-size: 24px;">Hello ' . htmlspecialchars($user['username']) . ',</h2>
                                <p style="color: #333333; line-height: 1.6; margin: 0 0 25px 0; font-size: 16px;">We received a request to reset your password. Use the verification code below to proceed:</p>
                                
                                <!-- Code Box -->
                                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 2px solid #4473d8; border-radius: 8px; margin: 25px 0;">
                                    <tr>
                                        <td style="padding: 25px; text-align: center;">
                                            <h3 style="color: #4473d8; margin: 0 0 15px 0; font-size: 18px;">Verification Code</h3>
                                            <div style="font-size: 36px; font-weight: bold; color: #4473d8; font-family: monospace; letter-spacing: 8px;">' . $code . '</div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 15px; margin: 25px 0;">
                                    <p style="color: #856404; margin: 0; font-size: 14px;"><strong>Important:</strong> This code will expire in 10 minutes for security reasons.</p>
                                </div>
                                
                                <p style="color: #333333; line-height: 1.6; margin: 25px 0; font-size: 16px;">If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td style="background-color: #f8fafc; padding: 20px 30px; text-align: center; border-radius: 0 0 10px 10px; border-top: 1px solid #e3f2fd;">
                                <p style="color: #666666; margin: 0; font-size: 14px;">Best regards,<br><strong>The Acadexa Team</strong></p>
                                <p style="color: #999999; margin: 15px 0 0 0; font-size: 12px;">This is an automated message. Please do not reply to this email.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';
    
    $mail->Body = $htmlBody;
    $mail->AltBody = "Password Reset Code: $code\n\nThis code will expire in 10 minutes.\n\nIf you did not request this, please ignore this email.\n\nBest regards,\nAcadexa Team";

    $mail->send();
    echo json_encode(['success' => true, 'code' => $code, 'message' => 'Verification code sent successfully!']);
} catch (Exception $e) {
    error_log('PHPMailer Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to send email. Please try again later.']);
}

$conn->close();
?>