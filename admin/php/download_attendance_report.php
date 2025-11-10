<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$class_code = $_GET['class_code'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$format = $_GET['format'] ?? 'csv';

if (empty($class_code) || empty($start_date) || empty($end_date)) {
    die('Invalid parameters');
}

// Fetch attendance data
$stmt = $conn->prepare("SELECT 
    a.student_id,
    a.student_name,
    a.date,
    a.status,
    c.name as class_name
FROM attendance a
INNER JOIN classes c ON a.class_code = c.code
WHERE a.class_code = ? 
    AND c.user_id = ? 
    AND a.date BETWEEN ? AND ?
ORDER BY a.student_name, a.date");

$stmt->bind_param('siss', $class_code, $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_report_' . $class_code . '_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Student ID', 'Student Name', 'Date', 'Status', 'Class']);
    
    foreach ($data as $row) {
        fputcsv($output, [
            $row['student_id'],
            $row['student_name'],
            $row['date'],
            ucfirst($row['status']),
            $row['class_name']
        ]);
    }
    
    fclose($output);
} else {
    // PDF generation using HTML
    $class_name = !empty($data) ? $data[0]['class_name'] : '';
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Attendance Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { text-align: center; color: #4361ee; }
            .info { margin: 20px 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #4361ee; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            @media print {
                button { display: none; }
            }
        </style>
    </head>
    <body>
        <h1>Attendance Report</h1>
        <div class="info">
            <p><strong>Class:</strong> <?php echo htmlspecialchars($class_name); ?></p>
            <p><strong>Period:</strong> <?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script>
            window.onload = function() {
                window.print();
            };
        </script>
    </body>
    </html>
    <?php
}

$stmt->close();
$conn->close();
exit;
?>
