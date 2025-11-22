<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample_add_student.csv"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

$csvContent = "Name,DOB,Medium,Roll No,Std,Parent Contact,Student Contact,Email,Group,Date of Joining\n";
$csvContent .= "John Doe,2005-01-15,English,001,10,9876543210,9876543211,john@example.com,,2024-01-15\n";
$csvContent .= "Jane Smith,2003-03-20,English,002,12,9876543212,9876543213,jane@example.com,A Group,2024-01-15\n";
$csvContent .= "Mike Johnson,2004-07-10,Hindi,003,11,9876543214,9876543215,mike@example.com,Commerce,2024-01-15\n";

echo $csvContent;
exit;
?>