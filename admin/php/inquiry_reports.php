<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Report - Acadexa</title>
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
        
        .interest-high {
            display: inline-block;
            padding: 5px 12px;
            background: #d4edda;
            color: #155724;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .interest-medium {
            display: inline-block;
            padding: 5px 12px;
            background: #fff3cd;
            color: #856404;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .interest-low {
            display: inline-block;
            padding: 5px 12px;
            background: #f8d7da;
            color: #721c24;
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
            <h1 class="page-title">Inquiry Report</h1>
            <p class="page-subtitle">Complete inquiry report with follow-up details and interest levels</p>
        </div>

        <div class="filter-card">
            <h3>Select Date Range</h3>
            <div class="filter-row">
                <div class="input-group">
                    <label>From Date</label>
                    <input type="date" id="from-date" class="input-field">
                </div>
                <div class="input-group">
                    <label>To Date</label>
                    <input type="date" id="to-date" class="input-field">
                </div>
                <button class="btn-primary" onclick="loadInquiryReport()">Generate Report</button>
            </div>
        </div>

        <div id="inquiry-data" class="data-section" style="display:none;">
            <div class="table-container">
                <table id="inquiry-table" class="report-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Mobile</th>
                            <th>Father Mobile</th>
                            <th>School</th>
                            <th>Class</th>
                            <th>Medium</th>
                            <th>Group</th>
                            <th>Reference</th>
                            <th>Interest Level</th>
                            <th>Follow-up Date</th>
                            <th>Follow-up Time</th>
                            <th>Notes</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody id="inquiry-tbody"></tbody>
                </table>
            </div>
            <div class="download-actions">
                <button class="btn-download" onclick="downloadPDF()"><i class="fas fa-file-pdf"></i> Download PDF</button>
                <button class="btn-download" onclick="downloadCSV()"><i class="fas fa-file-csv"></i> Download CSV</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script>
        let inquiryData = [];

        function loadInquiryReport() {
            const fromDate = document.getElementById('from-date').value;
            const toDate = document.getElementById('to-date').value;
            
            if(!fromDate || !toDate) {
                alert('Please select both dates');
                return;
            }

            fetch('get_inquiry_report.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `from_date=${fromDate}&to_date=${toDate}`
            })
            .then(res => res.json())
            .then(data => {
                inquiryData = data;
                const tbody = document.getElementById('inquiry-tbody');
                tbody.innerHTML = '';
                
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="13" style="text-align:center;">No records found</td></tr>';
                } else {
                    data.forEach(row => {
                        const interestClass = row.interest_level === 'high' ? 'interest-high' : 
                                            row.interest_level === 'medium' ? 'interest-medium' : 'interest-low';
                        tbody.innerHTML += `
                            <tr>
                                <td>${row.student_name}</td>
                                <td>${row.student_mobile}</td>
                                <td>${row.father_mobile || '-'}</td>
                                <td>${row.school_name || '-'}</td>
                                <td>${row.std}</td>
                                <td>${row.medium}</td>
                                <td>${row.group_name || '-'}</td>
                                <td>${row.reference_by || '-'}</td>
                                <td><span class="${interestClass}">${row.interest_level}</span></td>
                                <td>${row.followup_date || '-'}</td>
                                <td>${row.followup_time || '-'}</td>
                                <td>${row.notes || '-'}</td>
                                <td>${new Date(row.created_at).toLocaleDateString('en-GB')}</td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('inquiry-data').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load inquiry report');
            });
        }

        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            
            doc.setFontSize(18);
            doc.text('Inquiry Report', 14, 20);
            
            const tableData = inquiryData.map(row => [
                row.student_name,
                row.student_mobile,
                row.father_mobile || '-',
                row.school_name || '-',
                row.std,
                row.medium,
                row.group_name || '-',
                row.reference_by || '-',
                row.interest_level,
                row.followup_date || '-',
                row.followup_time || '-',
                row.notes || '-',
                new Date(row.created_at).toLocaleDateString('en-GB')
            ]);
            
            doc.autoTable({
                head: [['Student', 'Mobile', 'Father Mobile', 'School', 'Class', 'Medium', 'Group', 'Reference', 'Interest', 'Follow-up Date', 'Follow-up Time', 'Notes', 'Created']],
                body: tableData,
                startY: 30,
                styles: { fontSize: 7 }
            });
            
            doc.save('inquiry_report.pdf');
        }

        function downloadCSV() {
            let csv = 'Student Name,Mobile,Father Mobile,School,Class,Medium,Group,Reference,Interest Level,Follow-up Date,Follow-up Time,Notes,Created At\n';
            inquiryData.forEach(row => {
                csv += `"${row.student_name}","${row.student_mobile}","${row.father_mobile || '-'}","${row.school_name || '-'}","${row.std}","${row.medium}","${row.group_name || '-'}","${row.reference_by || '-'}","${row.interest_level}","${row.followup_date || '-'}","${row.followup_time || '-'}","${row.notes || '-'}","${new Date(row.created_at).toLocaleDateString('en-GB')}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'inquiry_report.csv';
            a.click();
        }
    </script>
</body>
</html>
