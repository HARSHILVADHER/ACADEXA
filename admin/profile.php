<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
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
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $bio = trim($_POST['bio']);

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
$user_stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile | Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --secondary: #3f37c9;
            --danger: #f72585;
            --dark: #212529;
            --light: #f8f9fa;
            --gray: #6c757d;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
        }
        
        /* Header/Navigation */
        header {
            background: var(--white);
            box-shadow: var(--shadow);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo i {
            font-size: 1.3rem;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--gray);
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 6px;
            transition: var(--transition);
        }
        
        .nav-links a:hover {
            color: var(--primary);
            background: var(--primary-light);
        }
        
        .nav-links a.active {
            background: var(--primary-light);
            color: var(--primary);
        }
        
        /* Main Content */
        .main-content {
            padding: 100px 40px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .profile-title {
            font-size: 2rem;
            color: var(--primary);
            font-weight: 600;
        }
        
        .profile-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 40px;
            margin-bottom: 30px;
            transition: var(--transition);
        }
        
        .profile-card:hover {
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.15);
        }
        
        .user-info {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-item {
            flex: 1;
            min-width: 250px;
        }
        
        .info-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--dark);
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
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
        
        label.required::after {
            content: " *";
            color: var(--danger);
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
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
            min-height: 120px;
        }
        
        .btn-group {
            display: flex;
            justify-content: space-between;
            grid-column: span 2;
            margin-top: 20px;
        }
        
        .btn {
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: var(--white);
        }
        
        .btn-danger:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        /* Success Message */
        .alert-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: #4cc9f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #4cc9f0;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .btn-group {
                flex-direction: column;
                gap: 15px;
            }
            
            header {
                padding: 15px 20px;
            }
            
            .main-content {
                padding: 90px 20px 30px;
            }
        }
        
        @media (max-width: 480px) {
            .user-info {
                flex-direction: column;
                gap: 15px;
            }
            
            .profile-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>Acadexa</span>
        </div>
        <nav class="nav-links">
            <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
        </nav>
    </header>

    <div class="main-content">
        <div class="profile-header">
            <h1 class="profile-title"><i class="fas fa-user-circle"></i> Your Profile</h1>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= $success_msg ?></span>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <div class="user-info">
                <div class="info-item">
                    <span class="info-label"><i class="fas fa-user"></i> Username</span>
                    <div class="info-value"><?= htmlspecialchars($user['username']) ?></div>
                </div>
                <div class="info-item">
                    <span class="info-label"><i class="fas fa-envelope"></i> Email</span>
                    <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>

            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name" class="required">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="required">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>">
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
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Profile
                        </button>
                        <a href="login.html" class="btn btn-danger" onclick="return confirm('Are you sure you want to log out?');">
                            <i class="fas fa-sign-out-alt"></i> Log Out
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add animation to form elements on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const formGroups = document.querySelectorAll('.form-group');
            
            formGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                group.style.transition = 'all 0.5s ease ' + (index * 0.1) + 's';
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                });
                
                observer.observe(group);
            });
        });
    </script>
</body>
</html>