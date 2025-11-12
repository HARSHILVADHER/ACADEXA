<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --primary-dark: #3a0ca3;
            --white: #ffffff;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --dark: #1a1a1a;
            --success: #28a745;
            --danger: #dc3545;
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

        .settings-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }

        .settings-sidebar {
            background: var(--white);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .settings-menu {
            list-style: none;
        }

        .settings-menu li {
            margin-bottom: 5px;
        }

        .settings-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: var(--gray);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
        }

        .settings-menu a:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .settings-menu a.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
        }

        .settings-menu i {
            font-size: 1.1rem;
            width: 20px;
        }

        .settings-content {
            background: var(--white);
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--card-shadow);
        }

        .settings-section {
            display: none;
        }

        .settings-section.active {
            display: block;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .section-description {
            color: var(--gray);
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .setting-group {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--light-gray);
        }

        .setting-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .setting-item:last-child {
            margin-bottom: 0;
        }

        .setting-info h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .setting-info p {
            font-size: 0.9rem;
            color: var(--gray);
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--light-gray);
            transition: var(--transition);
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: var(--transition);
            border-radius: 50%;
        }

        .toggle-switch input:checked + .toggle-slider {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
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
            font-size: 0.95rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(67, 97, 238, 0.4);
        }

        .btn-danger {
            padding: 12px 30px;
            background: var(--danger);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            font-size: 0.95rem;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
        }

        .color-picker-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .color-picker {
            width: 60px;
            height: 40px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            cursor: pointer;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .crop-container {
            max-width: 100%;
            max-height: 400px;
            margin: 20px 0;
        }

        .crop-container img {
            max-width: 100%;
        }

        .crop-options {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            justify-content: center;
        }

        .crop-btn {
            padding: 10px 20px;
            border: 2px solid var(--primary);
            background: var(--white);
            color: var(--primary);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .crop-btn.active {
            background: var(--primary);
            color: var(--white);
        }

        .crop-btn:hover {
            background: var(--primary);
            color: var(--white);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: var(--white);
            margin: 10% auto;
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--card-shadow-hover);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: var(--gray);
        }

        .close:hover {
            color: var(--dark);
        }

        .upload-area {
            border: 2px dashed var(--light-gray);
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .upload-area:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .upload-area i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        @media (max-width: 968px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }

            .settings-sidebar {
                position: static;
            }

            .settings-menu {
                display: flex;
                overflow-x: auto;
                gap: 10px;
            }

            .settings-menu li {
                margin-bottom: 0;
            }

            .settings-menu a {
                white-space: nowrap;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            nav {
                width: 100%;
                overflow-x: auto;
            }

            .setting-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Settings</h1>
            <p class="page-subtitle">Manage your application preferences and configurations</p>
        </div>

        <div class="settings-grid">
            <div class="settings-sidebar">
                <ul class="settings-menu">
                    <li><a href="#branding" class="active" onclick="switchSection('branding')"><i class="fas fa-image"></i> Branding</a></li>
                    <li><a href="#general" onclick="switchSection('general')"><i class="fas fa-cog"></i> General</a></li>
                    <li><a href="#notifications" onclick="switchSection('notifications')"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li><a href="#appearance" onclick="switchSection('appearance')"><i class="fas fa-palette"></i> Appearance</a></li>
                    <li><a href="#security" onclick="switchSection('security')"><i class="fas fa-shield-alt"></i> Security</a></li>
                    <li><a href="#backup" onclick="switchSection('backup')"><i class="fas fa-database"></i> Backup</a></li>
                    <li><a href="#advanced" onclick="switchSection('advanced')"><i class="fas fa-sliders-h"></i> Advanced</a></li>
                </ul>
            </div>

            <div class="settings-content">
                <div id="branding" class="settings-section active">
                    <h2 class="section-title">Branding Settings</h2>
                    <p class="section-description">Customize your institute's branding</p>

                    <div class="setting-group">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Logo</h3>
                        <p style="color: var(--gray); margin-bottom: 15px;">Upload your institute logo (JPG format only, max 2MB)</p>
                        <button class="btn-primary" onclick="openLogoModal()"><i class="fas fa-upload"></i> Upload Logo</button>
                        <div id="currentLogo" style="margin-top: 20px;"></div>
                    </div>
                </div>

                <div id="general" class="settings-section">
                    <h2 class="section-title">General Settings</h2>
                    <p class="section-description">Configure basic application settings</p>
                    <div class="setting-group">
                        <div class="form-group">
                            <label>Institute Name</label>
                            <input type="text" class="input-field" placeholder="Enter institute name" value="Acadexa Institute">
                        </div>
                    </div>
                    <button class="btn-primary">Save Changes</button>
                </div>

                <div id="notifications" class="settings-section">
                    <h2 class="section-title">Notification Settings</h2>
                    <p class="section-description">Manage how you receive notifications</p>
                    <button class="btn-primary">Save Changes</button>
                </div>

                <div id="appearance" class="settings-section">
                    <h2 class="section-title">Appearance Settings</h2>
                    <p class="section-description">Customize the look and feel</p>
                    <button class="btn-primary">Save Changes</button>
                </div>

                <div id="security" class="settings-section">
                    <h2 class="section-title">Security Settings</h2>
                    <p class="section-description">Manage your account security</p>
                    <button class="btn-primary">Update Password</button>
                </div>

                <div id="backup" class="settings-section">
                    <h2 class="section-title">Backup & Restore</h2>
                    <p class="section-description">Manage your data backups</p>
                    <button class="btn-primary"><i class="fas fa-download"></i> Create Backup Now</button>
                </div>

                <div id="advanced" class="settings-section">
                    <h2 class="section-title">Advanced Settings</h2>
                    <p class="section-description">Advanced configuration options</p>
                    <button class="btn-danger"><i class="fas fa-trash"></i> Clear All Cache</button>
                </div>
            </div>
        </div>
    </div>

    <div id="logoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Upload Logo</h3>
                <span class="close" onclick="closeLogoModal()">&times;</span>
            </div>
            <div class="upload-area" id="uploadArea" onclick="document.getElementById('logoInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Click to upload logo (JPG only)</p>
                <p style="font-size: 0.85rem; color: var(--gray); margin-top: 10px;">Maximum file size: 2MB</p>
            </div>
            <input type="file" id="logoInput" accept="image/jpeg,image/jpg" style="display: none;" onchange="previewLogo(event)">
            <div id="cropSection" style="display: none;">
                <div class="crop-options">
                    <button class="crop-btn active" onclick="setCropShape('rectangle')" id="rectBtn">
                        <i class="fas fa-square"></i> Rectangle
                    </button>
                    <button class="crop-btn" onclick="setCropShape('circle')" id="circleBtn">
                        <i class="fas fa-circle"></i> Circle
                    </button>
                </div>
                <div class="crop-container">
                    <img id="cropImage" />
                </div>
            </div>
            <button class="btn-primary" id="uploadBtn" style="width: 100%; margin-top: 20px; display: none;" onclick="uploadLogo()">Upload Cropped Logo</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropper = null;
        let cropShape = 'rectangle';

        function switchSection(sectionId) {
            document.querySelectorAll('.settings-section').forEach(section => {
                section.classList.remove('active');
            });
            document.querySelectorAll('.settings-menu a').forEach(link => {
                link.classList.remove('active');
            });
            document.getElementById(sectionId).classList.add('active');
            document.querySelector(`a[href="#${sectionId}"]`).classList.add('active');
            event.preventDefault();
        }

        function openLogoModal() {
            document.getElementById('logoModal').style.display = 'block';
        }

        function closeLogoModal() {
            document.getElementById('logoModal').style.display = 'none';
            document.getElementById('logoInput').value = '';
            document.getElementById('uploadArea').style.display = 'block';
            document.getElementById('cropSection').style.display = 'none';
            document.getElementById('uploadBtn').style.display = 'none';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }

        function previewLogo(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('File size exceeds 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('uploadArea').style.display = 'none';
                document.getElementById('cropSection').style.display = 'block';
                document.getElementById('uploadBtn').style.display = 'block';
                
                const cropImage = document.getElementById('cropImage');
                cropImage.src = e.target.result;
                
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(cropImage, {
                    aspectRatio: NaN,
                    viewMode: 1,
                    autoCropArea: 0.8,
                    responsive: true,
                    background: false
                });
            };
            reader.readAsDataURL(file);
        }

        function setCropShape(shape) {
            cropShape = shape;
            document.getElementById('rectBtn').classList.toggle('active', shape === 'rectangle');
            document.getElementById('circleBtn').classList.toggle('active', shape === 'circle');
            
            if (cropper) {
                if (shape === 'circle') {
                    cropper.setAspectRatio(1);
                } else {
                    cropper.setAspectRatio(NaN);
                }
            }
        }

        function uploadLogo() {
            if (!cropper) {
                alert('Please select an image first');
                return;
            }
            
            const canvas = cropper.getCroppedCanvas({
                maxWidth: 500,
                maxHeight: 500
            });
            
            canvas.toBlob(function(blob) {
                const formData = new FormData();
                formData.append('logo', blob, 'logo.jpg');
                formData.append('shape', cropShape);
                
                fetch('upload_logo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('Response text:', text);
                    const result = JSON.parse(text);
                    if (result.success) {
                        alert('Logo uploaded successfully!');
                        closeLogoModal();
                        location.reload();
                    } else {
                        alert('Upload failed: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Upload failed: ' + error.message);
                });
            }, 'image/jpeg', 0.9);
        }

        function loadLogo() {
            fetch('get_logo.php')
                .then(response => response.json())
                .then(result => {
                    const logoDiv = document.querySelector('.logo');
                    const currentLogoDiv = document.getElementById('currentLogo');
                    
                    if (result.logo_path && result.shape) {
                        const borderRadius = result.shape === 'circle' ? '50%' : '0';
                        logoDiv.innerHTML = `<img src="../../${result.logo_path}" alt="Logo" style="border-radius: ${borderRadius};">`;
                        if (currentLogoDiv) {
                            currentLogoDiv.innerHTML = `<p style="color: var(--gray); margin-bottom: 10px;">Current Logo (${result.shape}):</p><img src="../../${result.logo_path}" style="max-width: 200px; max-height: 80px; object-fit: contain; border-radius: ${borderRadius};">`;
                        }
                    }
                });
        }

        window.onclick = function(event) {
            const modal = document.getElementById('logoModal');
            if (event.target == modal) {
                closeLogoModal();
            }
        };

        window.onload = function() {
            loadLogo();
        };
    </script>
</body>
</html>
