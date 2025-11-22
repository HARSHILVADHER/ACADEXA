<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<p style='text-align:center;color:red;'>User not authenticated!</p>";
    exit();
}

// Get username for greeting
$username = 'User';
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $username = $row['username'];
}
$stmt->close();

// Generate time-based greeting
$hour = date('H');
if ($hour < 12) {
    $greeting = "Good morning";
} elseif ($hour < 17) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

// Inquiries count for this user
$inquiryCount = 0;
$sql = "SELECT COUNT(*) AS total FROM inquiry WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $row = $result->fetch_assoc();
    $inquiryCount = $row['total'];
}
$stmt->close();

// Students count for this user
$studentCount = 0;
$sqlStudent = "SELECT COUNT(*) AS total FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sqlStudent);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultStudent = $stmt->get_result();
if ($resultStudent) {
    $row = $resultStudent->fetch_assoc();
    $studentCount = $row['total'];
}
$stmt->close();

// Classes count for this user
$Classcount = 0;
$sqlClass = "SELECT COUNT(*) AS total FROM classes WHERE user_id = ?";
$stmt = $conn->prepare($sqlClass);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultClass = $stmt->get_result();
if ($resultClass) {
    $row = $resultClass->fetch_assoc();
    $Classcount = $row['total'];
}
$stmt->close();

// Fetch recent inquiries for this user (with filter)
$inquiries = [];
$filter = $_GET['inq_filter'] ?? 'latest';
$where = "WHERE user_id = ?";
if ($filter === 'week') {
    $where .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filter === 'month') {
    $where .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
}
$sqlInq = "SELECT student_name, student_mobile, school_name, std, medium, group_name, reference_by, interest_level, followup_date, followup_time, notes, created_at FROM inquiry $where ORDER BY created_at DESC LIMIT 20";
$stmt = $conn->prepare($sqlInq);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultInq = $stmt->get_result();
if ($resultInq) {
    while ($row = $resultInq->fetch_assoc()) {
        $inquiries[] = $row;
    }
}
$stmt->close();

// Fetch upcoming exams for this user (today or future)
$upcomingExams = [];
$examFilter = $_GET['exam_filter'] ?? 'latest';
$examWhere = "e.user_id = ? AND e.exam_date >= CURDATE()";
if ($examFilter === 'week') {
    $examWhere .= " AND e.exam_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
} elseif ($examFilter === 'month') {
    $examWhere .= " AND e.exam_date <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)";
}
$sql = "SELECT e.exam_name, e.exam_date, e.start_time, e.total_marks, c.name AS class_name
        FROM exam e
        JOIN classes c ON e.code = c.code
        WHERE $examWhere
        ORDER BY e.exam_date ASC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $upcomingExams[] = $row;
}
$stmt->close();

// Task role filter logic
$taskRoleFilter = $_GET['task_role_filter'] ?? '';
$taskRoleWhere = '';
$taskRoleParams = [];
$taskRoleTypes = 'i';

if ($taskRoleFilter && in_array($taskRoleFilter, ['admin', 'trustee', 'tutor'])) {
    $taskRoleWhere = " AND task_for = ?";
    $taskRoleParams[] = $taskRoleFilter;
    $taskRoleTypes .= 's';
}

// Calculate attendance percentage for this user
$attendancePercentage = 0;
// Check if user_id column exists in attendance table
$result = $conn->query("SHOW COLUMNS FROM attendance LIKE 'user_id'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE attendance ADD COLUMN user_id INT DEFAULT NULL");
}

$sql = "SELECT 
    COUNT(*) as total_records,
    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count
    FROM attendance 
    WHERE user_id = ? AND date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $row = $result->fetch_assoc();
    if ($row['total_records'] > 0) {
        $attendancePercentage = round(($row['present_count'] / $row['total_records']) * 100);
    }
}
$stmt->close();

