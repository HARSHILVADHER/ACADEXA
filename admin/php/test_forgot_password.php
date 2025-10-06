<?php
// Test file to verify forgot password functionality
header('Content-Type: application/json');

echo "Testing forgot password setup...\n\n";

// Test 1: Database connection
echo "1. Testing database connection...\n";
try {
    require_once 'config.php';
    echo "✓ Database connection successful\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit();
}

// Test 2: PHPMailer files
echo "2. Testing PHPMailer files...\n";
$phpmailer_files = [
    '../../super admin/PHPMailer/PHPMailer.php',
    '../../super admin/PHPMailer/SMTP.php',
    '../../super admin/PHPMailer/Exception.php'
];

foreach ($phpmailer_files as $file) {
    if (file_exists($file)) {
        echo "✓ Found: $file\n";
    } else {
        echo "✗ Missing: $file\n";
    }
}

// Test 3: PHPMailer class loading
echo "\n3. Testing PHPMailer class loading...\n";
try {
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require_once '../../super admin/PHPMailer/PHPMailer.php';
    require_once '../../super admin/PHPMailer/SMTP.php';
    require_once '../../super admin/PHPMailer/Exception.php';
    
    $mail = new PHPMailer(true);
    echo "✓ PHPMailer class loaded successfully\n";
} catch (Exception $e) {
    echo "✗ PHPMailer class loading failed: " . $e->getMessage() . "\n";
}

// Test 4: Users table structure
echo "\n4. Testing users table...\n";
try {
    $result = $conn->query("DESCRIBE users");
    if ($result) {
        echo "✓ Users table exists\n";
        echo "Columns: ";
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " ";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "✗ Users table error: " . $e->getMessage() . "\n";
}

echo "\nTest completed!\n";
$conn->close();
?>