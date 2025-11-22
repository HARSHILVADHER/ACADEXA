<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Report - Acadexa</title>
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
        
        .status-paid {
            display: inline-block;
            padding: 5px 12px;
            background: #d4edda;
            color: #155724;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-pending {
            display: inline-block;
            padding: 5px 12px;
            background: #fff3cd;
            color: #856404;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
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

        .total-row {
            background: linear-gradient(135deg, var(--primary-light), #f0f4ff) !important;
            font-weight: 700;
            font-size: 1.05rem;
        }

        .total-row td {
            border-top: 3px solid var(--primary);
            border-bottom: 3px solid var(--primary);
        }

        .btn-institute {
            padding: 12px 25px;
            background: linear-gradient(135deg, #f72585, #b5179e);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(247, 37, 133, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-institute:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(247, 37, 133, 0.4);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .stat-box {
            background: linear-gradient(135deg, var(--primary-light), #f0f4ff);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid var(--primary);
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

        .chart-container {
            margin-top: 20px;
            text-align: center;
            padding: 20px;
        }

        .class-chart-section {
            background: var(--white);
            padding: 20px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }

        .class-chart-section h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 15px;
            text-align: center;
        }

        .class-chart-section .stats-grid {
            margin-bottom: 15px;
        }

        .class-chart-section .stat-box {
            padding: 15px;
        }

        .class-chart-section .stat-box h3 {
            font-size: 0.85rem;
            margin-bottom: 8px;
        }

        .class-chart-section .stat-box p {
            font-size: 1.5rem;
        }

        .class-chart-section .chart-container {
            padding: 10px;
            margin-top: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--white);
            margin: 3% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            padding: 25px 30px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .close {
            color: var(--white);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }

        .close:hover {
            transform: scale(1.2);
        }

        .modal-body {
            padding: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: linear-gradient(135deg, var(--primary-light), #f0f4ff);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid var(--primary);
        }

        .stat-box h3 {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .stat-box p {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
        }

        .chart-container {
            margin-top: 20px;
            text-align: center;
        }

        canvas {
            max-width: 100%;
            height: auto;
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
            
            .download-actions {
                flex-direction: column;
            }
            
            .btn-download {
                width: 100%;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 95%;
                margin: 10% auto;
            }

            .report-tabs {
                flex-wrap: wrap;
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
            <h1 class="page-title">Fees Report</h1>
            <p class="page-subtitle">Complete fees collection report with pending and paid installments</p>
        </div>

        <div class="report-tabs">
            <button class="tab-btn active" onclick="switchTab('student')">Student-wise Report</button>
            <button class="tab-btn" onclick="switchTab('class')">Class-wise Report</button>
            <button class="tab-btn" onclick="switchTab('institute')">Institute-wise Report</button>
        </div>

        <!-- Student-wise Report -->
        <div id="student-report" class="report-section">
            <div class="filter-card">
                <h3>Select Date Range</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>From Date</label>
                        <input type="date" id="student-from-date" class="input-field">
                    </div>
                    <div class="input-group">
                        <label>To Date</label>
                        <input type="date" id="student-to-date" class="input-field">
                    </div>
                    <button class="btn-primary" onclick="loadStudentReport()">Generate Report</button>
                </div>
            </div>

            <div id="student-data" class="data-section" style="display:none;">
                <div class="table-container">
                    <table id="student-table" class="report-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Roll No</th>
                                <th>Class</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Paid Date</th>
                                <th>Payment Mode</th>
                            </tr>
                        </thead>
                        <tbody id="student-tbody"></tbody>
                    </table>
                </div>
                <div class="download-actions">
                    <button class="btn-download" onclick="downloadStudentPDF()"><i class="fas fa-file-pdf"></i> Download PDF</button>
                    <button class="btn-download" onclick="downloadStudentCSV()"><i class="fas fa-file-csv"></i> Download CSV</button>
                </div>
            </div>
        </div>

        <!-- Class-wise Report -->
        <div id="class-report" class="report-section" style="display:none;">
            <div class="filter-card">
                <h3>Select Class</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Class</label>
                        <select id="class-select" class="input-field">
                            <option value="">Select Class</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Status</label>
                        <select id="status-filter" class="input-field">
                            <option value="all">All</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <button class="btn-primary" onclick="loadClassReport()">Generate Report</button>
                </div>
            </div>

            <div id="class-chart-section" class="class-chart-section" style="display:none;">
                <h3>Fees Overview</h3>
                <div class="stats-grid">
                    <div class="stat-box">
                        <h3>Total Decided</h3>
                        <p id="classDecided">₹0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Total Received</h3>
                        <p id="classReceived">₹0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Total Pending</h3>
                        <p id="classPending">₹0</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="classFeesChart" style="max-height: 250px;"></canvas>
                </div>
            </div>

            <div id="class-data" class="data-section" style="display:none;">
                <div class="table-container">
                    <table id="class-table" class="report-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Roll No</th>
                                <th>Installment</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Paid Date</th>
                                <th>Payment Mode</th>
                            </tr>
                        </thead>
                        <tbody id="class-tbody"></tbody>
                    </table>
                </div>
                <div class="download-actions">
                    <button class="btn-download" onclick="downloadClassPDF()"><i class="fas fa-file-pdf"></i> Download PDF</button>
                    <button class="btn-download" onclick="downloadClassCSV()"><i class="fas fa-file-csv"></i> Download CSV</button>
                </div>
            </div>
        </div>

        <!-- Institute-wise Report -->
        <div id="institute-report" class="report-section" style="display:none;">
            <div id="institute-data" class="data-section">
                <div class="stats-grid" style="margin-bottom: 30px;">
                    <div class="stat-box">
                        <h3>Total Decided Fees</h3>
                        <p id="totalDecided">₹0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Total Paid</h3>
                        <p id="totalPaid">₹0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Total Pending</h3>
                        <p id="totalPending">₹0</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="feesChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Institute Total Modal -->
    <div id="instituteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-university"></i> Institute Fees Overview</h2>
                <span class="close" onclick="closeInstituteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="stats-grid">
                    <div class="stat-box">
                        <h3>Total Decided Fees</h3>
                        <p id="totalDecided">₹0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Total Paid</h3>
                        <p id="totalPaid">₹0</p>
                    </div>
                    <div class="stat-box">
                        <h3>Total Pending</h3>
                        <p id="totalPending">₹0</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="feesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script>
        let studentData = [];
        let classData = [];
        let classFeesChart = null;

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('student-report').style.display = 'none';
            document.getElementById('class-report').style.display = 'none';
            document.getElementById('institute-report').style.display = 'none';
            
            if(tab === 'student') {
                document.getElementById('student-report').style.display = 'block';
            } else if(tab === 'class') {
                document.getElementById('class-report').style.display = 'block';
                loadClasses();
            } else if(tab === 'institute') {
                document.getElementById('institute-report').style.display = 'block';
                loadInstituteReport();
            }
        }

        function loadClasses() {
            fetch('get_classes.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('class-select');
                    select.innerHTML = '<option value="">Select Class</option>';
                    data.forEach(cls => {
                        select.innerHTML += `<option value="${cls.code}">${cls.name} (${cls.code})</option>`;
                    });
                });
        }

        function loadStudentReport() {
            const fromDate = document.getElementById('student-from-date').value;
            const toDate = document.getElementById('student-to-date').value;
            
            if(!fromDate || !toDate) {
                alert('Please select both dates');
                return;
            }

            fetch('get_student_fees_report.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `from_date=${fromDate}&to_date=${toDate}`
            })
            .then(res => res.json())
            .then(data => {
                studentData = data;
                const tbody = document.getElementById('student-tbody');
                tbody.innerHTML = '';
                
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No records found</td></tr>';
                } else {
                    data.forEach(row => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${row.student_name}</td>
                                <td>${row.student_roll_no}</td>
                                <td>${row.class_code}</td>
                                <td>₹${parseFloat(row.amount).toFixed(2)}</td>
                                <td>${row.due_date}</td>
                                <td>${row.paid_date}</td>
                                <td>${row.payment_mode}</td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('student-data').style.display = 'block';
            });
        }

        function loadClassReport() {
            const classCode = document.getElementById('class-select').value;
            const statusFilter = document.getElementById('status-filter').value;
            
            if(!classCode) {
                alert('Please select a class');
                return;
            }

            fetch('get_class_fees_report.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `class_code=${classCode}&status=${statusFilter}`
            })
            .then(res => res.json())
            .then(data => {
                classData = data;
                const tbody = document.getElementById('class-tbody');
                tbody.innerHTML = '';
                
                let totalDecided = 0;
                let totalReceived = 0;
                let totalPending = 0;
                
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No records found</td></tr>';
                    document.getElementById('class-chart-section').style.display = 'none';
                } else {
                    data.forEach(row => {
                        const statusClass = row.status === 'Paid' ? 'status-paid' : 'status-pending';
                        const amount = parseFloat(row.amount);
                        totalDecided += amount;
                        
                        if(row.status === 'Paid') {
                            totalReceived += amount;
                        } else {
                            totalPending += amount;
                        }
                        
                        tbody.innerHTML += `
                            <tr>
                                <td>${row.student_name}</td>
                                <td>${row.student_roll_no}</td>
                                <td>${row.installment_no}</td>
                                <td>₹${amount.toFixed(2)}</td>
                                <td>${row.due_date}</td>
                                <td><span class="${statusClass}">${row.status}</span></td>
                                <td>${row.paid_date || '-'}</td>
                                <td>${row.payment_mode || '-'}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML += `
                        <tr class="total-row">
                            <td colspan="3">Total:</td>
                            <td>₹${totalDecided.toFixed(2)}</td>
                            <td colspan="4"></td>
                        </tr>
                    `;
                    
                    // Update stats
                    document.getElementById('classDecided').textContent = '₹' + totalDecided.toLocaleString('en-IN', {minimumFractionDigits: 2});
                    document.getElementById('classReceived').textContent = '₹' + totalReceived.toLocaleString('en-IN', {minimumFractionDigits: 2});
                    document.getElementById('classPending').textContent = '₹' + totalPending.toLocaleString('en-IN', {minimumFractionDigits: 2});
                    
                    // Create pie chart
                    if(classFeesChart) classFeesChart.destroy();
                    
                    const ctx = document.getElementById('classFeesChart').getContext('2d');
                    classFeesChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Decided', 'Received', 'Pending'],
                            datasets: [{
                                data: [totalDecided, totalReceived, totalPending],
                                backgroundColor: ['#4361ee', '#28a745', '#ffc107'],
                                borderWidth: 3,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 12,
                                        font: { size: 12, weight: 'bold' }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ₹' + parseFloat(context.parsed).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                        }
                                    }
                                }
                            }
                        }
                    });
                    
                    document.getElementById('class-chart-section').style.display = 'block';
                }
                document.getElementById('class-data').style.display = 'block';
            });
        }

        function downloadStudentPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l');
            
            doc.setFontSize(18);
            doc.text('Student-wise Fees Report', 14, 20);
            
            const tableData = studentData.map(row => [
                row.student_name,
                row.student_roll_no,
                row.class_code,
                'Rs ' + Number(row.amount).toFixed(2),
                row.due_date,
                row.paid_date,
                row.payment_mode
            ]);
            
            doc.autoTable({
                head: [['Student Name', 'Roll No', 'Class', 'Amount', 'Due Date', 'Paid Date', 'Payment Mode']],
                body: tableData,
                startY: 30,
                styles: { fontSize: 9 }
            });
            
            doc.save('student_fees_report.pdf');
        }

        function downloadStudentCSV() {
            let csv = 'Student Name,Roll No,Class,Amount,Due Date,Paid Date,Payment Mode\n';
            studentData.forEach(row => {
                csv += `${row.student_name},${row.student_roll_no},${row.class_code},${row.amount},${row.due_date},${row.paid_date},${row.payment_mode}\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'student_fees_report.csv';
            a.click();
        }

        function downloadClassPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l');
            
            doc.setFontSize(18);
            doc.text('Class-wise Fees Report', 14, 20);
            
            const tableData = classData.map(row => [
                row.student_name,
                row.student_roll_no,
                String(row.installment_no),
                'Rs ' + Number(row.amount).toFixed(2),
                row.due_date,
                row.status,
                row.paid_date || '-',
                row.payment_mode || '-'
            ]);
            
            doc.autoTable({
                head: [['Student Name', 'Roll No', 'Installment', 'Amount', 'Due Date', 'Status', 'Paid Date', 'Payment Mode']],
                body: tableData,
                startY: 30,
                styles: { fontSize: 9 }
            });
            
            doc.save('class_fees_report.pdf');
        }

        function downloadClassCSV() {
            let csv = 'Student Name,Roll No,Installment,Amount,Due Date,Status,Paid Date,Payment Mode\n';
            classData.forEach(row => {
                csv += `${row.student_name},${row.student_roll_no},${row.installment_no},${row.amount},${row.due_date},${row.status},${row.paid_date || '-'},${row.payment_mode || '-'}\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'class_fees_report.csv';
            a.click();
        }

        let feesChart = null;

        function loadInstituteReport() {
            fetch('get_institute_total.php')
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.text();
                })
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON:', text);
                        throw new Error('Invalid JSON response');
                    }
                })
                .then(data => {
                    const decided = parseFloat(data.decided) || 0;
                    const paid = parseFloat(data.paid) || 0;
                    const pending = parseFloat(data.pending) || 0;
                    
                    document.getElementById('totalDecided').textContent = '₹' + decided.toLocaleString('en-IN', {minimumFractionDigits: 2});
                    document.getElementById('totalPaid').textContent = '₹' + paid.toLocaleString('en-IN', {minimumFractionDigits: 2});
                    document.getElementById('totalPending').textContent = '₹' + pending.toLocaleString('en-IN', {minimumFractionDigits: 2});
                    
                    if(feesChart) feesChart.destroy();
                    
                    const ctx = document.getElementById('feesChart').getContext('2d');
                    feesChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Decided Fees', 'Paid', 'Pending'],
                            datasets: [{
                                data: [decided, paid, pending],
                                backgroundColor: ['#4361ee', '#28a745', '#ffc107'],
                                borderWidth: 3,
                                borderColor: '#fff'
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
                                        font: { size: 14, weight: 'bold' }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ₹' + parseFloat(context.parsed).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                        }
                                    }
                                }
                            }
                        }
                    });
                    
                    document.getElementById('institute-data').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error loading institute data:', error);
                    alert('Failed to load institute data. Please check console for details.');
                });
        }

        function openInstituteModal() {
            loadInstituteReport();
            document.getElementById('instituteModal').style.display = 'block';
        }

        function closeInstituteModal() {
            document.getElementById('instituteModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('instituteModal');
            if (event.target == modal) {
                closeInstituteModal();
            }
        }
    </script>
</body>
</html>
