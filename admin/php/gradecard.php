<?php
// Handle AJAX request for student info
if (isset($_GET['ajax']) && $_GET['ajax'] === 'studentinfo' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    require_once 'config.php';
    session_start();

    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["name" => "Unknown", "roll_no" => "-", "class" => "-", "email" => "-"]);
        exit;
    }

    $id = intval($_GET['id']);
    
    try {
        $stmt = $conn->prepare("SELECT name, roll_no, class_code, email FROM students WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $student = $result->fetch_assoc();
            echo json_encode([
                "name" => $student['name'] ?? 'Unknown',
                "roll_no" => $student['roll_no'] ?? '-',
                "class" => $student['class_code'] ?? '-',
                "email" => $student['email'] ?? '-'
            ]);
        } else {
            echo json_encode(["name" => "Student Not Found", "roll_no" => "-", "class" => "-", "email" => "-"]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["name" => "Error Loading", "roll_no" => "-", "class" => "-", "email" => "-"]);
    }
    
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

    .main-container {
      display: grid;
      grid-template-columns: 400px 1fr;
      gap: 30px;
      padding: 30px 40px;
      max-width: 1800px;
      margin: 0 auto;
    }

    .left-panel {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .right-panel {
      display: flex;
      flex-direction: column;
    }

    .card {
      background: white;
      border-radius: 16px;
      box-shadow: var(--card-shadow);
      padding: 25px;
      border: 1px solid rgba(67, 97, 238, 0.1);
    }

    .card-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 20px;
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

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--dark);
      font-size: 0.95rem;
    }

    .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid var(--light-gray);
      border-radius: 12px;
      font-size: 1rem;
      transition: var(--transition);
      background: var(--white);
    }

    .form-select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .student-list {
      max-height: 500px;
      overflow-y: auto;
    }

    .student-item {
      padding: 15px;
      border-radius: 12px;
      background: var(--light);
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: var(--transition);
      border: 2px solid transparent;
      cursor: pointer;
    }

    .student-item:hover {
      border-color: var(--primary);
      background: var(--primary-light);
    }

    .student-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .student-avatar {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .student-name {
      font-weight: 600;
      color: var(--dark);
      font-size: 1rem;
    }

    .btn {
      padding: 8px 16px;
      border-radius: 8px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .btn-outline {
      background: transparent;
      border: 2px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background: var(--primary);
      color: white;
    }

    #reportContainer {
      display: none;
    }

    .student-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
      padding-bottom: 25px;
      border-bottom: 2px solid var(--light-gray);
    }

    .detail-item {
      background: var(--light);
      padding: 15px;
      border-radius: 12px;
      border-left: 4px solid var(--primary);
      overflow: hidden;
      min-width: 0;
    }

    .detail-item label {
      display: block;
      font-size: 0.85rem;
      color: var(--gray);
      margin-bottom: 5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .detail-item div {
      font-weight: 600;
      color: var(--dark);
      font-size: 1.1rem;
      word-wrap: break-word;
      word-break: break-all;
      overflow-wrap: break-word;
      max-width: 100%;
    }

    .marks-header {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 15px;
      margin-bottom: 15px;
      font-weight: 700;
      color: var(--primary);
      background: var(--primary-light);
      padding: 15px;
      border-radius: 12px;
    }

    .marks-row {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 15px;
      margin-bottom: 15px;
      align-items: center;
    }

    .marks-row input[type="text"] {
      padding: 12px 16px;
      border: 2px solid var(--light-gray);
      border-radius: 8px;
      font-size: 1rem;
      transition: var(--transition);
    }

    .marks-input-group {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--light);
      padding: 8px 12px;
      border-radius: 8px;
      border: 2px solid var(--light-gray);
      transition: var(--transition);
    }

    .marks-input-group:focus-within {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .marks-input-group input {
      border: none;
      outline: none;
      background: transparent;
      font-size: 1rem;
      font-weight: 600;
      width: 50px;
      text-align: center;
    }

    .marks-input-group .slash {
      color: var(--gray);
      font-weight: 700;
      font-size: 1.2rem;
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
      margin-top: 30px;
      padding-top: 25px;
      border-top: 2px solid var(--light-gray);
    }

    .link-btn {
      color: var(--primary);
      background: var(--primary-light);
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 20px;
      border-radius: 8px;
    }

    .link-btn:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-2px);
    }

    #reportCardContainer {
      margin-top: 30px;
      background: white;
      border-radius: 16px;
      box-shadow: var(--card-shadow);
      padding: 30px;
      border: 1px solid rgba(67, 97, 238, 0.1);
    }

    .report-header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 3px solid var(--primary);
    }

    .report-title {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 8px;
      font-weight: 800;
    }

    .report-subtitle {
      color: var(--gray);
      font-size: 1.1rem;
      font-weight: 500;
    }

    .report-student-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
      background: var(--light);
      padding: 25px;
      border-radius: 12px;
    }

    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: var(--card-shadow);
    }

    .report-table th, .report-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid var(--light-gray);
    }

    .report-table th {
      background: var(--primary);
      color: var(--white);
      font-weight: 700;
      font-size: 1rem;
    }

    .report-table tr:hover td {
      background: var(--primary-light);
    }

    .report-chart {
      margin: 30px 0;
      height: 400px;
      background: var(--white);
      border-radius: 12px;
      padding: 20px;
      box-shadow: var(--card-shadow);
    }

    .download-btn {
      text-align: center;
      margin-top: 30px;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: var(--gray);
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 15px;
      color: var(--primary-light);
    }

    .empty-state h3 {
      color: var(--dark);
      margin-bottom: 8px;
    }

    @media (max-width: 1200px) {
      .main-container {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 20px;
      }
      
      .left-panel {
        order: 1;
      }
      
      .right-panel {
        order: 2;
      }
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        padding: 15px 20px;
        gap: 15px;
      }
      
      nav {
        width: 100%;
        justify-content: space-between;
        overflow-x: auto;
      }
      
      .marks-row, .marks-header {
        grid-template-columns: 1fr;
        gap: 10px;
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
      <a href="dashboard.php">Home</a>
      <a href="../createclass.html">Classes</a>
      <a href="../attendance.html">Attendance</a>
      <a href="gradecard.php" class="active">Reports</a>
      <a href="inquiry.php">Inquiries</a>
      <a href="profile.php">Profile</a>
    </nav>
  </header>

  <div class="main-container">
    <!-- Left Panel -->
    <div class="left-panel">
      <!-- Bulk Report Button -->
      <button onclick="window.location.href='../bulkreport.html'" class="btn btn-primary" style="width: 100%; margin-bottom: 20px;">
        <i class="fas fa-file-export"></i> Bulk Report Generator
      </button>
      
      <!-- Class Selection Card -->
      <div class="card">
        <h3 class="card-title">
          <i class="fas fa-graduation-cap"></i> Select Class
        </h3>
        
        <div class="form-group">
          <label for="classDropdown" class="form-label">Choose Class</label>
          <select id="classDropdown" class="form-select">
            <option value="">-- Select a Class --</option>
          </select>
        </div>
      </div>

      <!-- Students List Card -->
      <div class="card">
        <h3 class="card-title">
          <i class="fas fa-users"></i> Students
        </h3>
        
        <div id="studentList" class="student-list">
          <div class="empty-state">
            <i class="fas fa-user-graduate"></i>
            <h4>No Class Selected</h4>
            <p>Please select a class to view students</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <div id="reportContainer" class="card">
        <h3 class="card-title">
          <i class="fas fa-file-alt"></i> Generate Student Report
        </h3>
        
        <!-- Student Details -->
        <div id="studentInfo" class="student-details">
          <div class="detail-item">
            <label>Student Name</label>
            <div id="infoName">-</div>
          </div>
          <div class="detail-item">
            <label>Roll Number</label>
            <div id="infoRollNo">-</div>
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
        
        <!-- Marks Section -->
        <div id="marksSection">
          <div class="marks-header">
            <div>Subject</div>
            <div>MCQ Marks</div>
            <div>Theory Marks</div>
          </div>
        </div>
        
        <!-- Actions -->
        <div class="marks-actions">
          <button id="addMoreBtn" class="link-btn">
            <i class="fas fa-plus-circle"></i> Add More Subjects
          </button>
          <button id="generateReportBtn" class="btn btn-primary">
            <i class="fas fa-chart-bar"></i> Generate Report
          </button>
        </div>
        
        <!-- Report Card Container -->
        <div id="reportCardContainer"></div>
        
        <!-- Download Button -->
        <div class="download-btn">
          <button id="downloadReportBtn" class="btn btn-primary" style="display: none;">
            <i class="fas fa-download"></i> Download Report as PDF
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script>
    function loadClasses() {
      fetch('get_classes.php')
        .then(res => res.json())
        .then(classes => {
          const classDropdown = document.getElementById('classDropdown');
          classDropdown.innerHTML = '<option value="">-- Select a Class --</option>';
          if (Array.isArray(classes)) {
            classes.forEach(cls => {
              const opt = document.createElement('option');
              opt.value = cls.code;
              opt.textContent = cls.name;
              classDropdown.appendChild(opt);
            });
          }
        })
        .catch(error => {
          console.error('Error loading classes:', error);
        });
    }

    document.getElementById('classDropdown').addEventListener('change', function() {
      const classCode = this.value;
      const studentList = document.getElementById('studentList');
      studentList.innerHTML = '';
      document.getElementById('reportContainer').style.display = 'none';
      
      if (!classCode) {
        studentList.innerHTML = `
          <div class="empty-state">
            <i class="fas fa-user-graduate"></i>
            <h4>No Class Selected</h4>
            <p>Please select a class to view students</p>
          </div>
        `;
        return;
      }

      fetch('get_students.php?classCode=' + encodeURIComponent(classCode))
        .then(res => res.json())
        .then(students => {
          if (!Array.isArray(students) || students.length === 0) {
            studentList.innerHTML = `
              <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <h4>No Students Found</h4>
                <p>This class doesn't have any students yet</p>
              </div>
            `;
            return;
          }
          
          students.forEach((student) => {
            const div = document.createElement('div');
            div.className = 'student-item';
            div.innerHTML = `
              <div class="student-info">
                <div class="student-avatar">${student.name ? student.name.charAt(0).toUpperCase() : 'S'}</div>
                <div>
                  <div class="student-name">${student.name || 'Unknown'}</div>
                </div>
              </div>
              <button class="btn btn-outline" data-id="${student.id}">
                <i class="fas fa-file-alt"></i> Report
              </button>
            `;
            studentList.appendChild(div);
          });

          document.querySelectorAll('.student-item button').forEach(btn => {
            btn.onclick = function() {
              viewReport(this.dataset.id);
            }
          });
        })
        .catch(error => {
          console.error('Error loading students:', error);
        });
    });

    function viewReport(studentId) {
      fetch('gradecard.php?ajax=studentinfo&id=' + encodeURIComponent(studentId))
        .then(res => res.json())
        .then(data => {
          document.getElementById('infoName').textContent = data.name || 'Unknown';
          document.getElementById('infoRollNo').textContent = data.roll_no || '-';
          document.getElementById('infoClass').textContent = data.class || '-';
          document.getElementById('infoEmail').textContent = data.email || '-';
          
          const marksSection = document.getElementById('marksSection');
          marksSection.innerHTML = `
            <div class="marks-header">
              <div>Subject</div>
              <div>MCQ Marks</div>
              <div>Theory Marks</div>
            </div>
          `;
          addMarksRow();
          document.getElementById('reportContainer').style.display = 'block';
        })
        .catch(error => {
          document.getElementById('infoName').textContent = 'Unknown Student';
          document.getElementById('infoRollNo').textContent = '-';
          document.getElementById('infoClass').textContent = '-';
          document.getElementById('infoEmail').textContent = '-';
          
          const marksSection = document.getElementById('marksSection');
          marksSection.innerHTML = `
            <div class="marks-header">
              <div>Subject</div>
              <div>MCQ Marks</div>
              <div>Theory Marks</div>
            </div>
          `;
          addMarksRow();
          document.getElementById('reportContainer').style.display = 'block';
        });
    }

    function addMarksRow() {
      const marksSection = document.getElementById('marksSection');
      const row = document.createElement('div');
      row.className = 'marks-row';
      row.innerHTML = `
        <input type="text" placeholder="Enter subject name">
        <div class="marks-input-group">
          <input type="number" placeholder="0" min="0" max="100">
          <span class="slash">/</span>
          <input type="number" placeholder="100" min="1" max="1000">
        </div>
        <div class="marks-input-group">
          <input type="number" placeholder="0" min="0" max="100">
          <span class="slash">/</span>
          <input type="number" placeholder="100" min="1" max="1000">
        </div>
      `;
      marksSection.appendChild(row);
    }

    document.getElementById('addMoreBtn').addEventListener('click', function(e) {
      e.preventDefault();
      addMarksRow();
    });

    document.getElementById('generateReportBtn').addEventListener('click', function() {
      const name = document.getElementById('infoName').textContent;
      const rollNo = document.getElementById('infoRollNo').textContent;
      const className = document.getElementById('infoClass').textContent;
      const email = document.getElementById('infoEmail').textContent;

      const rows = document.querySelectorAll('.marks-row');
      const subjects = [];
      const mcqScores = [];
      const theoryScores = [];
      const mcqTotals = [];
      const theoryTotals = [];
      let marksTableRows = '';
      
      rows.forEach(row => {
        const inputs = row.querySelectorAll('input');
        const subject = inputs[0].value.trim();
        const mcqScore = parseInt(inputs[1].value) || 0;
        const mcqTotal = parseInt(inputs[2].value) || 100;
        const theoryScore = parseInt(inputs[3].value) || 0;
        const theoryTotal = parseInt(inputs[4].value) || 100;
        
        if(subject) {
          subjects.push(subject);
          mcqScores.push(mcqScore);
          theoryScores.push(theoryScore);
          mcqTotals.push(mcqTotal);
          theoryTotals.push(theoryTotal);
          
          const mcqPercentage = ((mcqScore / mcqTotal) * 100).toFixed(1);
          const theoryPercentage = ((theoryScore / theoryTotal) * 100).toFixed(1);
          
          marksTableRows += `
            <tr>
              <td><strong>${subject}</strong></td>
              <td>${mcqScore}/${mcqTotal} (${mcqPercentage}%)</td>
              <td>${theoryScore}/${theoryTotal} (${theoryPercentage}%)</td>
            </tr>
          `;
        }
      });

      let html = `
        <div style="max-width:900px; margin:0 auto;">
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
              <label>Roll Number</label>
              <div>${rollNo}</div>
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
            <canvas id="marksChart"></canvas>
          </div>
        </div>
      `;
      
      document.getElementById('reportCardContainer').innerHTML = html;

      if(subjects.length > 0) {
        setTimeout(() => {
          const ctx = document.getElementById('marksChart').getContext('2d');
          const maxTotal = Math.max(...mcqTotals, ...theoryTotals);
          
          new Chart(ctx, {
            type: 'bar',
            data: {
              labels: subjects,
              datasets: [
                {
                  label: 'MCQ Scores',
                  data: mcqScores,
                  backgroundColor: '#4361ee',
                  borderRadius: 6,
                  borderWidth: 2,
                  borderColor: '#3a0ca3'
                },
                {
                  label: 'Theory Scores',
                  data: theoryScores,
                  backgroundColor: '#4cc9f0',
                  borderRadius: 6,
                  borderWidth: 2,
                  borderColor: '#0891b2'
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { 
                  position: 'top',
                  labels: {
                    boxWidth: 15,
                    padding: 20,
                    usePointStyle: true,
                    font: {
                      size: 14,
                      weight: 'bold'
                    }
                  }
                },
                title: { 
                  display: true, 
                  text: 'Performance Analysis (Marks)',
                  font: {
                    size: 18,
                    weight: 'bold'
                  },
                  color: '#1a1a1a'
                }
              },
              scales: {
                y: { 
                  beginAtZero: true,
                  max: maxTotal,
                  title: {
                    display: true,
                    text: 'Marks',
                    font: {
                      size: 14,
                      weight: 'bold'
                    }
                  },
                  grid: {
                    color: '#e9ecef'
                  }
                },
                x: {
                  title: {
                    display: true,
                    text: 'Subjects',
                    font: {
                      size: 14,
                      weight: 'bold'
                    }
                  },
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

    document.getElementById('downloadReportBtn').onclick = async function() {
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
      this.disabled = true;
      
      try {
        const reportDiv = document.getElementById('reportCardContainer').firstElementChild;
        const canvas = await html2canvas(reportDiv, { 
          scale: 2,
          useCORS: true,
          allowTaint: true,
          backgroundColor: '#ffffff'
        });
        const imgData = canvas.toDataURL('image/png');
        const pdf = new window.jspdf.jsPDF('p', 'pt', 'a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pageWidth - 40;
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        
        pdf.addImage(imgData, 'PNG', 20, 20, pdfWidth, pdfHeight);
        pdf.save('student_report_card.pdf');
      } catch (error) {
        alert('Error generating PDF. Please try again.');
      } finally {
        this.innerHTML = '<i class="fas fa-download"></i> Download Report as PDF';
        this.disabled = false;
      }
    };

    window.onload = loadClasses;
  </script>
</body>
</html>