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
    $mail->Password = 'wwzj rkoh odao lpvs'; // Use Gmail App Password, not your Gmail password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Sender & recipient
    $mail->setFrom('acadexa.official@gmail.com', 'Acadexa');
    $mail->addAddress($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to Acadexa - Your Account Details';
    
    $htmlBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Welcome to Acadexa</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        <!-- Header -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
                                <h1 style="color: #ffffff; margin: 0; font-size: 32px; font-weight: bold;">Acadexa</h1>
                                <p style="color: #bbdefb; margin: 10px 0 0 0; font-size: 16px;">Welcome to Your Learning Platform</p>
                            </td>
                        </tr>
                        <!-- Content -->
                        <tr>
                            <td style="padding: 40px 30px;">
                                <h2 style="color: #0d47a1; margin: 0 0 20px 0; font-size: 24px;">Hello ' . htmlspecialchars($name) . ',</h2>
                                <p style="color: #333333; line-height: 1.6; margin: 0 0 25px 0; font-size: 16px;">Welcome to Acadexa! Your account has been successfully created. Below are your login credentials:</p>
                                
                                <!-- Credentials Box -->
                                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 2px solid #e3f2fd; border-radius: 8px; margin: 25px 0;">
                                    <tr>
                                        <td style="padding: 25px;">
                                            <h3 style="color: #0d47a1; margin: 0 0 15px 0; font-size: 18px;">Your Login Credentials</h3>
                                            <table width="100%" cellpadding="8" cellspacing="0">
                                                <tr>
                                                    <td style="color: #666666; font-weight: bold; width: 140px;">Email:</td>
                                                    <td style="color: #333333; font-family: monospace; background-color: #ffffff; padding: 8px; border-radius: 4px;">' . htmlspecialchars($email) . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #666666; font-weight: bold;">Username:</td>
                                                    <td style="color: #333333; font-family: monospace; background-color: #ffffff; padding: 8px; border-radius: 4px;">' . htmlspecialchars($username) . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #666666; font-weight: bold;">Institute Code:</td>
                                                    <td style="color: #333333; font-family: monospace; background-color: #ffffff; padding: 8px; border-radius: 4px;">' . htmlspecialchars($institute_code) . '</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                
                                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 15px; margin: 25px 0;">
                                    <p style="color: #856404; margin: 0; font-size: 14px;"><strong>Important:</strong> You will need to set your password when you first log in to the platform.</p>
                                </div>
                                
                                <p style="color: #333333; line-height: 1.6; margin: 25px 0; font-size: 16px;">To get started, please visit our platform and use either your email or username along with your institute code to access your account.</p>
                                
                                <div style="text-align: center; margin: 30px 0;">
                                    <a href="#" style="background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%); color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; display: inline-block;">Access Platform</a>
                                </div>
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
    $mail->AltBody = "Welcome to Acadexa\n\nDear $name,\n\nYour login credentials:\nEmail: $email\nUsername: $username\nInstitute Code: $institute_code\n\nPlease visit our platform and set your password on first login.\n\nBest regards,\nAcadexa Team";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Welcome email sent successfully!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to send email: ' . $mail->ErrorInfo]);
} 