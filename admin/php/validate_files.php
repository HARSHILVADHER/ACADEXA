<?php
// Simple validation script to check for PHP syntax errors

echo "Validating PHP files...\n\n";

$files_to_check = [
    'config.php',
    'send_reset_code.php',
    'reset_password.php'
];

foreach ($files_to_check as $file) {
    echo "Checking $file: ";
    
    if (!file_exists($file)) {
        echo "✗ File not found\n";
        continue;
    }
    
    $output = [];
    $return_var = 0;
    exec("php -l $file 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        echo "✓ Syntax OK\n";
    } else {
        echo "✗ Syntax Error:\n";
        foreach ($output as $line) {
            echo "  $line\n";
        }
    }
}

echo "\nValidation completed!\n";
?>