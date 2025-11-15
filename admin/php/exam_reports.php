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
        <div class="logo">Acadexa</div>
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
            <h1 class="page-title">Exam Report</h1>
            <p class="page-subtitle">Student performance analysis with marks and grade distribution</p>
        </div>

        <div class="report-tabs">
            <button class="tab-btn active" onclick="switchTab('exam')">Exam Report</button>
            <button class="tab-btn" onclick="switchTab('classwise')">Classwise</button>
            <button class="tab-btn" onclick="switchTab('institute')">Institute wise</button>
        </div>

        <!-- Exam Report Tab -->
        <div id="exam-report" class="report-section">
            <div class="filter-card">
                <h3>Search Exam</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Exam Name</label>
                        <input type="text" id="exam-search" class="input-field" placeholder="Enter exam name">
                    </div>
                    <button class="btn-primary" onclick="searchExam()"><i class="fas fa-search"></i> Search</button>
                </div>
            </div>

            <div id="exam-data" class="data-section" style="display:none;">
                <div id="exam-details-container"></div>
            </div>
        </div>

        <!-- Classwise Tab -->
        <div id="classwise-report" class="report-section" style="display:none;">
            <div class="filter-card">
                <h3>Select Class</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Class</label>
                        <select id="class-select" class="input-field" onchange="loadClassExams()">
                            <option value="">Select Class</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="class-exams-container" style="display:none;">
                <div class="data-section">
                    <h3 style="margin-bottom: 20px;">Exams for Selected Class</h3>
                    <div id="class-exams-list"></div>
                </div>
            </div>

            <div id="class-exam-details" style="display:none;">
                <div class="data-section">
                    <div id="class-exam-details-container"></div>
                </div>
            </div>
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
    <script>
        let topStudentsData = [];

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('exam-report').style.display = 'none';
            document.getElementById('classwise-report').style.display = 'none';
            document.getElementById('institute-report').style.display = 'none';
            
            if(tab === 'exam') {
                document.getElementById('exam-report').style.display = 'block';
            } else if(tab === 'classwise') {
                document.getElementById('classwise-report').style.display = 'block';
                loadClasses();
            } else if(tab === 'institute') {
                document.getElementById('institute-report').style.display = 'block';
                loadInstituteData();
            }
        }

        function searchExam() {
            const examName = document.getElementById('exam-search').value.trim();
            
            if(!examName) {
                alert('Please enter exam name');
                return;
            }

            fetch('get_exam_details.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `exam_name=${encodeURIComponent(examName)}`
            })
            .then(res => {
                if(!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                displayExamDetails(data);
                document.getElementById('exam-data').style.display = 'block';
            })
            .catch(err => {
                console.error(err);
                alert('Error fetching exam details');
            });
        }

        function displayExamDetails(exams) {
            const container = document.getElementById('exam-details-container');
            container.innerHTML = '';
            
            if(!Array.isArray(exams) || exams.length === 0) {
                container.innerHTML = '<p style="text-align:center; color: var(--gray);">No exams found</p>';
                return;
            }
            
            container.innerHTML = '<h3 style="margin-bottom: 20px;">Search Results</h3>';
            exams.forEach(exam => {
                const viewButton = exam.has_marks ? 
                    `<button class="btn-view" onclick="showPerformanceModal(${exam.id}, '${exam.exam_name}')">View</button>` : '';
                
                container.innerHTML += `
                    <div class="exam-card">
                        <h4>${exam.exam_name}</h4>
                        <p><i class="fas fa-school"></i> Class: ${exam.class_name} (${exam.code})</p>
                        <p><i class="fas fa-calendar"></i> Date: ${exam.exam_date}</p>
                        <p><i class="fas fa-clock"></i> Time: ${exam.start_time} - ${exam.end_time}</p>
                        <p><i class="fas fa-star"></i> Total Marks: ${exam.total_marks} | Passing: ${exam.passing_marks}</p>
                        ${viewButton}
                    </div>
                `;
            });
        }

        function loadClasses() {
            fetch('get_classes.php')
                .then(res => {
                    if(!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    const select = document.getElementById('class-select');
                    select.innerHTML = '<option value="">Select Class</option>';
                    data.forEach(cls => {
                        select.innerHTML += `<option value="${cls.code}">${cls.name} (${cls.code})</option>`;
                    });
                })
                .catch(err => {
                    console.error('Error:', err);
                });
        }

        function loadClassExams() {
            const classCode = document.getElementById('class-select').value;
            
            if(!classCode) {
                document.getElementById('class-exams-container').style.display = 'none';
                return;
            }

            fetch('get_class_exams.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `class_code=${classCode}`
            })
            .then(res => {
                if(!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                const container = document.getElementById('class-exams-list');
                container.innerHTML = '';
                
                if(data.length === 0) {
                    container.innerHTML = '<p style="text-align:center; color: var(--gray);">No exams found for this class</p>';
                } else {
                    data.forEach(exam => {
                        const viewButton = exam.has_marks ? 
                            `<button class="btn-view" onclick="showPerformanceModal(${exam.id}, '${exam.exam_name}')">View</button>` : '';
                        
                        container.innerHTML += `
                            <div class="exam-card">
                                <h4>${exam.exam_name}</h4>
                                <p><i class="fas fa-calendar"></i> Date: ${exam.exam_date}</p>
                                <p><i class="fas fa-clock"></i> Time: ${exam.start_time} - ${exam.end_time}</p>
                                <p><i class="fas fa-star"></i> Total Marks: ${exam.total_marks} | Passing: ${exam.passing_marks}</p>
                                ${viewButton}
                            </div>
                        `;
                    });
                }
                
                document.getElementById('class-exams-container').style.display = 'block';
                document.getElementById('class-exam-details').style.display = 'none';
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to load exams');
            });
        }

        function showClassExamDetails(examId) {
            fetch('get_exam_details.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `exam_id=${examId}`
            })
            .then(res => {
                if(!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                if(data.error) {
                    alert(data.error);
                    return;
                }
                
                const container = document.getElementById('class-exam-details-container');
                container.innerHTML = `
                    <div class="exam-details">
                        <h3>${data.exam_name}</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Class</span>
                                <span class="detail-value">${data.class_name} (${data.code})</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Exam Date</span>
                                <span class="detail-value">${data.exam_date}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Start Time</span>
                                <span class="detail-value">${data.start_time}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">End Time</span>
                                <span class="detail-value">${data.end_time}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Total Marks</span>
                                <span class="detail-value">${data.total_marks}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Passing Marks</span>
                                <span class="detail-value">${data.passing_marks}</span>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('class-exam-details').style.display = 'block';
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Failed to load exam details');
            });
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
        
        window.onclick = function(event) {
            const modal = document.getElementById('performanceModal');
            if (event.target == modal) {
                closePerformanceModal();
            }
        }
    </script>
</body>
</html>
