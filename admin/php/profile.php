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
$success_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $bio = trim($_POST['bio']);

    // Update users table
    $update_user = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
    $update_user->bind_param("si", $email, $user_id);
    $update_user->execute();
    $update_user->close();

    // Check if profile exists
    $check = $conn->prepare("SELECT id FROM profile WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        $update = $conn->prepare("UPDATE profile SET full_name=?, phone=?, address=?, dob=?, gender=?, bio=?, updated_at=NOW() WHERE user_id=?");
        $update->bind_param("ssssssi", $full_name, $phone, $address, $dob, $gender, $bio, $user_id);
        $update->execute();
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO profile (user_id, full_name, phone, address, dob, gender, bio) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("issssss", $user_id, $full_name, $phone, $address, $dob, $gender, $bio);
        $insert->execute();
        $insert->close();
    }

    $check->close();
    $success_msg = "Profile updated successfully!";
}

// Get user info
$user_stmt = $conn->prepare("SELECT username, email, profile_image FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

// Get profile info
$profile_stmt = $conn->prepare("SELECT * FROM profile WHERE user_id = ?");
$profile_stmt->bind_param("i", $user_id);
$profile_stmt->execute();
$profile = $profile_stmt->get_result()->fetch_assoc();
$profile_stmt->close();

// Ensure $profile is an array to prevent null access errors
if (!$profile) {
    $profile = [];
}



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --primary-dark: #3a0ca3;
            --secondary: #3f37c9;
            --accent: #f72585;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #ef233c;
            --dark: #1a1a1a;
            --light: #f8f9fa;
            --gray: #6c757d;
            --light-gray: #e9ecef;
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
        
        .main-content {
            padding: 30px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .dashboard-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .dashboard-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
            align-items: start;
        }
        
        .profile-sidebar {
            height: 100%;
        }
        
        .profile-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            color: var(--white);
            box-shadow: var(--card-shadow-hover);
            height: 100%;
            min-height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .profile-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            font-weight: 700;
            margin: 0 auto 30px;
            backdrop-filter: blur(10px);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            border: 3px solid rgba(255, 255, 255, 0.3);
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
        
        .admin-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .admin-role {
            opacity: 0.9;
            margin-bottom: 20px;
            font-size: 1rem;
        }
        
        .admin-badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            backdrop-filter: blur(10px);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }te-columns: 1fr 1fr;
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
        
        .dashboard-content {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .profile-details {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }
        
        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .detail-item {
            padding: 15px;
            background: var(--light);
            border-radius: 8px;
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .management-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .management-card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .management-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-5px);
            border-color: var(--primary-light);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .card-icon.students { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .card-icon.reports { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .card-icon.fees { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        .card-icon.roles { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
        .card-icon.income { background: linear-gradient(135deg, #fa709a, #fee140); color: white; }
        .card-icon.settings { background: linear-gradient(135deg, #a8edea, #fed6e3); color: var(--dark); }
        
        .card-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .card-description {
            font-size: 0.9rem;
            color: var(--gray);
            line-height: 1.5;
        }
        
        .profile-form {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
            display: none;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: var(--transition);
            background-color: var(--light);
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            background-color: var(--white);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-secondary {
            background-color: var(--gray);
            color: var(--white);
        }
        
        .btn-secondary:hover {
            background-color: var(--dark);
        }
        
        .alert-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid var(--success);
        }te-columns: 1fr 1fr;
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
        
        .dashboard-content {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .management-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .management-card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .management-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-5px);
            border-color: var(--primary-light);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .card-icon.students { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .card-icon.reports { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .card-icon.fees { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        .card-icon.roles { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
        .card-icon.income { background: linear-gradient(135deg, #fa709a, #fee140); color: white; }
        .card-icon.settings { background: linear-gradient(135deg, #a8edea, #fed6e3); color: var(--dark); }
        
        .card-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .card-description {
            font-size: 0.9rem;
            color: var(--gray);
            line-height: 1.5;
        }
        
        .profile-form {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: var(--transition);
            background-color: var(--light);
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            background-color: var(--white);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .alert-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid var(--success);
        }
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .card-icon.students { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .card-icon.reports { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .card-icon.fees { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        .card-icon.roles { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
        .card-icon.income { background: linear-gradient(135deg, #fa709a, #fee140); color: white; }
        .card-icon.settings { background: linear-gradient(135deg, #a8edea, #fed6e3); color: var(--dark); }
        
        .card-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .card-description {
            font-size: 0.9rem;
            color: var(--gray);
            line-height: 1.5;
        }
        
        @media (max-width: 1200px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }
            
            header {
                padding: 15px 20px;
            }
            
            .management-grid {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
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
            <a href="profile.php" class="active">Profile</a>
        </nav>
    </header>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Admin Profile</h1>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= $success_msg ?></span>
            </div>
        <?php endif; ?>

        <div class="dashboard-layout">
            <!-- Profile Sidebar -->
            <div class="profile-sidebar">
                <!-- Admin Profile Card -->
                <div class="profile-card">
                    <div class="profile-info">
                        <div class="profile-avatar" onclick="document.getElementById('imageUpload').click()">
                            <?php if (!empty($user['profile_image']) && file_exists('../../' . $user['profile_image'])): ?>
                                <img src="/ACADEXA/<?= htmlspecialchars($user['profile_image']) ?>" alt="Admin Profile">
                            <?php else: ?>
                                <span id="avatarLetter"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
                            <?php endif; ?>
                            <div class="upload-overlay">
                                <i class="fas fa-camera" style="color: white; font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <input type="file" id="imageUpload" accept="image/*">
                        <h2 class="admin-name"><?= htmlspecialchars($user['username']) ?></h2>
                        <p class="admin-role"><?= htmlspecialchars($user['email']) ?></p>
                        <div class="admin-badge">Administrator</div>
                    </div>
                    <a href="logout.php" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>


            </div>

            <!-- Profile Content -->
            <div class="dashboard-content">
                <!-- Profile Details View -->
                <div class="profile-details" id="profileDetails">
                    <div class="details-header">
                        <h3 class="card-title"><i class="fas fa-user"></i> Profile Information</h3>
                        <button class="btn btn-primary" onclick="toggleEdit()">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                    </div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value"><?= htmlspecialchars($profile['full_name'] ?? 'Not provided') ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Mobile Number</div>
                            <div class="detail-value"><?= htmlspecialchars($profile['phone'] ?? 'Not provided') ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date of Birth</div>
                            <div class="detail-value"><?= ($profile['dob'] ?? '') ? date('M j, Y', strtotime($profile['dob'])) : 'Not provided' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Gender</div>
                            <div class="detail-value"><?= htmlspecialchars($profile['gender'] ?? 'Not provided') ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Address</div>
                            <div class="detail-value"><?= htmlspecialchars($profile['address'] ?? 'Not provided') ?></div>
                        </div>
                        <div class="detail-item" style="grid-column: span 2;">
                            <div class="detail-label">Bio</div>
                            <div class="detail-value"><?= htmlspecialchars($profile['bio'] ?? 'No bio provided') ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Profile Edit Form -->
                <div class="profile-form" id="profileForm">
                    <div class="details-header">
                        <h3 class="card-title"><i class="fas fa-user-edit"></i> Edit Profile Information</h3>
                        <button type="button" class="btn btn-secondary" onclick="toggleEdit()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Mobile Number</label>
                                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="dob">Date of Birth</label>
                                <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($profile['dob'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?= ($profile['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= ($profile['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= ($profile['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="bio">Bio</label>
                                <textarea id="bio" name="bio"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="form-group full-width">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Management Services -->
                <div class="management-grid">
                    <!-- Student Management -->
                    <div class="management-card" onclick="window.location.href='../students.html'">
                        <div class="card-icon students">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="card-name">Student Management</h3>
                        <p class="card-description">Manage student records, profiles, enrollment, and academic information</p>
                    </div>

                    <!-- Reports & Analytics -->
                    <div class="management-card" onclick="window.location.href='reports.php'">
                        <div class="card-icon reports">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="card-name">Reports & Analytics</h3>
                        <p class="card-description">Generate academic reports, attendance analytics, and performance insights</p>
                    </div>

                    <!-- Fee Structure -->
                    <div class="management-card" onclick="window.location.href='fees.php'">
                        <div class="card-icon fees">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3 class="card-name">Fee Structure</h3>
                        <p class="card-description">Manage fee structures, payment tracking, and financial records</p>
                    </div>

                    <!-- Role Assignment -->
                    <div class="management-card" onclick="window.location.href='../faculty.html'">
                        <div class="card-icon roles">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 class="card-name">Faculty Management</h3>
                        <p class="card-description">Add and manage faculty members, assign roles and permissions</p>
                    </div>

                    <!-- Income Management -->
                    <div class="management-card" onclick="showIncomePasswordModal()">
                        <div class="card-icon income">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h3 class="card-name">Income Management</h3>
                        <p class="card-description">Track revenue, expenses, and financial performance metrics</p>
                    </div>

                    <!-- System Settings -->
                    <div class="management-card" onclick="window.location.href='settings.php'">
                        <div class="card-icon settings">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="card-name">System Settings</h3>
                        <p class="card-description">Configure system preferences, backup data, and security settings</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Income Password Modal -->
    <div id="incomePasswordModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; width: 400px; max-width: 90%; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <h3 style="margin-bottom: 20px; color: var(--primary);">Enter Password</h3>
            <p style="margin-bottom: 20px; color: var(--gray);">Please enter your password to access Income Management</p>
            <input type="password" id="incomePassword" placeholder="Enter your password" style="width: 100%; padding: 12px; border: 2px solid var(--light-gray); border-radius: 8px; margin-bottom: 20px; font-size: 16px; outline: none;">
            <div id="passwordError" style="color: var(--danger); margin-bottom: 15px; display: none;">Incorrect password. Please try again.</div>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button onclick="closeIncomePasswordModal()" style="padding: 10px 20px; background: var(--light-gray); color: var(--gray); border: none; border-radius: 8px; cursor: pointer;">Cancel</button>
                <button onclick="verifyIncomePassword()" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer;">Access</button>
            </div>
        </div>
    </div>

    <script>
    function toggleEdit() {
        const detailsView = document.getElementById('profileDetails');
        const formView = document.getElementById('profileForm');
        
        if (detailsView.style.display === 'none') {
            detailsView.style.display = 'block';
            formView.style.display = 'none';
        } else {
            detailsView.style.display = 'none';
            formView.style.display = 'block';
        }
    }
    
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('admin_id', '<?= $user_id ?>');
        
        fetch('upload_admin.php', {
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
                    img.alt = 'Admin Profile';
                    
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

    // Income Management Password Modal Functions
    function showIncomePasswordModal() {
        document.getElementById('incomePasswordModal').style.display = 'flex';
        document.getElementById('incomePassword').focus();
    }

    function closeIncomePasswordModal() {
        document.getElementById('incomePasswordModal').style.display = 'none';
        document.getElementById('incomePassword').value = '';
        document.getElementById('passwordError').style.display = 'none';
    }

    function verifyIncomePassword() {
        const password = document.getElementById('incomePassword').value;
        if (!password) {
            document.getElementById('passwordError').textContent = 'Please enter a password';
            document.getElementById('passwordError').style.display = 'block';
            return;
        }

        fetch('verify_income_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'password=' + encodeURIComponent(password)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'income.php';
            } else {
                document.getElementById('passwordError').textContent = data.message || 'Incorrect password';
                document.getElementById('passwordError').style.display = 'block';
                document.getElementById('incomePassword').value = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('passwordError').textContent = 'An error occurred. Please try again.';
            document.getElementById('passwordError').style.display = 'block';
        });
    }

    // Allow Enter key to submit password
    document.getElementById('incomePassword').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            verifyIncomePassword();
        }
    });
    </script>
</body>
</html>