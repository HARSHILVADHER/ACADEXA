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
    <title>Attendance Reports | Acadexa</title>
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
            --success: #22c55e;
            --danger: #ef4444;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
            max-width: 1200px;
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
        
        .report-card {
            background: var(--white);
            border-radius: 16px;
            padding: 35px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(67, 97, 238, 0.1);
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
        
        .form-select, .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--white);
        }
        
        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .date-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .stats-container {
            display: none;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid var(--light-gray);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-light), var(--white));
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 600;
        }
        
        .download-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn-success {
            background: var(--success);
            color: var(--white);
        }
        
        .btn-success:hover {
            background: #16a34a;
        }
        
        .btn-danger {
            background: var(--danger);
            color: var(--white);
        }
        
        .btn-danger:hover {
            background: #dc2626;
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
                <h1 class="page-title">Attendance Reports</h1>
                <p style="color: var(--gray);">Generate attendance reports by class and date range</p>
            </div>
            <a href="reports.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Reports
            </a>
        </div>

        <div class="report-card">
            <form id="attendanceForm">
                <div class="form-group">
                    <label class="form-label">Select Class</label>
                    <select name="class_code" id="classSelect" class="form-select" required>
                        <option value="">Choose a class</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo htmlspecialchars($class['code']); ?>">
                                <?php echo htmlspecialchars($class['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="date-group">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="startDate" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" id="endDate" class="form-input" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i>
                    Generate Report
                </button>
            </form>

            <div id="statsContainer" class="stats-container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value" id="totalPresent">0</div>
                        <div class="stat-label">Total Present</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="totalAbsent">0</div>
                        <div class="stat-label">Total Absent</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="totalDays">0</div>
                        <div class="stat-label">Total Days</div>
                    </div>
                </div>

                <div class="download-options">
                    <button class="btn btn-success" onclick="downloadReport('csv')">
                        <i class="fas fa-file-csv"></i>
                        Download CSV
                    </button>
                    <button class="btn btn-danger" onclick="downloadReport('pdf')">
                        <i class="fas fa-file-pdf"></i>
                        Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let reportData = null;

        document.getElementById('attendanceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            
            try {
                const response = await fetch(`get_attendance_report.php?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    reportData = data;
                    document.getElementById('totalPresent').textContent = data.total_present;
                    document.getElementById('totalAbsent').textContent = data.total_absent;
                    document.getElementById('totalDays').textContent = data.total_days;
                    document.getElementById('statsContainer').style.display = 'block';
                } else {
                    alert('Error generating report');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        function downloadReport(format) {
            if (!reportData) return;
            
            const form = document.getElementById('attendanceForm');
            const formData = new FormData(form);
            formData.append('format', format);
            
            const params = new URLSearchParams(formData);
            window.location.href = `download_attendance_report.php?${params}`;
        }
    </script>
</body>
</html>
