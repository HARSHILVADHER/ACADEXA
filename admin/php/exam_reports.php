<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Report - Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
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
        
        .report-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .tab-btn {
            padding: 12px 30px;
            border: none;
            background: var(--white);
            color: var(--gray);
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--card-shadow);
        }
        
        .tab-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
        }
        
        .filter-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }
        
        .filter-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
        }
        
        .filter-row {
            display: flex;
            gap: 20px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .input-group {
            flex: 1;
            min-width: 200px;
        }
        
        .input-group label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .input-field {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: var(--transition);
            outline: none;
        }
        
        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .btn-primary {
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(67, 97, 238, 0.4);
        }
        
        .data-section {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
        }
        
        .exam-details {
            background: linear-gradient(135deg, var(--primary-light), #f0f4ff);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 2px solid var(--primary);
        }
        
        .exam-details h3 {
            font-size: 1.3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: var(--gray);
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 1rem;
            color: var(--dark);
            font-weight: 700;
        }
        
        .exam-card {
            background: var(--white);
            border: 2px solid var(--light-gray);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: var(--transition);
            position: relative;
        }
        
        .exam-card:hover {
            border-color: var(--primary);
            box-shadow: var(--card-shadow);
        }
        
        .exam-card h4 {
            font-size: 1.1rem;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .exam-card p {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .btn-view {
            position: absolute;
            bottom: 15px;
            right: 15px;
            padding: 8px 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.4);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            overflow-y: auto;
        }
        
        .modal-content {
            background-color: #f8f9fa;
            margin: 1.5% auto;
            padding: 0;
            border-radius: 20px;
            width: 92%;
            max-width: 1100px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            animation: slideDown 0.4s ease;
            overflow: hidden;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-80px) scale(0.95); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--white);
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }
        
        .modal-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .close {
            color: var(--white);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            transition: var(--transition);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 40px;
            background: #f8f9fa;
        }
        
        .performance-overview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 35px;
        }
        
        .stats-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .performance-card {
            background: var(--white);
            padding: 20px 25px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            border-left: 5px solid;
        }
        
        .performance-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }
        
        .performance-card.high {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #ffffff 0%, #ecfdf5 100%);
        }
        
        .performance-card.average {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
        }
        
        .performance-card.poor {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
        }
        
        .performance-card-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .performance-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .performance-card.high .performance-icon {
            background: #d1fae5;
            color: #10b981;
        }
        
        .performance-card.average .performance-icon {
            background: #fef3c7;
            color: #f59e0b;
        }
        
        .performance-card.poor .performance-icon {
            background: #fee2e2;
            color: #ef4444;
        }
        
        .performance-info h3 {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .performance-info p {
            font-size: 0.95rem;
            color: var(--dark);
            font-weight: 700;
        }
        
        .performance-card .count {
            font-size: 2.2rem;
            font-weight: 800;
        }
        
        .performance-card.high .count {
            color: #10b981;
        }
        
        .performance-card.average .count {
            color: #f59e0b;
        }
        
        .performance-card.poor .count {
            color: #ef4444;
        }
        
        .chart-section {
            background: var(--white);
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .chart-section h3 {
            font-size: 1rem;
            color: var(--dark);
            margin-bottom: 25px;
            font-weight: 700;
            text-align: center;
        }
        
        .chart-wrapper {
            width: 100%;
            max-width: 350px;
            height: 350px;
            position: relative;
        }
        
        .recommendation {
            background: var(--white);
            padding: 25px 30px;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #667eea;
        }
        
        .recommendation h3 {
            font-size: 1.1rem;
            color: #667eea;
            margin-bottom: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .recommendation p {
            font-size: 0.95rem;
            color: #4b5563;
            line-height: 1.7;
        }
        
        .marks-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }
        
        .marks-card {
            background: var(--white);
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            border-top: 4px solid;
        }
        
        .marks-card.highest {
            border-top-color: #10b981;
        }
        
        .marks-card.avg {
            border-top-color: #3b82f6;
        }
        
        .marks-card.lowest {
            border-top-color: #ef4444;
        }
        
        .marks-card h4 {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .marks-card .marks-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 5px;
        }
        
        .marks-card.highest .marks-value {
            color: #10b981;
        }
        
        .marks-card.avg .marks-value {
            color: #3b82f6;
        }
        
        .marks-card.lowest .marks-value {
            color: #ef4444;
        }
        
        .marks-card p {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .stat-box {
            background: linear-gradient(135deg, var(--primary-light), #f0f4ff);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid var(--primary);
            margin-bottom: 30px;
        }
        
        .stat-box h3 {
            font-size: 0.95rem;
            color: var(--gray);
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .stat-box p {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }
        
        .class-checkbox-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: var(--white);
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .checkbox-item:hover {
            border-color: var(--primary);
        }
        
        .checkbox-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }
        
        .checkbox-item label {
            font-weight: 600;
            color: var(--dark);
            cursor: pointer;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }
        
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .report-table thead {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }
        
        .report-table th {
            padding: 15px;
            text-align: left;
            color: var(--white);
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .report-table td {
            padding: 15px;
            border-bottom: 1px solid var(--light-gray);
            color: var(--dark);
            font-size: 0.95rem;
        }
        
        .report-table tbody tr:hover {
            background: var(--primary-light);
        }
        
        .download-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }
        
        .btn-download {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--card-shadow);
        }
        
        .btn-download:first-child {
            background: #dc3545;
            color: var(--white);
        }
        
        .btn-download:first-child:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .btn-download:last-child {
            background: #28a745;
            color: var(--white);
        }
        
        .btn-download:last-child:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .rank-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .rank-1 {
            background: #ffd700;
            color: #000;
        }
        
        .rank-2 {
            background: #c0c0c0;
            color: #000;
        }
        
        .rank-3 {
            background: #cd7f32;
            color: #fff;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .input-group {
                width: 100%;
            }
            
            .report-tabs {
                flex-wrap: wrap;
            }
            
            .class-checkbox-list {
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
            <h1 class="page-title">Student Analysis</h1>
            <p class="page-subtitle">Complete student analysis with performance, marks, grade distribution, personal details, classes, and contact information</p>
        </div>

        <div class="report-tabs">
            <button class="tab-btn active" onclick="switchTab('student')">Individual Student Report</button>
            <button class="tab-btn" onclick="switchTab('exammarks')">Exam Marks Report</button>
            <button class="tab-btn" onclick="switchTab('subjectwise')">Subject Wise Exam Report</button>
            <button class="tab-btn" onclick="switchTab('institute')">See Your Top Students</button>
        </div>

        <!-- Student Report Tab -->
        <div id="student-report" class="report-section">
            <div class="filter-card">
                <h3>Select Student</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Class</label>
                        <select id="class_select_progress" class="input-field" required>
                            <option value="">Choose a class</option>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label>Student</label>
                        <select id="student_select_progress" class="input-field" required>
                            <option value="">Choose a student</option>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label>Start Date</label>
                        <input type="date" id="start_date_progress" class="input-field" required>
                    </div>
                    
                    <div class="input-group">
                        <label>End Date</label>
                        <input type="date" id="end_date_progress" class="input-field" required>
                    </div>
                    
                    <button type="button" class="btn-primary" onclick="generateProgressReport()">Generate Report</button>
                </div>
            </div>
            
            <div id="progress-results-container"></div>
        </div>

        <!-- Exam Marks Report Tab -->
        <div id="exammarks-report" class="report-section" style="display:none;">
            <div class="filter-card">
                <h3>Select Filters</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Class</label>
                        <select id="class_select_marks" class="input-field" required>
                            <option value="">Choose a class</option>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label>Exam</label>
                        <select id="exam_select_marks" class="input-field" required>
                            <option value="">Choose an exam</option>
                        </select>
                    </div>
                    
                    <button type="button" class="btn-primary" onclick="generateMarksReport()">Generate Report</button>
                </div>
            </div>
            
            <div id="marks-results-container"></div>
        </div>

        <!-- Subject Wise Exam Report Tab -->
        <div id="subjectwise-report" class="report-section" style="display:none;">
            <div class="filter-card">
                <h3>Select Filters</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Class</label>
                        <select id="subject-class-select" class="input-field" onchange="loadSubjectsByClass()">
                            <option value="">Select Class</option>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label>Subject</label>
                        <select id="subject-select" class="input-field">
                            <option value="">Select Subject</option>
                        </select>
                    </div>
                    
                    <button type="button" class="btn-primary" onclick="generateSubjectReport()">Generate Report</button>
                </div>
            </div>

            <div id="subject-report-container"></div>
        </div>

        <!-- Institute wise Tab -->
        <div id="institute-report" class="report-section" style="display:none;">
            <div class="stat-box">
                <h3>Total Exams Conducted</h3>
                <p id="total-exams">0</p>
            </div>

            <div class="filter-card">
                <h3>Select Classes</h3>
                <p style="color: var(--gray); margin-bottom: 20px;">Choose classes to view top performing students</p>
                <div id="class-checkbox-container" class="class-checkbox-list"></div>
            </div>

            <div id="exam-selection-card" class="filter-card" style="display:none;">
                <h3>Select Exams</h3>
                <p style="color: var(--gray); margin-bottom: 20px;">Choose exams from selected classes</p>
                <div id="exam-checkbox-container" class="class-checkbox-list"></div>
            </div>

            <div id="top-n-selection-card" class="filter-card" style="display:none;">
                <h3>Top Students</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Number of Top Students</label>
                        <select id="top-n-select" class="input-field">
                            <option value="">Select Number</option>
                        </select>
                    </div>
                    <button class="btn-primary" onclick="loadTopNStudents()"><i class="fas fa-trophy"></i> Show Top Students</button>
                </div>
            </div>

            <div id="top-students-data" class="data-section" style="display:none;">
                <h3 style="margin-bottom: 20px;" id="top-students-title">Top Students</h3>
                <div class="table-container">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Student Name</th>
                                <th>Roll No</th>
                                <th>Class</th>
                                <th>Total Marks</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody id="top-students-tbody"></tbody>
                    </table>
                </div>
                <div class="download-actions">
                    <button class="btn-download" onclick="generateReport()"><i class="fas fa-file-alt"></i> Generate Report</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Modal -->
    <div id="performanceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalExamTitle"><i class="fas fa-chart-pie"></i> Exam Performance Analysis</h2>
                <span class="close" onclick="closePerformanceModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="marks-summary">
                    <div class="marks-card highest">
                        <h4>Highest Marks</h4>
                        <div class="marks-value" id="highestMarks">0</div>
                        <p>Maximum Score</p>
                    </div>
                    <div class="marks-card avg">
                        <h4>Average Marks</h4>
                        <div class="marks-value" id="averageMarks">0</div>
                        <p>Mean Score</p>
                    </div>
                    <div class="marks-card lowest">
                        <h4>Lowest Marks</h4>
                        <div class="marks-value" id="lowestMarks">0</div>
                        <p>Minimum Score</p>
                    </div>
                </div>
                
                <div class="performance-overview">
                    <div class="stats-section">
                        <div class="performance-card high">
                            <div class="performance-card-content">
                                <div class="performance-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="performance-info">
                                    <h3>Highest Marks</h3>
                                    <p>75% and Above</p>
                                </div>
                            </div>
                            <div class="count" id="highCount">0</div>
                        </div>
                        <div class="performance-card average">
                            <div class="performance-card-content">
                                <div class="performance-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="performance-info">
                                    <h3>Average Marks</h3>
                                    <p>Pass to 75%</p>
                                </div>
                            </div>
                            <div class="count" id="avgCount">0</div>
                        </div>
                        <div class="performance-card poor">
                            <div class="performance-card-content">
                                <div class="performance-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="performance-info">
                                    <h3>Poor Marks</h3>
                                    <p>Below Passing</p>
                                </div>
                            </div>
                            <div class="count" id="poorCount">0</div>
                        </div>
                    </div>
                    
                    <div class="chart-section">
                        <h3>Performance Distribution</h3>
                        <div class="chart-wrapper">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="recommendation" id="recommendationBox">
                    <h3><i class="fas fa-lightbulb"></i> Recommendation</h3>
                    <p id="recommendationText">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        let topStudentsData = [];

        function switchTab(tab) {
            document.querySelectorAll('.report-tabs > .tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('student-report').style.display = 'none';
            document.getElementById('exammarks-report').style.display = 'none';
            document.getElementById('subjectwise-report').style.display = 'none';
            document.getElementById('institute-report').style.display = 'none';
            
            if(tab === 'student') {
                document.getElementById('student-report').style.display = 'block';
                loadStudentReportClasses();
            } else if(tab === 'exammarks') {
                document.getElementById('exammarks-report').style.display = 'block';
                loadExamMarksClasses();
            } else if(tab === 'subjectwise') {
                document.getElementById('subjectwise-report').style.display = 'block';
                loadSubjectWiseClasses();
            } else if(tab === 'institute') {
                document.getElementById('institute-report').style.display = 'block';
                loadInstituteData();
            }
        }



        function loadSubjectWiseClasses() {
            fetch('get_classes.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('subject-class-select');
                    select.innerHTML = '<option value="">Select Class</option>';
                    data.forEach(cls => {
                        select.innerHTML += `<option value="${cls.code}">${cls.name} (${cls.code})</option>`;
                    });
                })
                .catch(err => console.error('Error:', err));
        }

        function loadSubjectsByClass() {
            const classCode = document.getElementById('subject-class-select').value;
            const subjectSelect = document.getElementById('subject-select');
            
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            document.getElementById('subject-report-container').innerHTML = '';
            
            if(!classCode) return;
            
            fetch('get_subjects_by_class.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `class_code=${classCode}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.length === 0) {
                    subjectSelect.innerHTML = '<option value="">No subjects found</option>';
                } else {
                    data.forEach(subject => {
                        subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
                    });
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to load subjects');
            });
        }

        function generateSubjectReport() {
            const classCode = document.getElementById('subject-class-select').value;
            const subjectId = document.getElementById('subject-select').value;
            
            if(!classCode || !subjectId) {
                alert('Please select both class and subject');
                return;
            }
            
            // Backend processing will be added here
            document.getElementById('subject-report-container').innerHTML = '<div class="data-section"><p style="text-align:center; color: var(--gray); padding: 40px;">Report generation in progress...</p></div>';
        }

        function loadInstituteData() {
            fetch('get_institute_exam_stats.php')
                .then(res => {
                    if(!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    document.getElementById('total-exams').textContent = data.total_exams || 0;
                    
                    const container = document.getElementById('class-checkbox-container');
                    container.innerHTML = '';
                    
                    data.classes.forEach(cls => {
                        container.innerHTML += `
                            <div class="checkbox-item">
                                <input type="checkbox" id="class-${cls.code}" value="${cls.code}" onchange="loadExamsForSelectedClasses()">
                                <label for="class-${cls.code}">${cls.name} (${cls.code})</label>
                            </div>
                        `;
                    });
                    
                    const topNSelect = document.getElementById('top-n-select');
                    topNSelect.innerHTML = '<option value="">Select Number</option>';
                    for(let i = 1; i <= 20; i++) {
                        topNSelect.innerHTML += `<option value="${i}">${i}</option>`;
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Failed to load institute data');
                });
        }
        
        function loadExamsForSelectedClasses() {
            const checkboxes = document.querySelectorAll('#class-checkbox-container input[type="checkbox"]:checked');
            const selectedClasses = Array.from(checkboxes).map(cb => cb.value);
            
            if(selectedClasses.length === 0) {
                document.getElementById('exam-selection-card').style.display = 'none';
                document.getElementById('top-n-selection-card').style.display = 'none';
                document.getElementById('top-students-data').style.display = 'none';
                return;
            }
            
            fetch('get_class_exams_with_marks.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `class_codes=${selectedClasses.join(',')}`
            })
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('exam-checkbox-container');
                container.innerHTML = '';
                
                if(data.length === 0) {
                    container.innerHTML = '<p style="text-align:center; color: var(--gray);">No exams with marks found for selected classes</p>';
                } else {
                    data.forEach(exam => {
                        container.innerHTML += `
                            <div class="checkbox-item">
                                <input type="checkbox" id="exam-${exam.id}" value="${exam.id}" onchange="checkExamSelection()">
                                <label for="exam-${exam.id}">${exam.exam_name} (${exam.code}) - ${exam.exam_date}</label>
                            </div>
                        `;
                    });
                }
                
                document.getElementById('exam-selection-card').style.display = 'block';
                document.getElementById('top-n-selection-card').style.display = 'none';
                document.getElementById('top-students-data').style.display = 'none';
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to load exams');
            });
        }
        
        function checkExamSelection() {
            const checkboxes = document.querySelectorAll('#exam-checkbox-container input[type="checkbox"]:checked');
            
            if(checkboxes.length > 0) {
                document.getElementById('top-n-selection-card').style.display = 'block';
            } else {
                document.getElementById('top-n-selection-card').style.display = 'none';
                document.getElementById('top-students-data').style.display = 'none';
            }
        }
        
        function loadTopNStudents() {
            const examCheckboxes = document.querySelectorAll('#exam-checkbox-container input[type="checkbox"]:checked');
            const selectedExams = Array.from(examCheckboxes).map(cb => cb.value);
            const topN = document.getElementById('top-n-select').value;
            
            if(selectedExams.length === 0) {
                alert('Please select at least one exam');
                return;
            }
            
            if(!topN) {
                alert('Please select number of top students');
                return;
            }
            
            fetch('get_top_n_students.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `exam_ids=${selectedExams.join(',')}&top_n=${topN}`
            })
            .then(res => res.json())
            .then(data => {
                topStudentsData = data;
                const tbody = document.getElementById('top-students-tbody');
                tbody.innerHTML = '';
                
                document.getElementById('top-students-title').textContent = `Top ${topN} Students`;
                
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No data found</td></tr>';
                } else {
                    data.forEach((student, index) => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${student.student_name}</td>
                                <td>${student.roll_no}</td>
                                <td>${student.class_code}</td>
                                <td>${student.total_marks}</td>
                                <td>${student.percentage}%</td>
                            </tr>
                        `;
                    });
                }
                
                document.getElementById('top-students-data').style.display = 'block';
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to load top students');
            });
        }



        function generateReport() {
            const topN = document.getElementById('top-n-select').value;
            
            fetch('get_header_logo.php')
                .then(res => res.json())
                .then(logoData => {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    
                    const pageWidth = doc.internal.pageSize.getWidth();
                    const pageHeight = doc.internal.pageSize.getHeight();
                    
                    let startY = 10;
                    
                    if(logoData.logo && logoData.logo !== null && logoData.logo !== '') {
                        const logoPath = '../' + logoData.logo;
                        const img = new Image();
                        img.crossOrigin = 'Anonymous';
                        img.onload = function() {
                            try {
                                doc.addImage(img, 'PNG', pageWidth / 2 - 20, 5, 40, 20);
                            } catch(e) {
                                console.log('Logo error:', e);
                            }
                            generatePDFContent();
                        };
                        img.onerror = function() {
                            generatePDFContent();
                        };
                        img.src = logoPath;
                        startY = 30;
                    } else {
                        generatePDFContent();
                    }
                    
                    function generatePDFContent() {
                        doc.setFillColor(67, 97, 238);
                        doc.rect(0, startY, pageWidth, 20, 'F');
                        
                        doc.setTextColor(255, 255, 255);
                        doc.setFontSize(18);
                        doc.setFont(undefined, 'bold');
                        doc.text('OUR TOPPERS', pageWidth / 2, startY + 13, { align: 'center' });
                        
                        doc.setTextColor(0, 0, 0);
                        doc.setFontSize(11);
                        doc.setFont(undefined, 'normal');
                        doc.text(`Top ${topN} Students - Accounts RESULT`, pageWidth / 2, startY + 28, { align: 'center' });
                        
                        const tableData = topStudentsData.map((student, index) => [
                            index + 1,
                            student.student_name,
                            student.roll_no,
                            student.class_code,
                            student.total_marks,
                            student.percentage + '%'
                        ]);
                        
                        const fontSize = topN <= 10 ? 10 : 8;
                        const cellPadding = topN <= 10 ? 5 : 3;
                        
                        doc.autoTable({
                            head: [['Rank', 'Student Name', 'Roll No', 'Class', 'Total Marks', 'Percentage']],
                            body: tableData,
                            startY: startY + 35,
                            theme: 'grid',
                            headStyles: {
                                fillColor: [67, 97, 238],
                                textColor: [255, 255, 255],
                                fontSize: fontSize,
                                fontStyle: 'bold',
                                halign: 'center'
                            },
                            bodyStyles: {
                                fontSize: fontSize,
                                cellPadding: cellPadding,
                                halign: 'center'
                            },
                            alternateRowStyles: {
                                fillColor: [240, 244, 255]
                            },
                            columnStyles: {
                                0: { cellWidth: 15 },
                                1: { cellWidth: 50, halign: 'left' },
                                2: { cellWidth: 25 },
                                3: { cellWidth: 25 },
                                4: { cellWidth: 30 },
                                5: { cellWidth: 30 }
                            },
                            margin: { left: 14, right: 14 },
                            didDrawPage: function(data) {
                                doc.setFontSize(8);
                                doc.setTextColor(100);
                                doc.text('www.yoursite.com', 14, pageHeight - 10);
                                doc.text('+91 9876543210', pageWidth - 40, pageHeight - 10);
                            }
                        });
                        
                        doc.save(`top_${topN}_students_report.pdf`);
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    const pageWidth = doc.internal.pageSize.getWidth();
                    const pageHeight = doc.internal.pageSize.getHeight();
                    
                    doc.setFillColor(67, 97, 238);
                    doc.rect(0, 10, pageWidth, 20, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(18);
                    doc.setFont(undefined, 'bold');
                    doc.text('OUR TOPPERS', pageWidth / 2, 20, { align: 'center' });
                    doc.setTextColor(0, 0, 0);
                    doc.setFontSize(11);
                    doc.setFont(undefined, 'normal');
                    doc.text(`Top ${topN} Students - Accounts RESULT`, pageWidth / 2, 38, { align: 'center' });
                    
                    const tableData = topStudentsData.map((student, index) => [
                        index + 1, student.student_name, student.roll_no, student.class_code, student.total_marks, student.percentage + '%'
                    ]);
                    const fontSize = topN <= 10 ? 10 : 8;
                    const cellPadding = topN <= 10 ? 5 : 3;
                    
                    doc.autoTable({
                        head: [['Rank', 'Student Name', 'Roll No', 'Class', 'Total Marks', 'Percentage']],
                        body: tableData,
                        startY: 45,
                        theme: 'grid',
                        headStyles: { fillColor: [67, 97, 238], textColor: [255, 255, 255], fontSize: fontSize, fontStyle: 'bold', halign: 'center' },
                        bodyStyles: { fontSize: fontSize, cellPadding: cellPadding, halign: 'center' },
                        alternateRowStyles: { fillColor: [240, 244, 255] },
                        columnStyles: { 0: { cellWidth: 15 }, 1: { cellWidth: 50, halign: 'left' }, 2: { cellWidth: 25 }, 3: { cellWidth: 25 }, 4: { cellWidth: 30 }, 5: { cellWidth: 30 } },
                        margin: { left: 14, right: 14 },
                        didDrawPage: function(data) {
                            doc.setFontSize(8);
                            doc.setTextColor(100);
                            doc.text('www.yoursite.com', 14, pageHeight - 10);
                            doc.text('+91 9876543210', pageWidth - 40, pageHeight - 10);
                        }
                    });
                    doc.save(`top_${topN}_students_report.pdf`);
                });
        }
        
        let performanceChart = null;
        
        function showPerformanceModal(examId, examName) {
            document.getElementById('modalExamTitle').textContent = examName + ' - Performance Analysis';
            document.getElementById('performanceModal').style.display = 'block';
            
            fetch('get_exam_performance.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `exam_id=${examId}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.error) {
                    alert(data.error);
                    closePerformanceModal();
                    return;
                }
                
                document.getElementById('highCount').textContent = data.high_count;
                document.getElementById('avgCount').textContent = data.average_count;
                document.getElementById('poorCount').textContent = data.poor_count;
                
                document.getElementById('highestMarks').textContent = data.highest_marks;
                document.getElementById('averageMarks').textContent = data.average_marks;
                document.getElementById('lowestMarks').textContent = data.lowest_marks;
                
                const totalStudents = data.high_count + data.average_count + data.poor_count;
                const poorPercentage = (data.poor_count / totalStudents) * 100;
                
                let recommendation = '';
                if(poorPercentage > 40) {
                    recommendation = 'More preparation is needed. A significant number of students are performing below expectations. Consider additional tutoring sessions, revision classes, and focused attention on weak areas.';
                } else if(poorPercentage > 20) {
                    recommendation = 'Moderate preparation needed. Some students require additional support. Identify struggling students and provide targeted assistance to improve their performance.';
                } else {
                    recommendation = 'Good performance overall! The majority of students are performing well. Continue with the current teaching methodology and provide minor support where needed.';
                }
                
                document.getElementById('recommendationText').textContent = recommendation;
                
                renderPerformanceChart(data.high_count, data.average_count, data.poor_count);
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to load performance data');
                closePerformanceModal();
            });
        }
        
        function closePerformanceModal() {
            document.getElementById('performanceModal').style.display = 'none';
            if(performanceChart) {
                performanceChart.destroy();
                performanceChart = null;
            }
        }
        
        function renderPerformanceChart(high, average, poor) {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            if(performanceChart) {
                performanceChart.destroy();
            }
            
            performanceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Highest Marks', 'Average Marks', 'Poor Marks'],
                    datasets: [{
                        data: [high, average, poor],
                        backgroundColor: [
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 13,
                                    weight: '600',
                                    family: 'Inter'
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
        
        let currentReportData = null;

        function loadStudentReportClasses() {
            fetch('get_classes.php')
                .then(res => res.json())
                .then(data => {
                    const progressSelect = document.getElementById('class_select_progress');
                    progressSelect.innerHTML = '<option value="">Choose a class</option>';
                    
                    data.forEach(cls => {
                        progressSelect.innerHTML += `<option value="${cls.code}">${cls.name} (${cls.code})</option>`;
                    });
                })
                .catch(err => console.error('Error:', err));
        }

        function loadExamMarksClasses() {
            fetch('get_classes.php')
                .then(res => res.json())
                .then(data => {
                    const marksSelect = document.getElementById('class_select_marks');
                    marksSelect.innerHTML = '<option value="">Choose a class</option>';
                    
                    data.forEach(cls => {
                        marksSelect.innerHTML += `<option value="${cls.code}">${cls.name} (${cls.code})</option>`;
                    });
                })
                .catch(err => console.error('Error:', err));
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Load classes for the default active tab (Individual Student Report)
            loadStudentReportClasses();
            
            const classSelectMarks = document.getElementById('class_select_marks');
            if(classSelectMarks) {
                classSelectMarks.addEventListener('change', function() {
                    const classCode = this.value;
                    const examSelect = document.getElementById('exam_select_marks');
                    examSelect.innerHTML = '<option value="">Choose an exam</option>';
                    document.getElementById('marks-results-container').innerHTML = '';
                    
                    if (classCode) {
                        fetch(`get_exams_by_class.php?class_code=${encodeURIComponent(classCode)}`)
                            .then(response => response.json())
                            .then(exams => {
                                exams.forEach(exam => {
                                    const option = document.createElement('option');
                                    option.value = exam.id;
                                    option.textContent = exam.exam_name;
                                    examSelect.appendChild(option);
                                });
                            });
                    }
                });
            }

            const classSelectProgress = document.getElementById('class_select_progress');
            if(classSelectProgress) {
                classSelectProgress.addEventListener('change', function() {
                    const classCode = this.value;
                    const studentSelect = document.getElementById('student_select_progress');
                    studentSelect.innerHTML = '<option value="">Choose a student</option>';
                    document.getElementById('progress-results-container').innerHTML = '';
                    
                    if (classCode) {
                        fetch(`get_students_by_class.php?class_code=${encodeURIComponent(classCode)}`)
                            .then(response => response.json())
                            .then(students => {
                                students.forEach(student => {
                                    const option = document.createElement('option');
                                    option.value = student.id;
                                    option.textContent = student.name;
                                    studentSelect.appendChild(option);
                                });
                            });
                    }
                });
            }
        });

        function generateMarksReport() {
            const classCode = document.getElementById('class_select_marks').value;
            const examId = document.getElementById('exam_select_marks').value;
            
            if (!classCode || !examId) {
                alert('Please select both class and exam');
                return;
            }
            
            fetch(`get_exam_marks.php?exam_id=${examId}`)
                .then(response => response.json())
                .then(marks => {
                    const container = document.getElementById('marks-results-container');
                    
                    if (marks.length > 0) {
                        let html = '<div class="data-section"><h3>Student Marks Report</h3><table class="report-table"><thead><tr><th>Rank</th><th>Student Name</th><th>Roll No</th><th>Marks Obtained</th><th>Total Marks</th><th>Percentage</th></tr></thead><tbody>';
                        
                        marks.forEach((row, index) => {
                            const rank = index + 1;
                            const percentage = row.total_marks > 0 ? ((row.actual_marks / row.total_marks) * 100).toFixed(2) : 0;
                            const rankBadge = rank <= 3 ? `<span class="rank-badge rank-${rank}">${rank}</span>` : rank;
                            
                            html += `<tr>
                                <td>${rankBadge}</td>
                                <td>${row.student_name}</td>
                                <td>${row.student_roll_no}</td>
                                <td>${row.actual_marks}</td>
                                <td>${row.total_marks}</td>
                                <td>${percentage}%</td>
                            </tr>`;
                        });
                        
                        html += '</tbody></table></div>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<div class="data-section"><p style="text-align:center; color: var(--gray); padding: 40px;">No marks data found.</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to fetch report data');
                });
        }

        function generateProgressReport() {
            const classCode = document.getElementById('class_select_progress').value;
            const studentRoll = document.getElementById('student_select_progress').value;
            const startDate = document.getElementById('start_date_progress').value;
            const endDate = document.getElementById('end_date_progress').value;
            
            if (!classCode || !studentRoll) {
                alert('Please select both class and student');
                return;
            }
            
            if (!startDate || !endDate) {
                alert('Please select both start date and end date');
                return;
            }
            
            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after end date');
                return;
            }
            
            fetch(`get_student_full_report.php?class_code=${encodeURIComponent(classCode)}&student_roll=${encodeURIComponent(studentRoll)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`)
                .then(response => response.json())
                .then(data => {
                    if(data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }
                    currentReportData = data;
                    displayProgressReport(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to fetch student report');
                });
        }

        function displayProgressReport(data) {
            const container = document.getElementById('progress-results-container');
            const attendancePercent = data.attendance.total_days > 0 ? 
                ((data.attendance.present_days / data.attendance.total_days) * 100).toFixed(1) : 0;
            
            let html = `
            <div class="data-section" id="report-content">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="color: var(--primary); margin-bottom: 10px;">${data.institute_name}</h2>
                    <h3 style="color: var(--dark); margin-bottom: 20px;">Student Progress Report</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; text-align: left; background: var(--primary-light); padding: 20px; border-radius: 12px;">
                        <div><strong>Name:</strong> ${data.student.student_name}</div>
                        <div><strong>Roll No:</strong> ${data.student.student_roll_no}</div>
                        <div><strong>Class:</strong> ${data.class_name}</div>
                        <div><strong>Email:</strong> ${data.student.email || 'N/A'}</div>
                    </div>
                </div>

                <div style="margin: 30px 0;">
                    <h3 style="margin-bottom: 15px;">Attendance Overview</h3>
                    <div style="max-width: 400px; margin: 0 auto;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                    <div style="text-align: center; margin-top: 15px; font-size: 1.1rem;">
                        <strong>Attendance: ${attendancePercent}%</strong> (${data.attendance.present_days}/${data.attendance.total_days} days)
                    </div>
                </div>

                <div style="margin: 30px 0;">
                    <h3 style="margin-bottom: 15px;">Exam-wise Marks</h3>
                    <canvas id="marksBarChart"></canvas>
                </div>

                <div style="margin: 30px 0;">
                    <h3 style="margin-bottom: 15px;">Performance Trend</h3>
                    <canvas id="performanceLineChart"></canvas>
                </div>

                <div style="margin: 30px 0;">
                    <h3 style="margin-bottom: 15px;">Class Standing</h3>
                    <div style="max-width: 400px; margin: 0 auto;">
                        <canvas id="standingChart"></canvas>
                    </div>
                    <div style="text-align: center; margin-top: 15px; font-size: 1.1rem;">
                        <strong>Rank: ${data.rank} out of ${data.total_students} students</strong>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button class="btn-primary" onclick="downloadProgressReport()"><i class="fas fa-download"></i> Download Report</button>
                </div>
            </div>
            `;
            
            container.innerHTML = html;
            
            setTimeout(() => {
                createAttendanceChart(data.attendance);
                createMarksBarChart(data.exams);
                createPerformanceLineChart(data.exams);
                createStandingChart(data.rank, data.total_students);
            }, 100);
        }

        function createAttendanceChart(attendance) {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{
                        data: [attendance.present_days, attendance.absent_days],
                        backgroundColor: ['#4361ee', '#ef476f']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        function createMarksBarChart(exams) {
            const ctx = document.getElementById('marksBarChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: exams.map(e => e.exam_name),
                    datasets: [{
                        label: 'Marks Obtained',
                        data: exams.map(e => e.actual_marks),
                        backgroundColor: '#4361ee'
                    }, {
                        label: 'Total Marks',
                        data: exams.map(e => e.total_marks),
                        backgroundColor: '#e0e7ff'
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        function createPerformanceLineChart(exams) {
            const ctx = document.getElementById('performanceLineChart').getContext('2d');
            const percentages = exams.map(e => ((e.actual_marks / e.total_marks) * 100).toFixed(2));
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: exams.map(e => e.exam_name),
                    datasets: [{
                        label: 'Performance (%)',
                        data: percentages,
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true, max: 100 } }
                }
            });
        }

        function createStandingChart(rank, total) {
            const ctx = document.getElementById('standingChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Your Position', 'Other Students'],
                    datasets: [{
                        data: [1, total - 1],
                        backgroundColor: ['#4361ee', '#e0e7ff']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        async function downloadProgressReport() {
            const content = document.getElementById('report-content');
            const canvas = await html2canvas(content, { scale: 2, useCORS: true, logging: false });
            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgWidth = 190;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let heightLeft = imgHeight;
            let position = 10;
            
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= 280;
            
            while (heightLeft > 0) {
                position = heightLeft - imgHeight + 10;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= 280;
            }
            
            pdf.save(`${currentReportData.student.student_name}_Progress_Report.pdf`);
        }

        window.onclick = function(event) {
            const modal = document.getElementById('performanceModal');
            if (event.target == modal) {
                closePerformanceModal();
            }
        }
    </script>
</body>
</html>
