<?php
// Handle AJAX request for student info
if (isset($_GET['ajax']) && $_GET['ajax'] === 'studentinfo' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    require_once 'config.php'; // This sets up $conn

    if ($conn->connect_error) { echo json_encode([]); exit; }
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM students WHERE id='$id' LIMIT 1";
    $res = $conn->query($sql);
    $student = $res && $res->num_rows ? $res->fetch_assoc() : [];
    echo json_encode([
        "name" => $student['name'] ?? '',
        "std" => $student['grade'] ?? '',
        "class" => $student['class_code'] ?? '', // or whatever your class column is called
        "email" => $student['email'] ?? ''
    ]);
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Card | Acadexa</title>
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
      background-clip: text;
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
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .card {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #e2e8f0;
    }

    .card-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--primary);
    }

    .btn {
      padding: 0.5rem 1.25rem;
      border-radius: var(--border-radius);
      border: none;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--secondary);
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background: var(--primary-light);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--dark);
    }

    .form-select, .form-input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #cbd5e1;
      border-radius: var(--border-radius);
      font-size: 1rem;
      transition: var(--transition);
    }

    .form-select:focus, .form-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .student-list {
      margin-top: 1.5rem;
    }

    .student-item {
      padding: 1rem;
      border-radius: var(--border-radius);
      background: white;
      margin-bottom: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: var(--transition);
      border: 1px solid #e2e8f0;
    }

    .student-item:hover {
      border-color: var(--primary);
    }

    .student-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .student-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }

    .student-name {
      font-weight: 500;
    }

    .student-grade {
      color: var(--gray);
      font-size: 0.875rem;
    }

    /* Report Container Styles */
    #reportContainer {
      display: none;
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 1.5rem;
      margin-top: 2rem;
    }

    .student-details {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid #e2e8f0;
    }

    .detail-item label {
      display: block;
      font-size: 0.875rem;
      color: var(--gray);
      margin-bottom: 0.25rem;
    }

    .detail-item div {
      font-weight: 500;
      color: var(--dark);
    }

    .marks-header {
      display: grid;
      grid-template-columns: 1fr 100px 100px;
      gap: 1rem;
      margin-bottom: 1rem;
      font-weight: 500;
      color: var(--primary);
    }

    .marks-row {
      display: grid;
      grid-template-columns: 1fr 100px 100px;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .marks-row input {
      padding: 0.75rem;
      border: 1px solid #cbd5e1;
      border-radius: var(--border-radius);
      font-size: 1rem;
      transition: var(--transition);
    }

    .marks-row input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .marks-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid #e2e8f0;
    }

    .link-btn {
      color: var(--primary);
      background: none;
      border: none;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .link-btn:hover {
      text-decoration: underline;
    }

    /* Report Card Styles */
    #reportCardContainer {
      margin-top: 2rem;
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 2rem;
    }

    .report-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .report-title {
      font-size: 1.75rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    .report-subtitle {
      color: var(--gray);
    }

    .report-student-info {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
    }

    .report-table th, .report-table td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }

    .report-table th {
      background: var(--primary-light);
      color: var(--primary);
      font-weight: 500;
    }

    .report-table tr:hover td {
      background: #f8fafc;
    }

    .report-chart {
      margin: 2rem 0;
      height: 300px;
    }

    .download-btn {
      text-align: center;
      margin-top: 2rem;
    }

    .empty-state {
      text-align: center;
      padding: 3rem 0;
      color: var(--gray);
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #cbd5e1;
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        padding: 1rem;
        gap: 1rem;
      }
      
      nav {
        width: 100%;
        justify-content: space-between;
      }
      
      .marks-row, .marks-header {
        grid-template-columns: 1fr;
      }
      
      .student-details, .report-student-info {
        grid-template-columns: 1fr;
      }
    }
