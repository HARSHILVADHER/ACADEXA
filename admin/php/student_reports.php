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
                <form method="POST" action="#" id="marks_report_form">
                    <div class="filter-row">
                        <div class="input-group">
                            <label>Class</label>
                            <select name="class_code" id="class_select_marks" class="input-field" required>
                                <option value="">Choose a class</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo htmlspecialchars($class['code']); ?>">
                                        <?php echo htmlspecialchars($class['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label>Exam (Optional)</label>
                            <select name="exam_code" id="exam_select_marks" class="input-field">
                                <option value="">Choose an exam</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label>Subject (Optional)</label>
                            <select name="subject_code" id="subject_select_marks" class="input-field">
                                <option value="">Choose a subject</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Individual Progress Report -->
        <div id="progress-report" class="report-section" style="display:none;">
            <div class="filter-card">
                <h3>Select Student</h3>
                <form method="POST" action="#">
                    <div class="filter-row">
                        <div class="input-group">
                            <label>Class</label>
                            <select name="class_code" id="class_select_progress" class="input-field" required>
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
                            <select name="student_id" id="student_select_progress" class="input-field" required>
                                <option value="">Choose a student</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        // Load exams and subjects for Student Marks Report
        document.getElementById('class_select_marks').addEventListener('change', function() {
            const classCode = this.value;
            const className = this.options[this.selectedIndex].text;
            const examSelect = document.getElementById('exam_select_marks');
            const subjectSelect = document.getElementById('subject_select_marks');
            
            examSelect.innerHTML = '<option value="">Choose an exam</option>';
            subjectSelect.innerHTML = '<option value="">Choose a subject</option>';
            
            if (classCode) {
                fetch(`get_exams_subjects.php?class_code=${encodeURIComponent(classCode)}&class_name=${encodeURIComponent(className)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exams.length === 0) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No exam';
                            examSelect.appendChild(option);
                        } else {
                            data.exams.forEach(exam => {
                                const option = document.createElement('option');
                                option.value = exam.code;
                                option.textContent = exam.exam_name;
                                examSelect.appendChild(option);
                            });
                        }
                        data.subjects.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.subject_code;
                            option.textContent = subject.subject_name;
                            subjectSelect.appendChild(option);
                        });
                    });
            }
        });

        // Validate form: either exam or subject must be selected
        document.getElementById('marks_report_form').addEventListener('submit', function(e) {
            const examValue = document.getElementById('exam_select_marks').value;
            const subjectValue = document.getElementById('subject_select_marks').value;
            
            if (!examValue && !subjectValue) {
                e.preventDefault();
                alert('Please select either an Exam or a Subject');
            }
        });

        // Load students for Individual Progress Report
        document.getElementById('class_select_progress').addEventListener('change', function() {
            const classCode = this.value;
            const studentSelect = document.getElementById('student_select_progress');
            
            studentSelect.innerHTML = '<option value="">Choose a student</option>';
            
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
    </script>
</body>
</html>
