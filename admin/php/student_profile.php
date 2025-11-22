<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

require_once 'config.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$student_id = $_GET['id'] ?? null;

if (!$student_id) {
    header('Location: ../students.html');
    exit();
}

// Get student info with class name
$stmt = $conn->prepare("SELECT s.*, c.name as class_name FROM students s LEFT JOIN classes c ON s.class_code = c.code AND s.user_id = c.user_id WHERE s.id = ? AND s.user_id = ?");
$stmt->bind_param("ii", $student_id, $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Calculate actual attendance percentage
$attendance_percentage = 0;
if (isset($student['class_code'])) {
    // Check if attendance table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'attendance'");
    if ($table_check && $table_check->num_rows > 0) {
        // Get total attendance records for this student
        $total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM attendance WHERE student_id = ? AND user_id = ?");
        $total_stmt->bind_param("ii", $student_id, $user_id);
        $total_stmt->execute();
        $total_result = $total_stmt->get_result()->fetch_assoc();
        $total_days = $total_result['total'];
        $total_stmt->close();
        
        if ($total_days > 0) {
            // Get present days
            $present_stmt = $conn->prepare("SELECT COUNT(*) as present FROM attendance WHERE student_id = ? AND user_id = ? AND status = 'present'");
            $present_stmt->bind_param("ii", $student_id, $user_id);
            $present_stmt->execute();
            $present_result = $present_stmt->get_result()->fetch_assoc();
            $present_days = $present_result['present'];
            $present_stmt->close();
            
            $attendance_percentage = round(($present_days / $total_days) * 100, 1);
        }
    }
}

// Get comprehensive fee information
$fee_info = null;
$fee_stmt = $conn->prepare("SELECT decided_fees, installments FROM fees_structure WHERE student_id = ? AND user_id = ?");
$fee_stmt->bind_param("ii", $student_id, $user_id);
$fee_stmt->execute();
$fee_result = $fee_stmt->get_result()->fetch_assoc();
$fee_stmt->close();

if ($fee_result) {
    $installments = json_decode($fee_result['installments'], true) ?: [];
    $total_installments = count($installments);
    $total_decided_fees = floatval($fee_result['decided_fees']);
    
    // Get paid fees from paid_fees table
    $paid_stmt = $conn->prepare("SELECT installment_index, amount FROM paid_fees WHERE student_id = ? AND user_id = ? AND is_paid = 1");
    $paid_stmt->bind_param("ii", $student_id, $user_id);
    $paid_stmt->execute();
    $paid_result = $paid_stmt->get_result();
    
    $paid_installments = [];
    $fees_received = 0;
    while ($row = $paid_result->fetch_assoc()) {
        $paid_installments[] = $row['installment_index'];
        $fees_received += floatval($row['amount']);
    }
    $paid_stmt->close();
    
    // Find next unpaid installment
    $upcoming_installment = null;
    foreach ($installments as $index => $installment) {
        if (!in_array($index, $paid_installments)) {
            $upcoming_installment = $installment;
            break;
        }
    }
    
    $fee_info = [
        'total_decided_fees' => $total_decided_fees,
        'total_installments' => $total_installments,
        'fees_received' => $fees_received,
        'upcoming_installment' => $upcoming_installment,
        'balance_due' => $total_decided_fees - $fees_received
    ];
}

// Check if profile_image column exists, if not add it
$result = $conn->query("SHOW COLUMNS FROM students LIKE 'profile_image'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE students ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL");
    $student['profile_image'] = null;
}