// Fetch tasks for this user
$userTasks = [];
$sql = "SELECT task_text, task_date, task_for FROM task WHERE user_id = ?$taskRoleWhere ORDER BY task_date DESC LIMIT 10";
$stmt = $conn->prepare($sql);
if ($taskRoleWhere) {
    $stmt->bind_param($taskRoleTypes, $user_id, ...$taskRoleParams);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
$userTasks = [];
while ($row = $result->fetch_assoc()) {
    $userTasks[] = $row;
}
$stmt->close();

// Fetch upcoming fees installments (excluding paid ones)
$upcomingFees = [];
$feesFilter = $_GET['fees_filter'] ?? 'all';
$feesWhere = "f.user_id = ?";
if ($feesFilter !== 'all') {
    $feesWhere .= " AND f.class_code = ?";
}
$sql = "SELECT f.student_id, f.student_name, f.class_code, f.installments 
        FROM fees_structure f 
        WHERE $feesWhere ORDER BY f.student_name LIMIT 50";
$stmt = $conn->prepare($sql);
if ($feesFilter !== 'all') {
    $stmt->bind_param("is", $user_id, $feesFilter);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $installments = json_decode($row['installments'], true);
    if ($installments) {
        foreach ($installments as $index => $installment) {
            if (strtotime($installment['due_date']) >= strtotime('today')) {
                // Check if this installment is already paid
                $paidCheck = $conn->prepare("SELECT id FROM paid_fees WHERE student_id = ? AND installment_index = ? AND user_id = ? AND is_paid = 1");
                $paidCheck->bind_param("iii", $row['student_id'], $index, $user_id);
                $paidCheck->execute();
                $paidResult = $paidCheck->get_result();
                
                // Only add to upcoming fees if not paid
                if ($paidResult->num_rows == 0) {
                    $upcomingFees[] = [
                        'student_name' => $row['student_name'],
                        'class_code' => $row['class_code'],
                        'installment_no' => $index + 1,
                        'due_date' => $installment['due_date'],
                        'amount' => $installment['amount']
                    ];
                }
                $paidCheck->close();
            }
        }
    }
}
$stmt->close();

// Sort by due date (earliest first)
usort($upcomingFees, function($a, $b) {
    return strtotime($a['due_date']) - strtotime($b['due_date']);
});

// Limit to 10 after sorting
$upcomingFees = array_slice($upcomingFees, 0, 10);

// Create fees_structure table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS fees_structure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    student_roll_no VARCHAR(50),
    class_code VARCHAR(50) NOT NULL,
    decided_fees DECIMAL(10,2) NOT NULL,
    installments TEXT,
    notes TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Get all classes for fees filter
$feesClasses = [];
$sql = "SELECT DISTINCT code FROM classes WHERE user_id = ? ORDER BY code";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $feesClasses[] = $row['code'];
    }
    $stmt->close();
}

