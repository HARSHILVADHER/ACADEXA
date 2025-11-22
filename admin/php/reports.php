<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --primary-dark: #3a0ca3;
            --white: #ffffff;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --dark: #1a1a1a;
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
        }
        
        nav a {
            text-decoration: none;
            color: var(--gray);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
            font-size: 0.95rem;
        }
        
        nav a:hover, nav a.active {
            color: var(--primary);
            background: var(--primary-light);
        }
        
        .container {
            padding: 30px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .page-header {
            margin-bottom: 40px;
        }
        
        .page-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        .page-subtitle {
            color: var(--gray);
            font-size: 1rem;
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
        }
        
        .report-card {
            background: var(--white);
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid rgba(67, 97, 238, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        }
        
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .report-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        
        .report-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .report-desc {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
        }
        
        .download-btn:hover {
            background: var(--primary-dark);
            transform: translateX(5px);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .reports-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include 'header_logo.php'; ?>
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="../createclass.html">Classes</a>
            <a href="../attendance.html">Attendance</a>
            <a href="gradecard.php">Reports</a>
            <a href="inquiry.php">Inquiries</a>
            <a href="profile.php">Profile</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Institute Reports</h1>
            <p class="page-subtitle">Download comprehensive reports for your institute</p>
        </div>

        <div class="reports-grid">
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="report-title">Student Analysis</h3>
                <p class="report-desc">Complete student analysis with performance, marks, grade distribution, personal details, classes, and contact information</p>
                <a href="exam_reports.php" class="download-btn">
                    <i class="fas fa-download"></i>
                    Download Report
                </a>
            </div>

            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="report-title">Attendance Report</h3>
                <p class="report-desc">Detailed attendance records with statistics and trends for all classes</p>
                <a href="attendance_reports.php" class="download-btn">
                    <i class="fas fa-download"></i>
                    Download Report
                </a>
            </div>

            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <h3 class="report-title">Fees Report</h3>
                <p class="report-desc">Complete fees collection report with pending and paid installments</p>
                <a href="fees_reports.php" class="download-btn">
                    <i class="fas fa-download"></i>
                    Download Report
                </a>
            </div>

            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 class="report-title">Inquiry Report</h3>
                <p class="report-desc">All inquiries with follow-up status and conversion rates</p>
                <a href="inquiry_reports.php" class="download-btn">
                    <i class="fas fa-download"></i>
                    Download Report
                </a>
            </div>

            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="report-title">Class Report</h3>
                <p class="report-desc">Overview of all classes with student count and schedules</p>
                <a href="#" class="download-btn">
                    <i class="fas fa-download"></i>
                    Download Report
                </a>
            </div>
        </div>
    </div>
</body>
</html>
