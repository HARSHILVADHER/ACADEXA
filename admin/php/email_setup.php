<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_password = trim($_POST['email_password'] ?? '');
    
    if (!empty($email_password)) {
        // Update user's email password (encrypted)
        $encrypted_password = base64_encode($email_password);
        $stmt = $conn->prepare("UPDATE users SET email_password = ? WHERE id = ?");
        $stmt->bind_param('si', $encrypted_password, $user_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Email configuration saved successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Failed to save email configuration.</div>';
        }
    }
}

// Get current user email
$stmt = $conn->prepare("SELECT email, email_password FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Configuration - Acadexa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Email Configuration for Birthday Wishes</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <div class="alert alert-info">
                            <strong>Your Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
                            <small>Birthday wishes will be sent from this email address.</small>
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Gmail App Password</label>
                                <input type="password" name="email_password" class="form-control" 
                                       placeholder="Enter your Gmail App Password" 
                                       value="<?php echo !empty($user['email_password']) ? '••••••••••••' : ''; ?>">
                                <div class="form-text">
                                    <strong>How to get Gmail App Password:</strong><br>
                                    1. Go to Google Account settings<br>
                                    2. Enable 2-Step Verification<br>
                                    3. Generate App Password for "Mail"<br>
                                    4. Use that 16-character password here
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Save Configuration</button>
                            <a href="../birthdays.html" class="btn btn-secondary">Back to Birthdays</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>