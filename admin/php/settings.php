<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
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
        <div class="logo">Acadexa</div>
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
                    <li><a href="#general" class="active" onclick="switchSection('general')"><i class="fas fa-cog"></i> General</a></li>
                    <li><a href="#notifications" onclick="switchSection('notifications')"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li><a href="#appearance" onclick="switchSection('appearance')"><i class="fas fa-palette"></i> Appearance</a></li>
                    <li><a href="#security" onclick="switchSection('security')"><i class="fas fa-shield-alt"></i> Security</a></li>
                    <li><a href="#backup" onclick="switchSection('backup')"><i class="fas fa-database"></i> Backup</a></li>
                    <li><a href="#advanced" onclick="switchSection('advanced')"><i class="fas fa-sliders-h"></i> Advanced</a></li>
                </ul>
            </div>

            <div class="settings-content">
                <!-- General Settings -->
                <div id="general" class="settings-section active">
                    <h2 class="section-title">General Settings</h2>
                    <p class="section-description">Configure basic application settings</p>

                    <div class="setting-group">
                        <div class="form-group">
                            <label>Institute Name</label>
                            <input type="text" class="input-field" placeholder="Enter institute name" value="Acadexa Institute">
                        </div>
                        <div class="form-group">
                            <label>Institute Email</label>
                            <input type="email" class="input-field" placeholder="Enter email address" value="info@acadexa.com">
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="tel" class="input-field" placeholder="Enter contact number" value="+91 1234567890">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="input-field" rows="3" placeholder="Enter institute address">123 Education Street, City, State - 123456</textarea>
                        </div>
                    </div>

                    <div class="setting-group">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Academic Year</h3>
                        <div class="form-group">
                            <label>Current Academic Year</label>
                            <input type="text" class="input-field" placeholder="e.g., 2025-2026" value="2025-2026">
                        </div>
                    </div>

                    <button class="btn-primary">Save Changes</button>
                </div>

                <!-- Notifications Settings -->
                <div id="notifications" class="settings-section">
                    <h2 class="section-title">Notification Settings</h2>
                    <p class="section-description">Manage how you receive notifications</p>

                    <div class="setting-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Email Notifications</h4>
                                <p>Receive notifications via email</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Fee Payment Alerts</h4>
                                <p>Get notified when fees are paid</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Attendance Alerts</h4>
                                <p>Receive daily attendance summaries</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Exam Reminders</h4>
                                <p>Get reminders for upcoming exams</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Birthday Notifications</h4>
                                <p>Receive birthday reminders for students</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <button class="btn-primary">Save Changes</button>
                </div>

                <!-- Appearance Settings -->
                <div id="appearance" class="settings-section">
                    <h2 class="section-title">Appearance Settings</h2>
                    <p class="section-description">Customize the look and feel of your application</p>

                    <div class="setting-group">
                        <div class="form-group">
                            <label>Theme Color</label>
                            <div class="color-picker-wrapper">
                                <input type="color" class="color-picker" value="#4361ee">
                                <span style="color: var(--gray);">Primary color for the application</span>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Dark Mode</h4>
                                <p>Enable dark theme for the application</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Date Format</label>
                            <select class="input-field">
                                <option>DD-MM-YYYY</option>
                                <option>MM-DD-YYYY</option>
                                <option>YYYY-MM-DD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Time Format</label>
                            <select class="input-field">
                                <option>12 Hour (AM/PM)</option>
                                <option>24 Hour</option>
                            </select>
                        </div>
                    </div>

                    <button class="btn-primary">Save Changes</button>
                </div>

                <!-- Security Settings -->
                <div id="security" class="settings-section">
                    <h2 class="section-title">Security Settings</h2>
                    <p class="section-description">Manage your account security and privacy</p>

                    <div class="setting-group">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Change Password</h3>
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="input-field" placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="input-field" placeholder="Enter new password">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="input-field" placeholder="Confirm new password">
                        </div>
                        <button class="btn-primary">Update Password</button>
                    </div>

                    <div class="setting-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Two-Factor Authentication</h4>
                                <p>Add an extra layer of security to your account</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Session Timeout</h4>
                                <p>Auto logout after 30 minutes of inactivity</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Backup Settings -->
                <div id="backup" class="settings-section">
                    <h2 class="section-title">Backup & Restore</h2>
                    <p class="section-description">Manage your data backups</p>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Last backup: Never. It's recommended to backup your data regularly.</span>
                    </div>

                    <div class="setting-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Automatic Backups</h4>
                                <p>Enable automatic daily backups</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Backup Frequency</label>
                            <select class="input-field">
                                <option>Daily</option>
                                <option>Weekly</option>
                                <option>Monthly</option>
                            </select>
                        </div>
                    </div>

                    <div class="setting-group">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Manual Backup</h3>
                        <button class="btn-primary"><i class="fas fa-download"></i> Create Backup Now</button>
                    </div>

                    <div class="setting-group">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Restore Data</h3>
                        <div class="form-group">
                            <label>Upload Backup File</label>
                            <input type="file" class="input-field" accept=".sql,.zip">
                        </div>
                        <button class="btn-primary"><i class="fas fa-upload"></i> Restore Backup</button>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div id="advanced" class="settings-section">
                    <h2 class="section-title">Advanced Settings</h2>
                    <p class="section-description">Advanced configuration options</p>

                    <div class="setting-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Debug Mode</h4>
                                <p>Enable debug mode for troubleshooting</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Maintenance Mode</h4>
                                <p>Put the application in maintenance mode</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Items Per Page</label>
                            <select class="input-field">
                                <option>10</option>
                                <option selected>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                        </div>
                    </div>

                    <div class="setting-group">
                        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: var(--danger);">Danger Zone</h3>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>These actions are irreversible. Please proceed with caution.</span>
                        </div>
                        <button class="btn-danger"><i class="fas fa-trash"></i> Clear All Cache</button>
                        <button class="btn-danger" style="margin-left: 10px;"><i class="fas fa-sync"></i> Reset to Defaults</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchSection(sectionId) {
            // Remove active class from all sections and menu items
            document.querySelectorAll('.settings-section').forEach(section => {
                section.classList.remove('active');
            });
            document.querySelectorAll('.settings-menu a').forEach(link => {
                link.classList.remove('active');
            });

            // Add active class to selected section and menu item
            document.getElementById(sectionId).classList.add('active');
            document.querySelector(`a[href="#${sectionId}"]`).classList.add('active');

            // Prevent default anchor behavior
            event.preventDefault();
        }
    </script>
</body>
</html>
