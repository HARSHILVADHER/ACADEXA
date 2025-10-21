<?php
ob_start();
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

session_start();
require_once 'config.php';
require_once '../../super admin/PHPMailer/Exception.php';
require_once '../../super admin/PHPMailer/PHPMailer.php';
require_once '../../super admin/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (ob_get_length()) ob_clean();

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated.');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        throw new Exception('Invalid JSON input.');
    }

    $email = trim($input['email'] ?? '');
    $name = trim($input['name'] ?? '');
    $type = trim($input['type'] ?? 'User');

    if (empty($email) || empty($name)) {
        throw new Exception('Missing required fields.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address.');
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Get user details
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('User not found.');
    }
    
    $user = $result->fetch_assoc();
    $username = $user['username'];
    $userEmail = $user['email'];
    
    // Verify permissions
    $canSend = false;
    if ($type === 'student') {
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ? AND user_id = ?");
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $canSend = $stmt->get_result()->num_rows > 0;
    } elseif ($type === 'faculty') {
        $stmt = $conn->prepare("SELECT id FROM faculty WHERE email = ? AND user_id = ?");
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $canSend = $stmt->get_result()->num_rows > 0;
    }
    
    if (!$canSend) {
        throw new Exception('Permission denied.');
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'acadexa.official@gmail.com';
    $mail->Password = 'wwzj rkoh odao lpvs';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('acadexa.official@gmail.com', $username);
    if (!empty($userEmail)) {
        $mail->addReplyTo($userEmail, $username);
    }
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "🎉 Happy Birthday, " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "!";
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    $mail->Body = "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            @media only screen and (max-width: 600px) {
                .container { width: 100% !important; max-width: 100% !important; }
                .header { padding: 30px 15px !important; }
                .content { padding: 25px 15px !important; }
                .footer { padding: 25px 15px !important; }
                .title { font-size: 2.2em !important; }
                .name { font-size: 1.8em !important; }
                .text { font-size: 1.1em !important; }
                .message-box { padding: 20px 15px !important; margin: 20px 0 !important; }
                .icons { padding: 15px 20px !important; }
                .icon { font-size: 28px !important; margin: 0 6px !important; }
                .cta { padding: 18px 25px !important; font-size: 1em !important; }
            }
        </style>
    </head>
    <body style='margin: 0; padding: 0; font-family: Georgia, serif; background: linear-gradient(135deg, #2c3e50, #34495e);'>
        <div style='width: 100%; padding: 15px; box-sizing: border-box;'>
            <div class='container' style='max-width: 600px; width: 100%; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);'>
                
                <div class='header' style='background: linear-gradient(135deg, #3498db, #2980b9); padding: 40px 25px; text-align: center;'>
                    <div style='font-size: 48px; margin-bottom: 15px;'>🎉</div>
                    <h1 class='title' style='color: #ffffff; font-size: 2.8em; font-weight: 400; margin: 0; letter-spacing: 1px;'>Happy Birthday</h1>
                    <div style='width: 60px; height: 3px; background: #f39c12; margin: 15px auto; border-radius: 2px;'></div>
                </div>
                
                <div class='content' style='padding: 35px 25px;'>
                    <div style='text-align: center; margin-bottom: 30px;'>
                        <h2 class='name' style='font-size: 2.2em; color: #2c3e50; margin: 0 0 10px 0; font-weight: 300; letter-spacing: 0.5px;'>Dear {$safeName},</h2>
                        <div style='width: 80px; height: 2px; background: linear-gradient(135deg, #e74c3c, #f39c12); margin: 0 auto;'></div>
                    </div>
                    
                    <div class='message-box' style='background: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #3498db;'>
                        <p class='text' style='color: #34495e; font-size: 1.2em; line-height: 1.7; margin: 0 0 18px 0; font-weight: 400;'>On this wonderful day, we celebrate not just another year of your life, but the incredible journey you have embarked upon. Your dedication, passion, and commitment to excellence have been truly inspiring to witness.</p>
                        
                        <p class='text' style='color: #34495e; font-size: 1.2em; line-height: 1.7; margin: 0 0 18px 0; font-weight: 400;'>As you step into this new chapter, may it be filled with remarkable achievements, meaningful connections, and moments of pure joy. Your potential knows no bounds, and we are excited to see all the amazing things you will accomplish in the year ahead.</p>
                        
                        <p class='text' style='color: #34495e; font-size: 1.2em; line-height: 1.7; margin: 0; font-weight: 400;'>May this birthday mark the beginning of your most successful and fulfilling year yet. Here is to celebrating you today and always!</p>
                    </div>
                    
                    <div style='text-align: center; margin: 25px 0;'>
                        <div class='icons' style='display: inline-block; background: #f8f9fa; padding: 18px 25px; border-radius: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);'>
                            <span class='icon' style='font-size: 32px; margin: 0 8px; display: inline-block;'>🎂</span>
                            <span class='icon' style='font-size: 32px; margin: 0 8px; display: inline-block;'>🎁</span>
                            <span class='icon' style='font-size: 32px; margin: 0 8px; display: inline-block;'>🎈</span>
                            <span class='icon' style='font-size: 32px; margin: 0 8px; display: inline-block;'>✨</span>
                        </div>
                    </div>
                    
                    <div style='text-align: center; margin: 25px 0;'>
                        <div class='cta' style='background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 20px 35px; border-radius: 40px; display: inline-block; box-shadow: 0 6px 20px rgba(39,174,96,0.3);'>
                            <p style='margin: 0; font-size: 1.1em; font-weight: 500;'>Wishing you endless happiness and success! 🎆</p>
                        </div>
                    </div>
                </div>
                
                <div class='footer' style='background: linear-gradient(135deg, #ecf0f1, #bdc3c7); padding: 25px; text-align: center;'>
                    <div style='margin-bottom: 12px;'>
                        <div style='background: linear-gradient(135deg, #2c3e50, #34495e); color: white; padding: 12px 22px; border-radius: 25px; display: inline-block; font-weight: 500; font-size: 0.95em;'>{$username}</div>
                    </div>
                    <p style='color: #7f8c8d; font-size: 0.9em; margin: 0; line-height: 1.5; font-style: italic;'>Success is not just about what you accomplish in your life, it is about what you inspire others to do.</p>
                </div>
            </div>
        </div>
    </body>
    </html>";

    $mail->AltBody = "Happy Birthday, {$name}! Wishing you a wonderful day filled with joy and success. — {$username}";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Birthday greeting sent successfully!']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;