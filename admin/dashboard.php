<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<p style='text-align:center;color:red;'>User not authenticated!</p>";
    exit();
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

$conn->close();
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

    nav {
      display: flex;
      gap: 15px;
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
      display: flex;
      gap: 30px;
      margin-bottom: 30px;
    }

    .dashboard-column {
      flex: 1;
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
      flex-grow: 1;
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
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      border: 1px solid rgba(67, 97, 238, 0.1);
    }

    .exam-list {
      list-style: none;
      flex-grow: 1;
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
        flex-direction: column;
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
  </style>
</head>

<body>
  <header>
    <div class="logo">Acadexa</div>
    <nav>
      <a href="dashboard.php" class="active">Home</a>
      <a href="createclass.html">Classes</a>
      <a href="attendance.html">Attendance</a>
      <a href="gradecard.php">Reports</a>
      <a href="inquiry.php">Inquiries</a>
      <a href="profile.php">Profile</a>
    </nav>
  </header>

  <div class="container">
    <h1 class="animate-fade">Dashboard</h1>
    <p class="subtext animate-fade delay-1">Welcome back! Here's what's happening with your academy today.</p>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card animate-fade delay-1 hover-grow">
        <a href="students.php" style="text-decoration: none; color: inherit;">
          <div class="icon">
            <i class="fas fa-user-graduate"></i>
          </div>
          <div class="value"><?php echo $studentCount; ?></div>
          <div class="label">Total Students</div>
          <div class="change up">
            <i class="fas fa-arrow-up"></i> 12% from last month
          </div>
        </a>
      </div>

      <div class="stat-card animate-fade delay-2 hover-grow">
        <div class="icon">
          <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="value"><?php echo $Classcount; ?></div>
        <div class="label">Active Classes</div>
        <div class="change up">
          <i class="fas fa-arrow-up"></i> 3 new this month
        </div>
      </div>

      <div class="stat-card animate-fade delay-3 hover-grow">
        <a href="" style="text-decoration: none; color: inherit;">
          <div class="icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="value">86%</div>
          <div class="label">Attendance Rate</div>
          <div class="change down">
            <i class="fas fa-arrow-down"></i> 2% from last week
          </div>
        </a>
      </div>

      <div class="stat-card animate-fade delay-4 hover-grow">
        <a href="total_inquiry.php" style="text-decoration: none; color: inherit;">
          <div class="icon">
            <i class="fas fa-question-circle"></i>
          </div>
          <div class="value"><?php echo $inquiryCount; ?></div>
          <div class="label">Total Inquiries</div>
          <div class="change up">
            <i class="fas fa-arrow-up"></i> 5 new this week
          </div>
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
          <a href="calendar.php" class="btn">Go to Tasks</a>
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
          <a href="studymaterial.html" class="btn">Upload Materials</a>
        </div>
      </div>

      <div class="quick-card animate-fade delay-3 hover-grow">
        <div class="icon">
          <i class="fas fa-headset"></i>
        </div>
        <div class="title">Manage Inquiry</div>
        <div class="desc">Handle student inquiries</div>
        <div class="btn-container">
          <a href="total_inquiry.php" class="btn">Manage</a>
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
            <form method="get">
              <select class="filter-select" name="task_role_filter" onchange="this.form.submit()">
                <option value="">All Tasks</option>
                <option value="admin" <?php if($taskRoleFilter==='admin') echo 'selected'; ?>>Admin</option>
                <option value="trustee" <?php if($taskRoleFilter==='trustee') echo 'selected'; ?>>Trustee</option>
                <option value="tutor" <?php if($taskRoleFilter==='tutor') echo 'selected'; ?>>Tutor</option>
              </select>
              <?php
                foreach ($_GET as $k => $v) {
                  if ($k !== 'task_role_filter') {
                    echo '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars($v).'">';
                  }
                }
              ?>
            </form>
          </div>
          
          <?php if (empty($userTasks)): ?>
            <div class="empty-state">
              <i class="fas fa-check-circle success"></i>
              <p>No tasks found. Enjoy your free time!</p>
            </div>
          <?php else: ?>
            <ul class="task-list">
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
          <?php endif; ?>
          
          <a href="calendar.php" class="view-all-btn hover-underline">
            View All Tasks <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- Exams Column -->
      <div class="dashboard-column">
        <div class="exam-card animate-fade delay-5 hover-grow">
          <div class="card-header">
            <h3 class="card-title">Upcoming Exams</h3>
            <form method="get">
              <select class="filter-select" name="exam_filter" onchange="this.form.submit()">
                <option value="latest" <?php if($examFilter==='latest') echo 'selected'; ?>>Latest</option>
                <option value="week" <?php if($examFilter==='week') echo 'selected'; ?>>This Week</option>
                <option value="month" <?php if($examFilter==='month') echo 'selected'; ?>>This Month</option>
              </select>
              <?php
                foreach ($_GET as $k => $v) {
                  if ($k !== 'exam_filter') {
                    echo '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars($v).'">';
                  }
                }
              ?>
            </form>
          </div>
          
          <?php if (empty($upcomingExams)): ?>
            <div class="empty-state">
              <i class="fas fa-calendar-times warning"></i>
              <p>No upcoming exams scheduled</p>
            </div>
          <?php else: ?>
            <ul class="exam-list">
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
          <?php endif; ?>
          
          <a href="all_exam.php" class="view-all-btn hover-underline">
            View All Exams <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="inquiries-card animate-fade delay-5 hover-grow">
      <div class="card-header">
        <h3 class="card-title">Recent Inquiries</h3>
        <form method="get">
          <select class="filter-select" name="inq_filter" onchange="this.form.submit()">
            <option value="latest" <?php if($filter==='latest') echo 'selected'; ?>>Latest</option>
            <option value="week" <?php if($filter==='week') echo 'selected'; ?>>This Week</option>
            <option value="month" <?php if($filter==='month') echo 'selected'; ?>>This Month</option>
          </select>
        </form>
      </div>
      
      <?php if (empty($inquiries)): ?>
        <div class="empty-state">
          <i class="fas fa-inbox primary"></i>
          <p>No recent inquiries found</p>
        </div>
      <?php else: ?>
        <div style="overflow-x: auto; flex-grow: 1;">
          <table class="inquiry-table">
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
    // Add animation classes on scroll
    document.addEventListener('DOMContentLoaded', function() {
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

    // Simple checkbox functionality for tasks
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
  </script>
</body>
</html>