<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

require_once 'config.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$student_id = $_GET['id'] ?? null;

if (!$student_id) {
    header('Location: ../students.html');
    exit();
}

// Get student info
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $student_id, $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check if profile_image column exists, if not add it
$result = $conn->query("SHOW COLUMNS FROM students LIKE 'profile_image'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE students ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL");
    $student['profile_image'] = null;
}

if (!$student) {
    header('Location: ../students.html');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile | Acadexa</title>
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
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            background-clip: text;
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
        
        .main-content {
            padding: 30px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .profile-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        .back-btn {
            background: var(--primary);
            color: var(--white);
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--card-shadow);
        }
        
        .back-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .profile-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
            align-items: start;
        }
        
        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .profile-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            color: var(--white);
            box-shadow: var(--card-shadow-hover);
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 15px;
            backdrop-filter: blur(10px);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
            border-radius: 50%;
        }
        
        .profile-avatar:hover .upload-overlay {
            opacity: 1;
        }
        
        #imageUpload {
            display: none;
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .profile-id {
            opacity: 0.8;
            margin-bottom: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        
        .stats-card, .contact-card {
            background: var(--white);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .stat {
            text-align: center;
            padding: 15px;
            background: var(--light);
            border-radius: 12px;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .contact-item:last-child {
            border-bottom: none;
        }
        
        .contact-item i {
            color: var(--primary);
            width: 16px;
        }
        
        .profile-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .content-card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }
        
        .content-card:hover {
            box-shadow: var(--card-shadow-hover);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .card-badge {
            padding: 4px 12px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .profile-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .main-content {
                padding: 20px;
            }
            
            header {
                padding: 15px 20px;
            }
        } 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .card-badge.success {
            background: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .performance-item {
            text-align: center;
            padding: 20px;
            background: var(--light);
            border-radius: 12px;
        }
        
        .performance-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 8px;
        }
        
        .performance-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: var(--gray);
            font-weight: 500;
        }
        
        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .fee-summary {
            margin-bottom: 20px;
        }
        
        .fee-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .fee-row.total {
            border-top: 2px solid var(--primary);
            border-bottom: none;
            font-weight: 600;
            margin-top: 10px;
            padding-top: 15px;
        }
        
        .fee-label {
            color: var(--gray);
        }
        
        .fee-amount {
            font-weight: 600;
            color: var(--primary);
        }
        
        .payment-status {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .status-item {
            padding: 15px;
            background: var(--light);
            border-radius: 8px;
            text-align: center;
        }
        
        .status-label {
            font-size: 0.8rem;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .status-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .emergency-info {
            display: grid;
            gap: 15px;
        }
        
        .emergency-item {
            padding: 15px;
            background: var(--light);
            border-radius: 8px;
        }
        
        .emergency-label {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .emergency-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        @media (max-width: 1200px) {
            .profile-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .profile-sidebar {
                order: 2;
            }
            
            .profile-content {
                order: 1;
            }
        }
        
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            nav {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .main-content {
                padding: 20px;
            }
            
            .performance-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .payment-status {
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
            <a href="../students.html" class="active">Students</a>
            <a href="../attendance.html">Attendance</a>
            <a href="gradecard.php">Reports</a>
            <a href="inquiry.php">Inquiries</a>
            <a href="profile.php">Profile</a>
        </nav>
    </header>

    <div class="main-content">
        <div class="profile-header">
            <h1 class="profile-title">Student Profile</h1>
            <a href="../students.html" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>

        <!-- Main Profile Layout -->
        <div class="profile-layout">
            <!-- Left Sidebar -->
            <div class="profile-sidebar">
                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-avatar" onclick="document.getElementById('imageUpload').click()">
                        <?php if (!empty($student['profile_image']) && file_exists('../../' . $student['profile_image'])): ?>
                            <img src="/ACADEXA/<?= htmlspecialchars($student['profile_image']) ?>" alt="Profile Image">
                        <?php else: ?>
                            <span id="avatarLetter"><?= strtoupper(substr(htmlspecialchars($student['name']), 0, 1)) ?></span>
                        <?php endif; ?>
                        <div class="upload-overlay">
                            <i class="fas fa-camera" style="color: white; font-size: 1.2rem;"></i>
                        </div>
                    </div>
                    <input type="file" id="imageUpload" accept="image/*">
                    <h2 class="profile-name"><?= htmlspecialchars($student['name']) ?></h2>
                    <p class="profile-id">ID: #<?= str_pad($student['id'], 4, '0', STR_PAD_LEFT) ?></p>
                    <div class="profile-status">
                        <span class="status-badge active">Active Student</span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="stats-card">
                    <h3 class="card-title">Quick Stats</h3>
                    <div class="stat-grid">
                        <div class="stat">
                            <div class="stat-number"><?= htmlspecialchars($student['age']) ?></div>
                            <div class="stat-label">Age</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number"><?= htmlspecialchars($student['class_code']) ?></div>
                            <div class="stat-label">Class</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">92%</div>
                            <div class="stat-label">Attendance</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">A-</div>
                            <div class="stat-label">Grade</div>
                        </div>
                    </div>
                </div>

                <!-- Contact Card -->
                <div class="contact-card">
                    <h3 class="card-title">Contact Info</h3>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($student['contact']) ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($student['email']) ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($student['address'] ?? 'Not provided') ?></span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="profile-content">
                <!-- Academic Performance -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> Academic Performance</h3>
                        <span class="card-badge">Current Semester</span>
                    </div>
                    <div class="performance-grid">
                        <div class="performance-item">
                            <div class="performance-label">Overall GPA</div>
                            <div class="performance-value">3.8</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Subjects Enrolled</div>
                            <div class="performance-value">6</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Assignments Completed</div>
                            <div class="performance-value">24/28</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Class Rank</div>
                            <div class="performance-value">#5</div>
                        </div>
                    </div>
                </div>

                <!-- Academic Details -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-graduation-cap"></i> Academic Details</h3>
                    </div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Class Code</span>
                            <span class="detail-value"><?= htmlspecialchars($student['class_code']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Enrollment Date</span>
                            <span class="detail-value"><?= date('M j, Y', strtotime($student['created_at'] ?? 'now')) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Academic Year</span>
                            <span class="detail-value">2024-2025</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Student Type</span>
                            <span class="detail-value">Regular</span>
                        </div>
                    </div>
                </div>

                <!-- Fee Information -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-credit-card"></i> Fee Information</h3>
                        <span class="card-badge success">Paid</span>
                    </div>
                    <div class="fee-summary">
                        <div class="fee-row">
                            <span class="fee-label">Monthly Tuition</span>
                            <span class="fee-amount">₹5,000</span>
                        </div>
                        <div class="fee-row">
                            <span class="fee-label">Registration Fee</span>
                            <span class="fee-amount">₹1,000</span>
                        </div>
                        <div class="fee-row total">
                            <span class="fee-label">Total Paid</span>
                            <span class="fee-amount">₹15,000</span>
                        </div>
                    </div>
                    <div class="payment-status">
                        <div class="status-item">
                            <span class="status-label">Last Payment</span>
                            <span class="status-value">March 15, 2024</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Next Due</span>
                            <span class="status-value">April 15, 2024</span>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-friends"></i> Emergency Contact</h3>
                    </div>
                    <div class="emergency-info">
                        <div class="emergency-item">
                            <div class="emergency-label">Guardian Name</div>
                            <div class="emergency-value">Not provided</div>
                        </div>
                        <div class="emergency-item">
                            <div class="emergency-label">Guardian Contact</div>
                            <div class="emergency-value">Not provided</div>
                        </div>
                        <div class="emergency-item">
                            <div class="emergency-label">Relationship</div>
                            <div class="emergency-value">Parent/Guardian</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('student_id', '<?= $student_id ?>');
        
        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const avatar = document.querySelector('.profile-avatar');
                const letter = document.getElementById('avatarLetter');
                const existingImg = avatar.querySelector('img');
                
                if (existingImg) {
                    existingImg.src = data.image_url;
                } else {
                    const img = document.createElement('img');
                    img.src = data.image_url;
                    img.alt = 'Profile Image';
                    
                    if (letter) letter.style.display = 'none';
                    avatar.appendChild(img);
                }
                
                alert('Profile image updated successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Upload failed. Please try again.');
        });
    });
    </script>
</body>
</html>