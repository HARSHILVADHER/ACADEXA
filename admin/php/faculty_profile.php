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
$faculty_id = $_GET['id'] ?? null;

if (!$faculty_id) {
    header('Location: ../faculty.html');
    exit();
}

// Get faculty info
$stmt = $conn->prepare("SELECT * FROM faculty WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $faculty_id, $user_id);
$stmt->execute();
$faculty = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$faculty) {
    header('Location: ../faculty.html');
    exit();
}

// Check if profile_image column exists, if not add it
$result = $conn->query("SHOW COLUMNS FROM faculty LIKE 'profile_image'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE faculty ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL");
    $faculty['profile_image'] = null;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Profile | Acadexa</title>
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

    <div class="main-content">
        <div class="profile-header">
            <h1 class="profile-title">Faculty Profile</h1>
            <a href="../faculty.html" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Faculty
            </a>
        </div>

        <div class="profile-layout">
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar" onclick="document.getElementById('imageUpload').click()">
                        <?php if (!empty($faculty['profile_image']) && file_exists('../../' . $faculty['profile_image'])): ?>
                            <img src="/ACADEXA/<?= htmlspecialchars($faculty['profile_image']) ?>" alt="Profile Image">
                        <?php else: ?>
                            <span id="avatarLetter"><?= strtoupper(substr(htmlspecialchars($faculty['name']), 0, 1)) ?></span>
                        <?php endif; ?>
                        <div class="upload-overlay">
                            <i class="fas fa-camera" style="color: white; font-size: 1.2rem;"></i>
                        </div>
                    </div>
                    <input type="file" id="imageUpload" accept="image/*">
                    <h2 class="profile-name"><?= htmlspecialchars($faculty['name']) ?></h2>
                    <div class="profile-status">
                        <span class="status-badge active">Active Faculty</span>
                        <p class="profile-roll" style="margin-top: 8px; opacity: 0.8; font-size: 0.9rem;">Faculty ID: <?= htmlspecialchars($faculty['faculty_id'] ?? 'Not provided') ?></p>
                    </div>
                </div>

                <div class="stats-card">
                    <h3 class="card-title">Quick Stats</h3>
                    <div class="stat-grid">
                        <div class="stat">
                            <div class="stat-number"><?= $faculty['dob'] ? floor((time() - strtotime($faculty['dob'])) / (365.25 * 24 * 60 * 60)) : 'N/A' ?></div>
                            <div class="stat-label">Age</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">5</div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                    </div>
                </div>

                <div class="contact-card">
                    <h3 class="card-title">Contact Info</h3>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <input type="text" id="contact_number" value="<?= htmlspecialchars($faculty['contact_number'] ?? '') ?>" style="border: none; background: transparent; outline: none; width: 100%; font-size: 0.9rem;">
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" value="<?= htmlspecialchars($faculty['email']) ?>" style="border: none; background: transparent; outline: none; width: 100%; font-size: 0.9rem;">
                    </div>
                    <button onclick="updateContact()" style="margin-top: 10px; padding: 8px 16px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.8rem;">Update Contact</button>
                </div>
            </div>

            <div class="profile-content">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chalkboard-teacher"></i> Teaching Performance</h3>
                        <span class="card-badge">Current Semester</span>
                    </div>
                    <div class="performance-grid">
                        <div class="performance-item">
                            <div class="performance-label">Classes Taught</div>
                            <div class="performance-value">12</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Students Mentored</div>
                            <div class="performance-value">45</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Average Rating</div>
                            <div class="performance-value">4.8</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-label">Courses</div>
                            <div class="performance-value">3</div>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tie"></i> Faculty Details</h3>
                    </div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Subject Specialization</span>
                            <span class="detail-value"><?= htmlspecialchars($faculty['subject'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Join Date</span>
                            <span class="detail-value"><?= date('M j, Y', strtotime($faculty['created_at'] ?? 'now')) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Date of Birth</span>
                            <span class="detail-value"><?= $faculty['dob'] ? date('M j, Y', strtotime($faculty['dob'])) : 'Not provided' ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Employment Type</span>
                            <span class="detail-value">Full-time</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Department</span>
                            <span class="detail-value">Academic</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value" style="color: var(--success);">Active</span>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Salary Information</h3>
                        <span class="card-badge">Current</span>
                    </div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Base Salary</span>
                            <span class="detail-value">₹45,000/month</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Allowances</span>
                            <span class="detail-value">₹8,000/month</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total Package</span>
                            <span class="detail-value">₹6,36,000/year</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Last Increment</span>
                            <span class="detail-value">January 2024</span>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-shield-alt"></i> Rights & Permissions</h3>
                        <span class="card-badge success">Active</span>
                    </div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Class Management</span>
                            <span class="detail-value">Full Access</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Grade Entry</span>
                            <span class="detail-value">Authorized</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Student Records</span>
                            <span class="detail-value">View & Edit</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Report Generation</span>
                            <span class="detail-value">Enabled</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updateContact() {
        const contact = document.getElementById('contact_number').value;
        const email = document.getElementById('email').value;
        
        const formData = new FormData();
        formData.append('faculty_id', <?= $faculty_id ?>);
        formData.append('contact_number', contact);
        formData.append('email', email);
        
        fetch('update_faculty_contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === 'success') {
                alert('Contact information updated successfully!');
            } else {
                alert('Error updating contact information');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating contact information');
        });
    }

    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('faculty_id', '<?= $faculty_id ?>');
        formData.append('type', 'faculty');
        
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