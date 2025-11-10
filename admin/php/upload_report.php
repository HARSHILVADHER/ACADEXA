<?php
// filepath: e:\XAMPP\htdocs\Acadexa\upload_report.php
header('Content-Type: application/json');
$targetDir = "uploads/";
if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
$filename = uniqid("report_") . ".pdf";
$targetFile = $targetDir . $filename;
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
    // Adjust the URL as per your server setup
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/$targetFile";
    echo json_encode(['url' => $url]);
} else {
    echo json_encode(['url' => '']);
}