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
            cursor: pointer;
            transition: var(--transition);
        }
        
        .exam-card:hover {
            border-color: var(--primary);
            transform: translateX(5px);
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
                <h3>Top 20 Students</h3>
                <p style="color: var(--gray); margin-bottom: 20px;">Select classes to view top performing students</p>
                <div id="class-checkbox-container" class="class-checkbox-list"></div>
                <button class="btn-primary" onclick="loadTopStudents()"><i class="fas fa-trophy"></i> Show Top 20 Students</button>
            </div>

            <div id="top-students-data" class="data-section" style="display:none;">
                <h3 style="margin-bottom: 20px;">Top 20 Students</h3>
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
                    <button class="btn-download" onclick="downloadTopStudentsPDF()"><i class="fas fa-file-pdf"></i> Download PDF</button>
                    <button class="btn-download" onclick="downloadTopStudentsCSV()"><i class="fas fa-file-csv"></i> Download CSV</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
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
                if(data.error) {
                    alert(data.error);
                    return;
                }
                
                displayExamDetails(data);
                document.getElementById('exam-data').style.display = 'block';
            })
            .catch(err => {
                console.error(err);
                alert('Error fetching exam details');
            });
        }

        function displayExamDetails(exam) {
            const container = document.getElementById('exam-details-container');
            container.innerHTML = `
                <div class="exam-details">
                    <h3>${exam.exam_name}</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Class</span>
                            <span class="detail-value">${exam.class_name} (${exam.code})</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Exam Date</span>
                            <span class="detail-value">${exam.exam_date}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Start Time</span>
                            <span class="detail-value">${exam.start_time}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">End Time</span>
                            <span class="detail-value">${exam.end_time}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total Marks</span>
                            <span class="detail-value">${exam.total_marks}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Passing Marks</span>
                            <span class="detail-value">${exam.passing_marks}</span>
                        </div>
                    </div>
                </div>
            `;
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
                        container.innerHTML += `
                            <div class="exam-card" onclick="showClassExamDetails(${exam.id})">
                                <h4>${exam.exam_name}</h4>
                                <p><i class="fas fa-calendar"></i> Date: ${exam.exam_date}</p>
                                <p><i class="fas fa-clock"></i> Time: ${exam.start_time} - ${exam.end_time}</p>
                                <p><i class="fas fa-star"></i> Total Marks: ${exam.total_marks} | Passing: ${exam.passing_marks}</p>
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
                                <input type="checkbox" id="class-${cls.code}" value="${cls.code}">
                                <label for="class-${cls.code}">${cls.name} (${cls.code})</label>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Failed to load institute data');
                });
        }

        function loadTopStudents() {
            const checkboxes = document.querySelectorAll('#class-checkbox-container input[type="checkbox"]:checked');
            const selectedClasses = Array.from(checkboxes).map(cb => cb.value);
            
            if(selectedClasses.length === 0) {
                alert('Please select at least one class');
                return;
            }

            fetch('get_top_students.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `classes=${selectedClasses.join(',')}`
            })
            .then(res => res.json())
            .then(data => {
                topStudentsData = data;
                const tbody = document.getElementById('top-students-tbody');
                tbody.innerHTML = '';
                
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
            });
        }

        function downloadTopStudentsPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.setFontSize(18);
            doc.text('Top 20 Students Report', 14, 20);
            
            const tableData = topStudentsData.map((student, index) => [
                index + 1,
                student.student_name,
                student.roll_no,
                student.class_code,
                student.total_marks,
                student.percentage + '%'
            ]);
            
            doc.autoTable({
                head: [['Rank', 'Student Name', 'Roll No', 'Class', 'Total Marks', 'Percentage']],
                body: tableData,
                startY: 30
            });
            
            doc.save('top_20_students.pdf');
        }

        function downloadTopStudentsCSV() {
            let csv = 'Rank,Student Name,Roll No,Class,Total Marks,Percentage\n';
            topStudentsData.forEach((student, index) => {
                csv += `${index + 1},${student.student_name},${student.roll_no},${student.class_code},${student.total_marks},${student.percentage}%\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'top_20_students.csv';
            a.click();
        }
    </script>
</body>
</html>
