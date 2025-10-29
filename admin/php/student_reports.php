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
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--light-gray);
            color: var(--gray);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .back-btn:hover {
            background: var(--gray);
            color: var(--white);
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .report-card {
            background: var(--white);
            border-radius: 16px;
            padding: 35px;
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
            width: 70px;
            height: 70px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 25px;
        }
        
        .report-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
        }
        
        .report-desc {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 25px;
            line-height: 1.7;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--white);
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .download-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px 24px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 1rem;
        }
        
        .download-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .reports-grid {
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
            <a href="reports.php" class="active">Reports</a>
            <a href="inquiry.php">Inquiries</a>
            <a href="profile.php">Profile</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Student Reports</h1>
                <p class="page-subtitle">Download student marks and progress reports</p>
            </div>
            <a href="reports.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Reports
            </a>
        </div>

        <div class="reports-grid">
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="report-title">Student Marks Report</h3>
                <p class="report-desc">Download detailed marks report for all exams and subjects</p>
                
                <form method="POST" action="#">
                    <div class="form-group">
                        <label class="form-label">Select Class</label>
                        <select name="class_code" class="form-select" required>
                            <option value="">Choose a class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['code']); ?>">
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Select Exam</label>
                        <select name="exam_id" class="form-select" required>
                            <option value="">Choose an exam</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="download-btn">
                        <i class="fas fa-download"></i>
                        Download Marks Report
                    </button>
                </form>
            </div>

            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 class="report-title">Individual Progress Report</h3>
                <p class="report-desc">Download comprehensive progress report for individual students</p>
                
                <form method="POST" action="#">
                    <div class="form-group">
                        <label class="form-label">Select Class</label>
                        <select name="class_code" class="form-select" required>
                            <option value="">Choose a class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['code']); ?>">
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Select Student</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Choose a student</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="download-btn">
                        <i class="fas fa-download"></i>
                        Download Progress Report
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Load exams for Student Marks Report
        document.querySelector('.report-card:nth-child(1) select[name="class_code"]').addEventListener('change', function() {
            const classCode = this.value;
            const examSelect = document.querySelector('.report-card:nth-child(1) select[name="exam_id"]');
            
            examSelect.innerHTML = '<option value="">Choose an exam</option>';
            
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

        // Load students for Individual Progress Report
        document.querySelector('.report-card:nth-child(2) select[name="class_code"]').addEventListener('change', function() {
            const classCode = this.value;
            const studentSelect = document.querySelector('.report-card:nth-child(2) select[name="student_id"]');
            
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