</style>
</head>
<body>
  <header>
    <div class="logo">Acadexa</div>
    <nav>
      <a href="../dashboard.html">Home</a>
      <a href="../createclass.html">Classes</a>
      <a href="../attendance.html">Attendance</a>
      <a href="gradecard.php" class="active">Reports</a>
      <a href="inquiry.php">Inquiries</a>
      <a href="profile.php">Profile</a>
    </nav>
  </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-line"></i> Grade Card Management
                </h2>
                <a href="../bulkreport.html" class="btn btn-primary">
                    <i class="fas fa-file-export"></i> Generate All Reports
                </a>
            </div>
            
            <div class="form-group">
                <label for="classDropdown" class="form-label">Select Class</label>
                <select id="classDropdown" class="form-select">
                    <option value="">-- Select a Class --</option>
                </select>
            </div>
            
            <div id="studentList" class="student-list">
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>No Class Selected</h3>
                    <p>Please select a class to view students</p>
                </div>
            </div>
        </div>

        <!-- Report Container -->
        <div id="reportContainer" class="card">
            <h2 class="card-title" style="margin-bottom: 1.5rem;">
                <i class="fas fa-file-alt"></i> Generate Student Report
            </h2>
            
            <div id="studentInfo" class="student-details">
                <div class="detail-item">
                    <label>Student Name</label>
                    <div id="infoName">-</div>
                </div>
                <div class="detail-item">
                    <label>Grade</label>
                    <div id="infoStd">-</div>
                </div>
                <div class="detail-item">
                    <label>Class</label>
                    <div id="infoClass">-</div>
                </div>
                <div class="detail-item">
                    <label>Email</label>
                    <div id="infoEmail">-</div>
                </div>
            </div>
            
            <div id="marksSection">
                <div class="marks-header">
                    <div>Subject</div>
                    <div>MCQ</div>
                    <div>Theory</div>
                </div>
                <!-- Marks rows will be added here dynamically -->
            </div>
            
            <div class="marks-actions">
                <button id="addMoreBtn" class="link-btn">
                    <i class="fas fa-plus-circle"></i> Add More Subjects
                </button>
                <button id="generateReportBtn" class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i> Generate Report
                </button>
            </div>
            
            <div id="reportCardContainer"></div>
            
            <div class="download-btn">
                <button id="downloadReportBtn" class="btn btn-primary" style="display: none;">
                    <i class="fas fa-download"></i> Download Report as PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Load Chart.js BEFORE your main script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Fetch all classes from the database
        function loadClasses() {
            fetch('get_classes.php')
                .then(res => res.json())
                .then(classes => {
                    const classDropdown = document.getElementById('classDropdown');
                    classDropdown.innerHTML = '<option value="">-- Select a Class --</option>';
                    classes.forEach(cls => {
                        const opt = document.createElement('option');
                        opt.value = cls.code;
                        opt.textContent = cls.name;
                        classDropdown.appendChild(opt);
                    });
                });
        }

        // Fetch students for the selected class
        document.getElementById('classDropdown').addEventListener('change', function() {
            const classCode = this.value;
            const studentList = document.getElementById('studentList');
            studentList.innerHTML = '';
            document.getElementById('reportContainer').style.display = 'none';
            
            if (!classCode) {
                studentList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>No Class Selected</h3>
                        <p>Please select a class to view students</p>
                    </div>
                `;
                return;
            }

            fetch('get_students.php?classCode=' + encodeURIComponent(classCode))
                .then(res => res.json())
                .then(students => {
                    if (students.length === 0) {
                        studentList.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-user-slash"></i>
                                <h3>No Students Found</h3>
                                <p>This class doesn't have any students yet</p>
                            </div>
                        `;
                        return;
                    }
                    
                    students.forEach((student, idx) => {
                        const div = document.createElement('div');
                        div.className = 'student-item';
                        div.innerHTML = `
                            <div class="student-info">
                                <div class="student-avatar">${student.name.charAt(0)}</div>
                                <div>
                                    <div class="student-name">${student.name}</div>
                                    ${student.grade ? `<div class="student-grade">Grade ${student.grade}</div>` : ''}
                                </div>
                            </div>
                            <button class="btn btn-outline" data-id="${student.id}" data-class="${classCode}">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </button>
                        `;
                        studentList.appendChild(div);
                    });

                    // Attach event listeners to all report buttons
                    document.querySelectorAll('.student-item button').forEach(btn => {
                        btn.onclick = function() {
                            viewReport(this.dataset.id, '', this.dataset.class);
                        }
                    });
                });
        });

        // Initial load
        window.onload = loadClasses;

        // Report logic
        function viewReport(studentId, studentName, classCode) {
            fetch('gradecard.php?ajax=studentinfo&id=' + encodeURIComponent(studentId) + '&classCode=' + encodeURIComponent(classCode))
                .then(res => res.json())
                .then(data => {
                    document.getElementById('infoName').textContent = data.name || '-';
                    document.getElementById('infoStd').textContent = data.std || '-';
                    document.getElementById('infoClass').textContent = data.class || '-';
                    document.getElementById('infoEmail').textContent = data.email || '-';
                    
                    // Reset marks section
                    const marksSection = document.getElementById('marksSection');
                    marksSection.innerHTML = `
                        <div class="marks-header">
                            <div>Subject</div>
                            <div>MCQ</div>
                            <div>Theory</div>
                        </div>
                    `;
                    addMarksRow(); // Add the first row
                    document.getElementById('reportContainer').style.display = 'block';
                    document.getElementById('reportContainer').scrollIntoView({behavior: "smooth"});
                });
        }

        // Add a marks row (subject, MCQ, Theory)
        function addMarksRow() {
            const marksSection = document.getElementById('marksSection');
            const row = document.createElement('div');
            row.className = 'marks-row';
            row.innerHTML = `
                <input type="text" placeholder="Enter subject name">
                <input type="number" placeholder="0" min="0" max="100">
                <input type="number" placeholder="0" min="0" max="100">
            `;
            marksSection.appendChild(row);
        }

        // Add more button handler
        document.getElementById('addMoreBtn').addEventListener('click', function(e) {
            e.preventDefault();
            addMarksRow();
        });

        // Generate Report button handler
        document.getElementById('generateReportBtn').addEventListener('click', function() {
            // Fetch student info
            const name = document.getElementById('infoName').textContent;
            const std = document.getElementById('infoStd').textContent;
            const className = document.getElementById('infoClass').textContent;
            const email = document.getElementById('infoEmail').textContent;

            // Gather marks data
            const rows = document.querySelectorAll('.marks-row');
            const subjects = [];
            const mcqs = [];
            const theorys = [];
            let marksTableRows = '';
            
            rows.forEach(row => {
                const inputs = row.querySelectorAll('input');
                const subject = inputs[0].value.trim();
                const mcq = Number(inputs[1].value);
                const theory = Number(inputs[2].value);
                
                if(subject) {
                    subjects.push(subject);
                    mcqs.push(mcq);
                    theorys.push(theory);
                    marksTableRows += `
                        <tr>
                            <td>${subject}</td>
                            <td>${isNaN(mcq) ? '-' : mcq}</td>
                            <td>${isNaN(theory) ? '-' : theory}</td>
                        </tr>
                    `;
                }
            });

            // Build report card HTML
            let html = `
                <div style="max-width:800px; margin:0 auto;">
                    <div class="report-header">
                        <h1 class="report-title">Student Report Card</h1>
                        <p class="report-subtitle">Academic Performance Report</p>
                    </div>
                    
                    <div class="report-student-info">
                        <div class="detail-item">
                            <label>Student Name</label>
                            <div>${name}</div>
                        </div>
                        <div class="detail-item">
                            <label>Grade</label>
                            <div>${std}</div>
                        </div>
                        <div class="detail-item">
                            <label>Class</label>
                            <div>${className}</div>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <div>${email}</div>
                        </div>
                    </div>
                    
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>MCQ Score</th>
                                <th>Theory Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${marksTableRows || '<tr><td colspan="3" style="text-align:center;">No marks entered</td></tr>'}
                        </tbody>
                    </table>
                    
                    <div class="report-chart">
                        <canvas id="marksChart" height="300"></canvas>
                    </div>
                </div>
            `;
            
            document.getElementById('reportCardContainer').innerHTML = html;

            // Draw bar chart using Chart.js if we have data
            if(subjects.length > 0) {
                setTimeout(() => {
                    const ctx = document.getElementById('marksChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: subjects,
                            datasets: [
                                {
                                    label: 'MCQ Scores',
                                    data: mcqs,
                                    backgroundColor: '#4361ee',
                                    borderRadius: 4
                                },
                                {
                                    label: 'Theory Scores',
                                    data: theorys,
                                    backgroundColor: '#4cc9f0',
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { 
                                    position: 'top',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                },
                                title: { 
                                    display: true, 
                                    text: 'Marks Distribution',
                                    font: {
                                        size: 16
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    padding: 12,
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true,
                                    max: 100,
                                    grid: {
                                        drawBorder: false
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }, 100);
            }
            
            document.getElementById('downloadReportBtn').style.display = 'inline-block';
        });

        // Download Report as PDF
        document.getElementById('downloadReportBtn').onclick = async function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
            this.disabled = true;
            
            try {
                const reportDiv = document.getElementById('reportCardContainer').firstElementChild;
                const canvas = await html2canvas(reportDiv, { 
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    allowTaint: true
                });
                
                const imgData = canvas.toDataURL('image/png');
                const pdf = new window.jspdf.jsPDF('p', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = pageWidth - 40;
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                
                pdf.addImage(imgData, 'PNG', 20, 20, pdfWidth, pdfHeight);
                pdf.save('student_report.pdf');
                
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('There was an error generating the PDF. Please try again.');
            } finally {
                this.innerHTML = '<i class="fas fa-download"></i> Download Report as PDF';
                this.disabled = false;
            }
        };
    </script>
</body>
</html>