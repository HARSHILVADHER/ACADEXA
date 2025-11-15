<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch classes for the logged-in user
$classes = [];
$stmt = $conn->prepare("SELECT code, name FROM classes WHERE user_id = ? ORDER BY name");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reports | Acadexa</title>
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
        
        .results-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-top: 30px;
        }
        
        .results-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .results-table thead {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
        }
        
        .results-table th,
        .results-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .results-table th {
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .results-table tbody tr {
            transition: var(--transition);
        }
        
        .results-table tbody tr:hover {
            background: var(--primary-light);
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
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: var(--gray);
            font-size: 1.1rem;
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
            
            .results-table {
                font-size: 0.85rem;
            }
            
            .results-table th,
            .results-table td {
                padding: 10px;
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
            <a href="reports.php" class="active">Reports</a>
            <a href="inquiry.php">Inquiries</a>
            <a href="profile.php">Profile</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Student Reports</h1>
            <p class="page-subtitle">Download student marks and progress reports</p>
        </div>

        <div class="report-tabs">
            <button class="tab-btn active" onclick="switchTab('marks')">Student Marks Report</button>
            <button class="tab-btn" onclick="switchTab('progress')">Individual Progress Report</button>
        </div>

        <!-- Student Marks Report -->
        <div id="marks-report" class="report-section">
            <div class="filter-card">
                <h3>Select Filters</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Class</label>
                        <select id="class_select_marks" class="input-field" required>
                            <option value="">Choose a class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['code']); ?>">
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </option>
                            <?php endforeach; ?>
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

        <!-- Individual Progress Report -->
        <div id="progress-report" class="report-section" style="display:none;">
            <div class="filter-card">
                <h3>Select Student</h3>
                <div class="filter-row">
                    <div class="input-group">
                        <label>Class</label>
                        <select id="class_select_progress" class="input-field" required>
                            <option value="">Choose a class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['code']); ?>">
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label>Student</label>
                        <select id="student_select_progress" class="input-field" required>
                            <option value="">Choose a student</option>
                        </select>
                    </div>
                    
                    <button type="button" class="btn-primary" onclick="generateProgressReport()">Generate Report</button>
                </div>
            </div>
            
            <div id="progress-results-container"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        let currentReportData = null;

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            if(tab === 'marks') {
                event.target.classList.add('active');
                document.getElementById('marks-report').style.display = 'block';
                document.getElementById('progress-report').style.display = 'none';
            } else {
                event.target.classList.add('active');
                document.getElementById('marks-report').style.display = 'none';
                document.getElementById('progress-report').style.display = 'block';
            }
        }

        // Load exams for Student Marks Report
        document.getElementById('class_select_marks').addEventListener('change', function() {
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
                        let html = '<div class="results-card"><h3>Student Marks Report</h3><table class="results-table"><thead><tr><th>Rank</th><th>Student Name</th><th>Roll No</th><th>Marks Obtained</th><th>Total Marks</th><th>Percentage</th></tr></thead><tbody>';
                        
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
                        container.innerHTML = '<div class="results-card"><div class="no-data"><i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i><p>No marks data found.</p></div></div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to fetch report data');
                });
        }

        // Load students for Individual Progress Report
        document.getElementById('class_select_progress').addEventListener('change', function() {
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

        function generateProgressReport() {
            const classCode = document.getElementById('class_select_progress').value;
            const studentRoll = document.getElementById('student_select_progress').value;
            
            if (!classCode || !studentRoll) {
                alert('Please select both class and student');
                return;
            }
            
            console.log('Fetching report for:', classCode, studentRoll);
            
            fetch(`get_student_full_report.php?class_code=${encodeURIComponent(classCode)}&student_roll=${encodeURIComponent(studentRoll)}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('Response text:', text);
                    const data = JSON.parse(text);
                    if(data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }
                    currentReportData = data;
                    displayProgressReport(data);
                })
                .catch(error => {
                    console.error('Error details:', error);
                    alert('Failed to fetch student report. Check console for details.');
                });
        }

        function displayProgressReport(data) {
            const container = document.getElementById('progress-results-container');
            const attendancePercent = data.attendance.total_days > 0 ? 
                ((data.attendance.present_days / data.attendance.total_days) * 100).toFixed(1) : 0;
            
            let html = `
            <div class="results-card" id="report-content">
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
                    <button class="btn-primary" onclick="downloadReport()"><i class="fas fa-download"></i> Download Report</button>
                </div>
            </div>
            `;
            
            container.innerHTML = html;
            
            // Create charts
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
                    plugins: {
                        legend: { position: 'bottom' }
                    }
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
                    scales: {
                        y: { beginAtZero: true }
                    }
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
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
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
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        async function downloadReport() {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const content = document.getElementById('report-content');
            
            const canvas = await html2canvas(content, {
                scale: 2,
                useCORS: true,
                logging: false
            });
            
            const imgData = canvas.toDataURL('image/png');
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
    </script>
</body>
</html>