if (!$student) {
    header('Location: ../students.html');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile | Acadexa</title>
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
            background-clip: text;
        }
        
        .logo img {
            height: 40px;
            width: auto;
            object-fit: contain;
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
        
        .main-content {
            padding: 30px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .profile-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        .back-btn {
            background: var(--primary);
            color: var(--white);
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--card-shadow);
        }
        
        .back-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .profile-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
            align-items: start;
        }
        
        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .profile-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            color: var(--white);
            box-shadow: var(--card-shadow-hover);
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 15px;
            backdrop-filter: blur(10px);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
            border-radius: 50%;
        }
        
        .profile-avatar:hover .upload-overlay {
            opacity: 1;
        }
        
        #imageUpload {
            display: none;
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .profile-id {
            opacity: 0.8;
            margin-bottom: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        
        .stats-card, .contact-card {
            background: var(--white);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .stat {
            text-align: center;
            padding: 15px;
            background: var(--light);
            border-radius: 12px;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .contact-item:last-child {
            border-bottom: none;
        }
        
        .contact-item i {
            color: var(--primary);
            width: 16px;
        }
        
        .profile-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .content-card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }
        
        .content-card:hover {
            box-shadow: var(--card-shadow-hover);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .card-badge {
            padding: 4px 12px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .profile-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .main-content {
                padding: 20px;
            }
            
            header {
                padding: 15px 20px;
            }
        } 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .card-badge.success {
            background: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .performance-item {
            text-align: center;
            padding: 20px;
            background: var(--light);
            border-radius: 12px;
        }
        
        .performance-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 8px;
        }
        
        .performance-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: var(--gray);
            font-weight: 500;
        }
        
        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .fee-summary {
            margin-bottom: 20px;
        }
        
        .fee-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .fee-row.total {
            border-top: 2px solid var(--primary);
            border-bottom: none;
            font-weight: 600;
            margin-top: 10px;
            padding-top: 15px;
        }
        
        .fee-label {
            color: var(--gray);
        }
        
        .fee-amount {
            font-weight: 600;
            color: var(--primary);
        }
        
        .payment-status {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .status-item {
            padding: 15px;
            background: var(--light);
            border-radius: 8px;
            text-align: center;
        }
        
        .status-label {
            font-size: 0.8rem;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .status-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .emergency-info {
            display: grid;
            gap: 15px;
        }
        
        .emergency-item {
            padding: 15px;
            background: var(--light);
            border-radius: 8px;
        }
        
        .emergency-label {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .emergency-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        @media (max-width: 1200px) {
            .profile-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .profile-sidebar {
                order: 2;
            }
            
            .profile-content {
                order: 1;
            }
        }
        
        .btn-view {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-view:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 2% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--card-shadow-hover);
        }
        
        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--primary);
            color: var(--white);
            border-radius: 16px 16px 0 0;
        }
        
        .modal-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }
        
        .close {
            color: var(--white);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .close:hover {
            opacity: 0.7;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .tab-container {
            display: flex;
            border-bottom: 1px solid var(--light-gray);
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-weight: 600;
            color: var(--gray);
            transition: var(--transition);
            border-bottom: 3px solid transparent;
        }
        
        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .installment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .installment-item:last-child {
            border-bottom: none;
        }
        
        .installment-edit {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 10px 0;
        }
        
        .installment-edit input {
            padding: 8px 12px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            font-size: 0.9rem;
        }
        
        .receipt-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 2fr auto;
            gap: 15px;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .payment-mode {
            display: flex;
            gap: 10px;
        }
        
        .payment-mode label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        
        .btn-generate {
            background: var(--success);
            color: var(--white);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            display: none;
        }
        
        .btn-generate.show {
            display: block;
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
            
            .main-content {
                padding: 20px;
            }
            
            .performance-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .payment-status {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
            
            .receipt-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include 'header_logo.php'; ?>
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="../students.html" class="active">Students</a>
            <a href="../attendance.html">Attendance</a>
            <a href="gradecard.php">Reports</a>
            <a href="inquiry.php">Inquiries</a>
            <a href="profile.php">Profile</a>
        </nav>
    </header>

    <div class="main-content">
        <div class="profile-header">
            <h1 class="profile-title">Student Profile</h1>
            <a href="../students.html" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>

        <!-- Main Profile Layout -->
        <div class="profile-layout">
            <!-- Left Sidebar -->
            <div class="profile-sidebar">
                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-avatar" onclick="document.getElementById('imageUpload').click()">
                        <?php if (!empty($student['profile_image']) && file_exists('../../' . $student['profile_image'])): ?>
                            <img src="/ACADEXA/<?= htmlspecialchars($student['profile_image']) ?>" alt="Profile Image">
                        <?php else: ?>
                            <span id="avatarLetter"><?= strtoupper(substr(htmlspecialchars($student['name']), 0, 1)) ?></span>
                        <?php endif; ?>
                        <div class="upload-overlay">
                            <i class="fas fa-camera" style="color: white; font-size: 1.2rem;"></i>
                        </div>
                    </div>
                    <input type="file" id="imageUpload" accept="image/*">
                    <h2 class="profile-name"><?= htmlspecialchars($student['name']) ?></h2>
                    <div class="profile-status">
                        <span class="status-badge active">Active Student</span>
                        <p class="profile-roll" style="margin-top: 8px; opacity: 0.8; font-size: 0.9rem;">Roll No: <?= htmlspecialchars($student['roll_no'] ?? 'Not provided') ?></p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="stats-card">
                    <h3 class="card-title">Quick Stats</h3>
                    <div class="stat-grid">
                        <div class="stat">
                            <div class="stat-number"><?= $student['dob'] ? floor((time() - strtotime($student['dob'])) / (365.25 * 24 * 60 * 60)) : 'N/A' ?></div>
                            <div class="stat-label">Age</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number"><?= $attendance_percentage ?>%</div>
                            <div class="stat-label">Attendance</div>
                        </div>
                    </div>
                </div>

                <!-- Contact Card -->
                <div class="contact-card">
                    <h3 class="card-title">Contact Info</h3>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($student['parent_contact'] ?? 'Not provided') ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($student['email']) ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($student['address'] ?? 'Not provided') ?></span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="profile-content">
                <!-- Academic Performance -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> Academic Performance</h3>
                        <span class="card-badge">Current Semester</span>
                    </div>
                    <div class="performance-grid">
                        <div class="performance-item">
                            <div class="performance-label">Overall GPA</div>
                            <div class="performance-value">3.8</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Subjects Enrolled</div>
                            <div class="performance-value">6</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Assignments Completed</div>
                            <div class="performance-value">24/28</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Class Rank</div>
                            <div class="performance-value">#5</div>
                        </div>
                    </div>
                </div>

                <!-- Academic Details -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-graduation-cap"></i> Academic Details</h3>
                    </div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Class</span>
                            <span class="detail-value"><?= htmlspecialchars($student['class_name'] ?? $student['class_code']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Enrollment Date</span>
                            <span class="detail-value"><?= date('M j, Y', strtotime($student['created_at'] ?? 'now')) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Academic Year</span>
                            <span class="detail-value">2024-2025</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Student Type</span>
                            <span class="detail-value">Regular</span>
                        </div>
                    </div>
                </div>

                <!-- Fee Information -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-credit-card"></i> Fee Information</h3>
                        <button class="btn-view" onclick="openFeeModal(<?= $student_id ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                    <?php if ($fee_info): ?>
                        <div class="fee-summary">
                            <div class="fee-row">
                                <span class="fee-label">Total Decided Fees</span>
                                <span class="fee-amount">₹<?= number_format($fee_info['total_decided_fees'], 2) ?></span>
                            </div>
                            <div class="fee-row">
                                <span class="fee-label">Total Installments</span>
                                <span class="fee-amount"><?= $fee_info['total_installments'] ?></span>
                            </div>
                            <div class="fee-row">
                                <span class="fee-label">Fees Received</span>
                                <span class="fee-amount" style="color: var(--success);">₹<?= number_format($fee_info['fees_received'], 2) ?></span>
                            </div>
                            <div class="fee-row">
                                <span class="fee-label">Balance Due</span>
                                <span class="fee-amount" style="color: var(--danger);">₹<?= number_format($fee_info['balance_due'], 2) ?></span>
                            </div>
                            <?php if ($fee_info['upcoming_installment']): ?>
                                <div class="fee-row total">
                                    <span class="fee-label">Next Installment</span>
                                    <span class="fee-amount">₹<?= number_format($fee_info['upcoming_installment']['amount'], 2) ?> <br><small style="font-size: 0.8rem; color: var(--gray);">Due: <?= date('M j, Y', strtotime($fee_info['upcoming_installment']['due_date'])) ?></small></span>
                                </div>
                            <?php else: ?>
                                <div class="fee-row total">
                                    <span class="fee-label">Next Installment</span>
                                    <span class="fee-amount" style="color: var(--success);">All Paid</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="fee-summary">
                            <div class="fee-row">
                                <span class="fee-label">No fee structure found</span>
                                <span class="fee-amount">-</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Student Details -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user"></i> Student Details</h3>
                    </div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Date of Birth</span>
                            <span class="detail-value"><?= $student['dob'] ? date('M j, Y', strtotime($student['dob'])) : 'Not provided' ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Parent Contact</span>
                            <span class="detail-value"><?= htmlspecialchars($student['parent_contact'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Student Contact</span>
                            <span class="detail-value"><?= htmlspecialchars($student['student_contact'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Standard</span>
                            <span class="detail-value"><?= htmlspecialchars($student['std'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Medium</span>
                            <span class="detail-value"><?= htmlspecialchars($student['medium'] ?? 'Not provided') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fee Modal -->
    <div id="feeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Fee Management</h2>
                <span class="close" onclick="closeFeeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="tab-container">
                    <button class="tab active" onclick="switchTab('structure')">Fee Structure</button>
                    <button class="tab" onclick="switchTab('receipt')">Receipt</button>
                </div>
                
                <div id="structure-tab" class="tab-content active">
                    <div id="fee-structure-content">
                        <p>Loading fee structure...</p>
                    </div>
                </div>
                
                <div id="receipt-tab" class="tab-content">
                    <div id="receipt-content">
                        <p>Loading receipt information...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('student_id', '<?= $student_id ?>');
        
        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const avatar = document.querySelector('.profile-avatar');
                const letter = document.getElementById('avatarLetter');
                const existingImg = avatar.querySelector('img');
                
                if (existingImg) {
                    existingImg.src = data.image_url;
                } else {
                    const img = document.createElement('img');
                    img.src = data.image_url;
                    img.alt = 'Profile Image';
                    
                    if (letter) letter.style.display = 'none';
                    avatar.appendChild(img);
                }
                
                alert('Profile image updated successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Upload failed. Please try again.');
        });
    });
    
    function openFeeModal(studentId) {
        document.getElementById('feeModal').style.display = 'block';
        loadFeeData(studentId);
    }
    
    function closeFeeModal() {
        document.getElementById('feeModal').style.display = 'none';
    }
    
    function switchTab(tabName) {
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        event.target.classList.add('active');
        document.getElementById(tabName + '-tab').classList.add('active');
    }
    
    function loadFeeData(studentId) {
        fetch('get_fee_details.php?student_id=' + studentId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayFeeStructure(data.fee_data);
                displayReceiptSection(data.fee_data);
            } else {
                document.getElementById('fee-structure-content').innerHTML = '<p>No fee structure found for this student.</p>';
                document.getElementById('receipt-content').innerHTML = '<p>No fee data available.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('fee-structure-content').innerHTML = '<p>Error loading fee data.</p>';
        });
    }
    
    function displayFeeStructure(feeData) {
        const installments = JSON.parse(feeData.installments || '[]');
        let html = `
            <div style="margin-bottom: 20px;">
                <h4>Decided Fees: ₹${parseFloat(feeData.decided_fees).toFixed(2)}</h4>
            </div>
            <div id="installments-edit">
                <h5>Installments:</h5>
        `;
        
        installments.forEach((installment, index) => {
            html += `
                <div class="installment-edit">
                    <input type="number" value="${installment.amount}" step="0.01" onchange="updateInstallment(${index}, 'amount', this.value)">
                    <input type="date" value="${installment.due_date}" onchange="updateInstallment(${index}, 'due_date', this.value)">
                    <button onclick="removeInstallment(${index})" style="background: var(--danger); color: white; border: none; padding: 8px; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        });
        
        html += `
            </div>
            <button onclick="saveUpdatedFees(${feeData.student_id})" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 6px; margin-top: 15px; cursor: pointer;">
                Save Changes
            </button>
        `;
        
        document.getElementById('fee-structure-content').innerHTML = html;
    }
    
    function displayReceiptSection(feeData) {
        const installments = JSON.parse(feeData.installments || '[]');
        const paidStatus = feeData.paid_status || {};
        let html = '<h5>Payment Receipts:</h5>';
        
        installments.forEach((installment, index) => {
            const isPaid = paidStatus[index]?.is_paid || false;
            const paymentMode = paidStatus[index]?.payment_mode || '';
            const statusText = isPaid ? 'PAID' : 'PENDING';
            const statusColor = isPaid ? 'var(--success)' : 'var(--warning)';
            
            html += `
                <div class="receipt-row">
                    <div>₹${installment.amount}</div>
                    <div>${installment.due_date}</div>
                    <div class="payment-status" style="color: ${statusColor}; font-weight: bold;">
                        ${statusText}
                    </div>
                    ${!isPaid ? `
                        <div class="payment-mode">
                            <label><input type="radio" name="payment_${index}" value="cash" onchange="showGenerateButton(${index})"> Cash</label>
                            <label><input type="radio" name="payment_${index}" value="cheque" onchange="showGenerateButton(${index})"> Cheque</label>
                            <label><input type="radio" name="payment_${index}" value="online" onchange="showGenerateButton(${index})"> Online</label>
                        </div>
                        <button class="btn-generate" id="generate_${index}" onclick="generateReceipt(${index}, ${feeData.student_id})">
                            Generate Receipt
                        </button>
                    ` : `
                        <div style="color: var(--success); font-weight: 500;">
                            Paid via ${paymentMode.toUpperCase()}
                        </div>
                        <button class="btn-generate" style="background: var(--success);" onclick="downloadReceipt(${index}, ${feeData.student_id})">
                            Download Receipt
                        </button>
                    `}
                </div>
            `;
        });
        
        document.getElementById('receipt-content').innerHTML = html;
    }
    
    function showGenerateButton(index) {
        document.getElementById('generate_' + index).classList.add('show');
    }
    
    function generateReceipt(index, studentId) {
        const paymentMode = document.querySelector(`input[name="payment_${index}"]:checked`)?.value;
        if (!paymentMode) {
            alert('Please select a payment mode');
            return;
        }
        
        const btn = document.getElementById(`generate_${index}`);
        btn.innerHTML = 'Generating...';
        btn.disabled = true;
        
        fetch('generate_receipt.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                student_id: studentId,
                installment_index: index,
                payment_mode: paymentMode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Receipt generated successfully!');
                loadFeeData(studentId); // Reload fee data
            } else {
                alert('Error: ' + data.message);
                btn.innerHTML = 'Generate Receipt';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating receipt');
            btn.innerHTML = 'Generate Receipt';
            btn.disabled = false;
        });
    }
    
    function downloadReceipt(index, studentId) {
        // Generate PDF receipt for paid installment
        window.open(`download_receipt.php?student_id=${studentId}&installment_index=${index}`, '_blank');
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('feeModal');
        if (event.target == modal) {
            closeFeeModal();
        }
    }
    </script>
</body>
</html>