// Don't close connection here - needed by header_logo.php
// $conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acadexa Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #e0e7ff;
      --primary-dark: #3a0ca3;
      --secondary: #3f37c9;
      --accent: #f72585;
      --dark: #1a1a1a;
      --light: #f8f9fa;
      --gray: #6c757d;
      --light-gray: #e9ecef;
      --success: #4cc9f0;
      --warning: #f8961e;
      --danger: #ef233c;
      --white: #ffffff;
      --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --card-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background-color: #f5f7ff;
      color: var(--dark);
      line-height: 1.6;
    }

    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 18px 40px;
      background: var(--white);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: -0.5px;
    }
    
    .logo img {
      height: 40px;
      width: auto;
      object-fit: contain;
    }

    nav {
      display: flex;
      gap: 15px;
      align-items: center;
    }
    


    nav a {
      text-decoration: none;
      color: var(--gray);
      font-weight: 600;
      padding: 8px 16px;
      border-radius: 8px;
      transition: var(--transition);
      position: relative;
      font-size: 0.95rem;
    }

    /* Notification Bar */
    .notification-bar {
      background: var(--white);
      padding: 12px 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      display: flex;
      gap: 15px;
      align-items: center;
      position: fixed;
      top: 80px;
      right: 40px;
      z-index: 99;
      border-radius: 10px;
      border: 1px solid rgba(67, 97, 238, 0.1);
      width: auto;
    }

    .notification-bar:empty {
      display: none;
    }

    .notification-item {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 14px;
      border-radius: 8px;
      background: var(--primary-light);
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
      color: inherit;
      white-space: nowrap;
    }

    .notification-item:hover {
      background: var(--primary);
      color: var(--white);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    .notification-item i {
      font-size: 1.2rem;
      color: var(--primary);
    }

    .notification-item:hover i {
      color: var(--white);
    }

    .notification-label {
      font-weight: 600;
      font-size: 0.9rem;
    }

    .notification-count {
      background: var(--primary);
      color: var(--white);
      border-radius: 50%;
      min-width: 22px;
      height: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      font-weight: 700;
      padding: 0 6px;
    }

    .notification-item:hover .notification-count {
      background: var(--white);
      color: var(--primary);
    }

    nav a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 0;
      height: 3px;
      background: var(--primary);
      border-radius: 3px;
      transition: var(--transition);
    }

    nav a:hover {
      color: var(--primary);
    }

    nav a:hover::after {
      width: 70%;
    }

    nav a.active {
      color: var(--primary);
      background: var(--primary-light);
    }

    nav a.active::after {
      width: 70%;
    }

    .container {
      padding: 30px 40px;
      max-width: 1800px;
      margin: 0 auto;
    }

    h1 {
      font-size: 2.2rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 8px;
    }

    p.subtext {
      color: var(--gray);
      font-size: 1rem;
      margin-bottom: 30px;
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: var(--white);
      border-radius: 16px;
      padding: 25px;
      box-shadow: var(--card-shadow);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(67, 97, 238, 0.1);
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: var(--primary);
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--card-shadow-hover);
    }

    .stat-card .icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      color: var(--white);
      font-size: 1.4rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
    }

    .stat-card .value {
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 5px;
    }

    .stat-card .label {
      color: var(--gray);
      font-size: 0.95rem;
    }

    .stat-card .change {
      display: flex;
      align-items: center;
      margin-top: 15px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .change.up {
      color: var(--success);
    }

    .change.down {
      color: var(--danger);
    }

    /* Quick Access Cards */
    .section-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 25px;
      position: relative;
      display: inline-block;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 0;
      width: 50px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--accent));
      border-radius: 2px;
    }

    .quick-access-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 50px;
    }

    .quick-card {
      background: var(--white);
      border-radius: 16px;
      padding: 25px;
      box-shadow: var(--card-shadow);
      transition: var(--transition);
      text-align: center;
      border: 1px solid rgba(67, 97, 238, 0.1);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .quick-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--card-shadow-hover);
    }

    .quick-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--accent));
    }

    .quick-card .icon {
      width: 60px;
      height: 60px;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 16px;
      background: var(--primary-light);
      color: var(--primary);
      font-size: 1.8rem;
      transition: var(--transition);
    }

    .quick-card:hover .icon {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: var(--white);
      transform: rotate(10deg) scale(1.1);
    }

    .quick-card .title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--dark);
    }

    .quick-card .desc {
      color: var(--gray);
      font-size: 0.9rem;
      margin-bottom: 15px;
      flex-grow: 1;
    }

    .quick-card .btn-container {
      margin-top: auto;
    }

    .quick-card .btn {
      display: inline-block;
      padding: 10px 20px;
      background: var(--primary-light);
      color: var(--primary);
      border-radius: 8px;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.9rem;
      transition: var(--transition);
      border: none;
      cursor: pointer;
      width: 100%;
      text-align: center;
    }

    .quick-card:hover .btn {
      background: var(--primary);
      color: var(--white);
    }

    /* Dashboard Flex Layout */
    .dashboard-flex {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 30px;
      margin-bottom: 30px;
    }

    .dashboard-column {
      min-width: 0;
      display: flex;
      flex-direction: column;
    }

    /* Task Card */
    .task-card {
      background: var(--white);
      border-radius: 16px;
      padding: 25px;
      box-shadow: var(--card-shadow);
      margin-bottom: 30px;
      height: 500px;
      display: flex;
      flex-direction: column;
      border: 1px solid rgba(67, 97, 238, 0.1);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .card-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--dark);
      position: relative;
      padding-left: 15px;
    }

    .card-title::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 5px;
      height: 30px;
      background: var(--primary);
      border-radius: 3px;
    }

    .filter-select {
      padding: 8px 15px;
      border-radius: 8px;
      border: 1px solid var(--light-gray);
      background: var(--white);
      color: var(--dark);
      font-weight: 500;
      font-size: 0.9rem;
      outline: none;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .filter-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .task-list {
      list-style: none;
      flex-grow: 1;
      overflow-y: auto;
      max-height: 350px;
      padding-right: 5px;
    }

    .task-list::-webkit-scrollbar {
      width: 6px;
    }

    .task-list::-webkit-scrollbar-track {
      background: var(--light-gray);
      border-radius: 3px;
    }

    .task-list::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 3px;
    }

    .task-list::-webkit-scrollbar-thumb:hover {
      background: var(--primary-dark);
    }

    .task-item {
      padding: 15px 0;
      border-bottom: 1px solid var(--light-gray);
      display: flex;
      align-items: center;
      transition: var(--transition);
    }

    .task-item:last-child {
      border-bottom: none;
    }

    .task-item:hover {
      transform: translateX(5px);
    }

    .task-checkbox {
      margin-right: 15px;
      width: 18px;
      height: 18px;
      accent-color: var(--primary);
      cursor: pointer;
    }

    .task-text {
      flex: 1;
      font-size: 0.95rem;
      color: var(--dark);
    }

    .task-date {
      font-size: 0.85rem;
      color: var(--gray);
      min-width: 100px;
      text-align: right;
    }

    .task-role {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      margin-left: 15px;
    }

    .task-role.admin {
      background: rgba(239, 35, 60, 0.1);
      color: var(--danger);
    }

    .task-role.trustee {
      background: rgba(248, 150, 30, 0.1);
      color: var(--warning);
    }

    .task-role.tutor {
      background: rgba(67, 97, 238, 0.1);
      color: var(--primary);
    }

    /* Exam Card */
    .exam-card {
      background: var(--white);
      border-radius: 16px;
      padding: 25px;
      box-shadow: var(--card-shadow);
      height: 500px;
      display: flex;
      flex-direction: column;
      border: 1px solid rgba(67, 97, 238, 0.1);
    }

    .exam-list {
      list-style: none;
      flex-grow: 1;
      overflow-y: auto;
      max-height: 350px;
      padding-right: 5px;
    }

    .exam-list::-webkit-scrollbar {
      width: 6px;
    }

    .exam-list::-webkit-scrollbar-track {
      background: var(--light-gray);
      border-radius: 3px;
    }

    .exam-list::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 3px;
    }

    .exam-list::-webkit-scrollbar-thumb:hover {
      background: var(--primary-dark);
    }

    .exam-item {
      padding: 15px 0;
      border-bottom: 1px solid var(--light-gray);
      transition: var(--transition);
    }

    .exam-item:last-child {
      border-bottom: none;
    }

    .exam-item:hover {
      transform: translateX(5px);
    }

    .exam-class {
      font-size: 0.9rem;
      color: var(--primary);
      font-weight: 600;
      margin-bottom: 5px;
    }

    .exam-name {
      font-size: 1rem;
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 5px;
    }

    .exam-details {
      display: flex;
      gap: 15px;
      font-size: 0.85rem;
      color: var(--gray);
      flex-wrap: wrap;
    }

    .exam-detail {
      display: flex;
      align-items: center;
    }

    .exam-detail i {
      margin-right: 5px;
      font-size: 0.9rem;
    }

    .view-all-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: var(--primary-light);
      color: var(--primary);
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
      transition: var(--transition);
      align-self: flex-end;
    }

    .view-all-btn:hover {
      background: var(--primary);
      color: var(--white);
    }

    /* Recent Inquiries */
    .inquiries-card {
      background: var(--white);
      border-radius: 16px;
      padding: 25px;
      box-shadow: var(--card-shadow);
      margin-bottom: 30px;
      border: 1px solid rgba(67, 97, 238, 0.1);
      display: flex;
      flex-direction: column;
      height: 500px;
    }

    .inquiries-card > div:nth-child(2) {
      flex-grow: 1;
      overflow-y: auto;
      max-height: 350px;
      padding-right: 5px;
    }

    .inquiries-card > div:nth-child(2)::-webkit-scrollbar {
      width: 6px;
    }

    .inquiries-card > div:nth-child(2)::-webkit-scrollbar-track {
      background: var(--light-gray);
      border-radius: 3px;
    }

    .inquiries-card > div:nth-child(2)::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 3px;
    }

    .inquiries-card > div:nth-child(2)::-webkit-scrollbar-thumb:hover {
      background: var(--primary-dark);
    }

    .inquiry-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .inquiry-table thead th {
      padding: 12px 15px;
      text-align: left;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--gray);
      background: var(--light);
      position: sticky;
      top: 0;
    }

    .inquiry-table tbody tr {
      transition: var(--transition);
    }

    .inquiry-table tbody tr:hover {
      background: rgba(67, 97, 238, 0.03);
    }

    .inquiry-table td {
      padding: 15px;
      border-bottom: 1px solid var(--light-gray);
      font-size: 0.9rem;
      color: var(--dark);
    }

    .inquiry-table tr:last-child td {
      border-bottom: none;
    }

    .student-name {
      font-weight: 600;
      color: var(--primary);
    }

    .followup-date {
      display: flex;
      align-items: center;
    }

    .followup-date i {
      margin-right: 5px;
      color: var(--accent);
    }

    .status-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .status-new {
      background: rgba(76, 201, 240, 0.1);
      color: var(--success);
    }

    .status-followup {
      background: rgba(248, 150, 30, 0.1);
      color: var(--warning);
    }

    .status-converted {
      background: rgba(67, 97, 238, 0.1);
      color: var(--primary);
    }

    /* Empty State Styles */
    .empty-state {
      text-align: center;
      padding: 30px;
      color: var(--gray);
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .empty-state i {
      font-size: 2rem;
      margin-bottom: 15px;
    }

    .empty-state i.success {
      color: var(--success);
    }

    .empty-state i.warning {
      color: var(--warning);
    }

    .empty-state i.primary {
      color: var(--primary);
    }

    /* Responsive Adjustments */
    @media (max-width: 1200px) {
      .dashboard-flex {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      header {
        padding: 15px 20px;
        flex-direction: column;
        gap: 15px;
      }

      nav {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 10px;
      }

      .notification-bar {
        padding: 10px 15px;
        gap: 10px;
        right: 20px;
        top: 70px;
      }

      .notification-item {
        padding: 6px 12px;
        font-size: 0.85rem;
      }

      .container {
        padding: 20px;
      }

      .stats-grid {
        grid-template-columns: 1fr 1fr;
      }

      .quick-access-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 576px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }

      .quick-access-grid {
        grid-template-columns: 1fr;
      }

      .inquiry-table thead {
        display: none;
      }

      .inquiry-table tr {
        display: block;
        margin-bottom: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      }

      .inquiry-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid var(--light-gray);
      }

      .inquiry-table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--gray);
        margin-right: 15px;
      }
    }

    /* Animation Classes */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade {
      animation: fadeIn 0.6s ease-out forwards;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }

    /* Hover Effects */
    .hover-grow {
      transition: transform 0.3s ease;
    }

    .hover-grow:hover {
      transform: scale(1.03);
    }

    .hover-underline {
      position: relative;
    }

    .hover-underline::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: currentColor;
      transition: width 0.3s ease;
    }

    .hover-underline:hover::after {
      width: 100%;
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .greeting {
      color: var(--primary);
      font-size: 1.1rem;
      font-weight: 700;
    }
  </style>
</head>

<body>
  <header>
    <?php include 'header_logo.php'; ?>
    <nav>
      <a href="dashboard.php" class="active">Home</a>
      <a href="../createclass.html">Classes</a>
      <a href="../attendance.html">Attendance</a>
      <a href="gradecard.php">Reports</a>
      <a href="inquiry.php">Inquiries</a>
      <a href="profile.php">Profile</a>
    </nav>
  </header>

  <!-- Notification Bar -->
  <div class="notification-bar">
    <a href="../birthdays.html" class="notification-item" id="birthdayNotif">
      <i class="fas fa-birthday-cake"></i>
      <span class="notification-label">Birthdays</span>
      <span class="notification-count" id="birthdayCount">0</span>
    </a>
    <a href="fees.php" class="notification-item" id="feesNotif">
      <i class="fas fa-money-bill-wave"></i>
      <span class="notification-label">Fees Due</span>
      <span class="notification-count" id="feesCount">0</span>
    </a>
    <a href="total_inquiry.php" class="notification-item" id="inquiryNotif">
      <i class="fas fa-bell"></i>
      <span class="notification-label">Inquiries</span>
      <span class="notification-count" id="inquiryCount">0</span>
    </a>
  </div>

  <div class="container">
    <div class="dashboard-header">
      <div>
        <h1 class="animate-fade">Dashboard</h1>
        <p class="subtext animate-fade delay-1">Welcome back! Here's what's happening with your academy today.</p>
      </div>
      <div class="greeting animate-fade delay-1">
        <?php echo $greeting . ' "' . htmlspecialchars($username) . '"'; ?>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card animate-fade delay-1 hover-grow">
        <a href="../students.html" style="text-decoration: none; color: inherit;">
          <div class="icon">
            <i class="fas fa-user-graduate"></i>
          </div>
          <div class="value"><?php echo $studentCount; ?></div>
          <div class="label">Total Students</div>
          
        </a>
      </div>

      <div class="stat-card animate-fade delay-2 hover-grow">
        <div class="icon">
          <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="value"><?php echo $Classcount; ?></div>
        <div class="label">Active Classes</div>
      </div>

      <div class="stat-card animate-fade delay-3 hover-grow">
        <a href="" style="text-decoration: none; color: inherit;">
          <div class="icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="value"><?php echo $attendancePercentage; ?>%</div>
          <div class="label">Attendance Rate</div>
        </a>
      </div>

      <div class="stat-card animate-fade delay-4 hover-grow">
        <a href="total_inquiry.php" style="text-decoration: none; color: inherit;">
          <div class="icon">
            <i class="fas fa-question-circle"></i>
          </div>
          <div class="value"><?php echo $inquiryCount; ?></div>
          <div class="label">Total Inquiries</div>
        </a>
      </div>
    </div>

    <!-- Quick Access -->
    <h2 class="section-title animate-fade delay-2">Quick Access</h2>
    <div class="quick-access-grid">
      <div class="quick-card animate-fade delay-3 hover-grow">
        <div class="icon">
          <i class="fas fa-tasks"></i>
        </div>
        <div class="title">Create Your Task</div>
        <div class="desc">Create and maintain the daily task</div>
        <div class="btn-container">
          <a href="../calendar.php" class="btn">Go to Tasks</a>
        </div>
      </div>

      <div class="quick-card animate-fade delay-4 hover-grow">
        <div class="icon">
          <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="title">Exam</div>
        <div class="desc">Create Exam for Students</div>
        <div class="btn-container">
          <a href="create_exam.php" class="btn">Create Exam</a>
        </div>
      </div>

      <div class="quick-card animate-fade delay-5 hover-grow">
        <div class="icon">
          <i class="fas fa-book-open"></i>
        </div>
        <div class="title">Study Material</div>
        <div class="desc">Create student performance reports</div>
        <div class="btn-container">
          <a href="../studymaterial.php" class="btn">Upload Materials</a>
        </div>
      </div>

      <div class="quick-card animate-fade delay-3 hover-grow">
        <div class="icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="title">Timetable</div>
        <div class="desc">Manage class schedules and timetables</div>
        <div class="btn-container">
          <a href="../timetable.html" class="btn">Manage Timetable</a>
        </div>
      </div>
    </div>

    <!-- Dashboard Flex Layout -->
    <div class="dashboard-flex">
      <!-- Tasks Column -->
      <div class="dashboard-column">
        <div class="task-card animate-fade delay-4 hover-grow">
          <div class="card-header">
            <h3 class="card-title">Your Tasks</h3>
            <select class="filter-select" id="task_role_filter" onchange="filterTasks(this.value)">
              <option value="">All Tasks</option>
              <option value="admin" <?php if($taskRoleFilter==='admin') echo 'selected'; ?>>Admin</option>
              <option value="trustee" <?php if($taskRoleFilter==='trustee') echo 'selected'; ?>>Trustee</option>
              <option value="tutor" <?php if($taskRoleFilter==='tutor') echo 'selected'; ?>>Tutor</option>
            </select>
          </div>
          
          <div id="tasks-empty-state" class="empty-state" style="display: <?php echo empty($userTasks) ? 'flex' : 'none'; ?>">
            <i class="fas fa-check-circle success"></i>
            <p>No tasks found. Enjoy your free time!</p>
          </div>
          <ul id="tasks-list" class="task-list" style="display: <?php echo empty($userTasks) ? 'none' : 'block'; ?>">
            <?php foreach ($userTasks as $task): ?>
              <li class="task-item">
                <input type="checkbox" class="task-checkbox">
                <span class="task-text"><?php echo htmlspecialchars($task['task_text']); ?></span>
                <span class="task-date">
                  <?php
                    $d = explode('-', $task['task_date']);
                    echo isset($d[2]) ? "{$d[2]}-{$d[1]}-{$d[0]}" : htmlspecialchars($task['task_date']);
                  ?>
                </span>
                <span class="task-role <?php echo htmlspecialchars($task['task_for']); ?>">
                  <?php echo htmlspecialchars($task['task_for']); ?>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
          
          <a href="../calendar.php" class="view-all-btn hover-underline">
            View All Tasks <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- Fees Column -->
      <div class="dashboard-column">
        <div class="exam-card animate-fade delay-5 hover-grow">
          <div class="card-header">
            <h3 class="card-title">Upcoming Fees</h3>
            <select class="filter-select" id="fees_filter" onchange="filterFees(this.value)">
              <option value="all" <?php if($feesFilter==='all') echo 'selected'; ?>>All Classes</option>
              <?php foreach ($feesClasses as $class): ?>
                <option value="<?php echo htmlspecialchars($class); ?>" <?php if($feesFilter===$class) echo 'selected'; ?>><?php echo htmlspecialchars($class); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div id="fees-empty-state" class="empty-state" style="display: <?php echo empty($upcomingFees) ? 'flex' : 'none'; ?>">
            <i class="fas fa-money-bill-wave warning"></i>
            <p>No upcoming fees installments</p>
          </div>
          <ul id="fees-list" class="exam-list" style="display: <?php echo empty($upcomingFees) ? 'none' : 'block'; ?>">
            <?php foreach ($upcomingFees as $fee): ?>
              <li class="exam-item">
                <div class="exam-class"><?php echo htmlspecialchars($fee['class_code']); ?></div>
                <div class="exam-name"><?php echo htmlspecialchars($fee['student_name']); ?></div>
                <div class="exam-details">
                  <div class="exam-detail">
                    <i class="fas fa-hashtag"></i>
                    Installment <?php echo $fee['installment_no']; ?>
                  </div>
                  <div class="exam-detail">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo date('d M Y', strtotime($fee['due_date'])); ?>
                  </div>
                  <div class="exam-detail">
                    <i class="fas fa-rupee-sign"></i>
                    ₹<?php echo number_format($fee['amount'], 2); ?>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
          
          <a href="fees.php" class="view-all-btn hover-underline">
            Manage Fees <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- Exams Column -->
      <div class="dashboard-column">
        <div class="exam-card animate-fade delay-5 hover-grow">
          <div class="card-header">
            <h3 class="card-title">Upcoming Exams</h3>
            <select class="filter-select" id="exam_filter" onchange="filterExams(this.value)">
              <option value="latest" <?php if($examFilter==='latest') echo 'selected'; ?>>Latest</option>
              <option value="week" <?php if($examFilter==='week') echo 'selected'; ?>>This Week</option>
              <option value="month" <?php if($examFilter==='month') echo 'selected'; ?>>This Month</option>
            </select>
          </div>
          
          <div id="exams-empty-state" class="empty-state" style="display: <?php echo empty($upcomingExams) ? 'flex' : 'none'; ?>">
            <i class="fas fa-calendar-times warning"></i>
            <p>No upcoming exams scheduled</p>
          </div>
          <ul id="exams-list" class="exam-list" style="display: <?php echo empty($upcomingExams) ? 'none' : 'block'; ?>">
            <?php foreach ($upcomingExams as $exam): ?>
              <li class="exam-item">
                <div class="exam-class"><?php echo htmlspecialchars($exam['class_name']); ?></div>
                <div class="exam-name"><?php echo htmlspecialchars($exam['exam_name']); ?></div>
                <div class="exam-details">
                  <div class="exam-detail">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo htmlspecialchars($exam['exam_date']); ?>
                  </div>
                  <div class="exam-detail">
                    <i class="fas fa-clock"></i>
                    <?php echo htmlspecialchars($exam['start_time']); ?>
                  </div>
                  <div class="exam-detail">
                    <i class="fas fa-star"></i>
                    <?php echo htmlspecialchars($exam['total_marks']); ?> Marks
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
          
          <a href="../all_exam.php" class="view-all-btn hover-underline">
            View All Exams <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="inquiries-card animate-fade delay-5 hover-grow">
      <div class="card-header">
        <h3 class="card-title">Recent Inquiries</h3>
        <select class="filter-select" id="inq_filter" onchange="filterInquiries(this.value)">
          <option value="latest" <?php if($filter==='latest') echo 'selected'; ?>>Latest</option>
          <option value="week" <?php if($filter==='week') echo 'selected'; ?>>This Week</option>
          <option value="month" <?php if($filter==='month') echo 'selected'; ?>>This Month</option>
        </select>
      </div>
      
      <?php if (empty($inquiries)): ?>
        <div class="empty-state">
          <i class="fas fa-inbox primary"></i>
          <p>No recent inquiries found</p>
        </div>
      <?php else: ?>
        <div style="overflow-x: auto; flex-grow: 1;">
          <table id="inquiries-table" class="inquiry-table">
            <thead>
              <tr>
                <th>Student</th>
                <th>Contact</th>
                <th>Class</th>
                <th>Medium</th>
                <th>Follow Up</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($inquiries as $inq): ?>
                <?php
                  $studentName = htmlspecialchars($inq['student_name']);
                  $phone = htmlspecialchars($inq['student_mobile']);
                  $medium = htmlspecialchars($inq['medium']);
                  $class = htmlspecialchars($inq['std']);
                  $followupDate = $inq['followup_date'] ? date('d M Y', strtotime($inq['followup_date'])) : 'Not set';
                  $followupTime = $inq['followup_time'] ? date('h:i A', strtotime($inq['followup_time'])) : '';
                  $interestLevel = $inq['interest_level'] ?? 'medium';
                  
                  // Determine status based on interest level
                  $statusClass = 'status-new';
                  $statusText = 'New';
                  if ($interestLevel === 'high') {
                    $statusClass = 'status-converted';
                    $statusText = 'Hot Lead';
                  } elseif ($interestLevel === 'medium') {
                    $statusClass = 'status-followup';
                    $statusText = 'Follow Up';
                  }
                ?>
                <tr>
                  <td class="student-name"><?php echo $studentName; ?></td>
                  <td><?php echo $phone; ?></td>
                  <td>Class <?php echo $class; ?></td>
                  <td><?php echo $medium; ?></td>
                  <td>
                    <div class="followup-date">
                      <i class="fas fa-bell"></i>
                      <?php echo $followupDate; ?>
                      <?php if ($followupTime): ?>
                        <span style="margin-left: 10px;"><?php echo $followupTime; ?></span>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
      
      <a href="total_inquiry.php" class="view-all-btn hover-underline">
        View All Inquiries <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>



  <script>
    // Load notification counts
    function loadNotifications() {
      fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
          const birthdayNotif = document.getElementById('birthdayNotif');
          const feesNotif = document.getElementById('feesNotif');
          const inquiryNotif = document.getElementById('inquiryNotif');
          
          // Show/hide birthday notification
          if (data.birthdays > 0) {
            document.getElementById('birthdayCount').textContent = data.birthdays;
            birthdayNotif.style.display = 'flex';
          } else {
            birthdayNotif.style.display = 'none';
          }
          
          // Show/hide fees notification
          if (data.fees > 0) {
            document.getElementById('feesCount').textContent = data.fees;
            feesNotif.style.display = 'flex';
          } else {
            feesNotif.style.display = 'none';
          }
          
          // Show/hide inquiry notification
          if (data.inquiries > 0) {
            document.getElementById('inquiryCount').textContent = data.inquiries;
            inquiryNotif.style.display = 'flex';
          } else {
            inquiryNotif.style.display = 'none';
          }
        })
        .catch(error => {
          console.error('Error loading notifications:', error);
        });
    }

    // Add animation classes on scroll
    document.addEventListener('DOMContentLoaded', function() {
      loadNotifications();
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade');
          }
        });
      }, {
        threshold: 0.1
      });

      document.querySelectorAll('.stat-card, .quick-card, .task-card, .exam-card, .inquiries-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.classList.add(`delay-${(index % 5) + 1}`);
        observer.observe(card);
      });
    });

    // Checkbox functionality for tasks
    function attachCheckboxListeners() {
      document.querySelectorAll('.task-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
          const taskItem = this.closest('.task-item');
          if (this.checked) {
            taskItem.style.opacity = '0.6';
            taskItem.querySelector('.task-text').style.textDecoration = 'line-through';
          } else {
            taskItem.style.opacity = '1';
            taskItem.querySelector('.task-text').style.textDecoration = 'none';
          }
        });
      });
    }
    
    // Initialize checkbox listeners
    attachCheckboxListeners();

    // Filter tasks without page reload
    function filterTasks(roleFilter) {
      fetch('get_tasks_data.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'task_role_filter=' + encodeURIComponent(roleFilter)
      })
      .then(response => response.json())
      .then(data => {
        const tasksList = document.querySelector('#tasks-list');
        const emptyState = document.querySelector('#tasks-empty-state');
        
        if (data.tasks && data.tasks.length > 0) {
          tasksList.innerHTML = '';
          data.tasks.forEach(task => {
            const taskItem = document.createElement('li');
            taskItem.className = 'task-item';
            const dateArray = task.task_date.split('-');
            const formattedDate = dateArray.length === 3 ? `${dateArray[2]}-${dateArray[1]}-${dateArray[0]}` : task.task_date;
            taskItem.innerHTML = `
              <input type="checkbox" class="task-checkbox">
              <span class="task-text">${task.task_text}</span>
              <span class="task-date">${formattedDate}</span>
              <span class="task-role ${task.task_for}">${task.task_for}</span>
            `;
            tasksList.appendChild(taskItem);
          });
          tasksList.style.display = 'block';
          emptyState.style.display = 'none';
          // Re-attach checkbox event listeners
          attachCheckboxListeners();
        } else {
          tasksList.style.display = 'none';
          emptyState.style.display = 'flex';
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }

    // Filter fees without page reload
    function filterFees(classCode) {
      fetch('get_fees_data.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'fees_filter=' + encodeURIComponent(classCode)
      })
      .then(response => response.json())
      .then(data => {
        const feesList = document.querySelector('#fees-list');
        const emptyState = document.querySelector('#fees-empty-state');
        
        if (data.fees && data.fees.length > 0) {
          feesList.innerHTML = '';
          data.fees.forEach(fee => {
            const feeItem = document.createElement('li');
            feeItem.className = 'exam-item';
            feeItem.innerHTML = `
              <div class="exam-class">${fee.class_code}</div>
              <div class="exam-name">${fee.student_name}</div>
              <div class="exam-details">
                <div class="exam-detail">
                  <i class="fas fa-hashtag"></i>
                  Installment ${fee.installment_no}
                </div>
                <div class="exam-detail">
                  <i class="fas fa-calendar-alt"></i>
                  ${new Date(fee.due_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'})}
                </div>
                <div class="exam-detail">
                  <i class="fas fa-rupee-sign"></i>
                  ₹${parseFloat(fee.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}
                </div>
              </div>
            `;
            feesList.appendChild(feeItem);
          });
          feesList.style.display = 'block';
          emptyState.style.display = 'none';
        } else {
          feesList.style.display = 'none';
          emptyState.style.display = 'flex';
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }

    // Filter exams without page reload
    function filterExams(timeFilter) {
      fetch('get_exams_data.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'exam_filter=' + encodeURIComponent(timeFilter)
      })
      .then(response => response.json())
      .then(data => {
        const examsList = document.querySelector('#exams-list');
        const emptyState = document.querySelector('#exams-empty-state');
        
        if (data.exams && data.exams.length > 0) {
          examsList.innerHTML = '';
          data.exams.forEach(exam => {
            const examItem = document.createElement('li');
            examItem.className = 'exam-item';
            examItem.innerHTML = `
              <div class="exam-class">${exam.class_name}</div>
              <div class="exam-name">${exam.exam_name}</div>
              <div class="exam-details">
                <div class="exam-detail">
                  <i class="fas fa-calendar-alt"></i>
                  ${exam.exam_date}
                </div>
                <div class="exam-detail">
                  <i class="fas fa-clock"></i>
                  ${exam.start_time}
                </div>
                <div class="exam-detail">
                  <i class="fas fa-star"></i>
                  ${exam.total_marks} Marks
                </div>
              </div>
            `;
            examsList.appendChild(examItem);
          });
          examsList.style.display = 'block';
          emptyState.style.display = 'none';
        } else {
          examsList.style.display = 'none';
          emptyState.style.display = 'flex';
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }

    // Filter inquiries without page reload
    function filterInquiries(timeFilter) {
      fetch('get_inquiries_data.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'inq_filter=' + encodeURIComponent(timeFilter)
      })
      .then(response => response.json())
      .then(data => {
        const inquiriesTable = document.querySelector('#inquiries-table tbody');
        
        if (data.inquiries && data.inquiries.length > 0) {
          inquiriesTable.innerHTML = '';
          data.inquiries.forEach(inq => {
            const followupDate = inq.followup_date ? new Date(inq.followup_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'}) : 'Not set';
            const followupTime = inq.followup_time ? new Date('1970-01-01T' + inq.followup_time).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true}) : '';
            const interestLevel = inq.interest_level || 'medium';
            let statusClass = 'status-new';
            let statusText = 'New';
            if (interestLevel === 'high') {
              statusClass = 'status-converted';
              statusText = 'Hot Lead';
            } else if (interestLevel === 'medium') {
              statusClass = 'status-followup';
              statusText = 'Follow Up';
            }
            
            const row = document.createElement('tr');
            row.innerHTML = `
              <td class="student-name">${inq.student_name}</td>
              <td>${inq.student_mobile}</td>
              <td>Class ${inq.std}</td>
              <td>${inq.medium}</td>
              <td>
                <div class="followup-date">
                  <i class="fas fa-bell"></i>
                  ${followupDate}
                  ${followupTime ? '<span style="margin-left: 10px;">' + followupTime + '</span>' : ''}
                </div>
              </td>
              <td><span class="status-badge ${statusClass}">${statusText}</span></td>
            `;
            inquiriesTable.appendChild(row);
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }


  </script>
</body>
</html>