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

// Create fees_structure table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS fees_structure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    student_roll_no VARCHAR(50),
    class_code VARCHAR(50) NOT NULL,
    decided_fees DECIMAL(10,2) NOT NULL,
    installments TEXT,
    notes TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student_user (student_id, user_id),
    INDEX idx_user_class (user_id, class_code)
)";
$conn->query($create_table_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_fees'])) {
    $selected_students = $_POST['selected_students'] ?? [];
    $decided_fees = floatval($_POST['decided_fees'] ?? 0);
    $installment_amounts = $_POST['installment_amount'] ?? [];
    $installment_dates = $_POST['installment_date'] ?? [];
    $notes = trim($_POST['notes'] ?? '');

    // Validate installment total matches decided fees
    $installment_total = 0;
    for ($i = 0; $i < count($installment_amounts); $i++) {
        if (!empty($installment_amounts[$i])) {
            $installment_total += floatval($installment_amounts[$i]);
        }
    }
    
    if (!empty($selected_students) && $decided_fees > 0 && abs($decided_fees - $installment_total) < 0.01) {
        // Prepare installments JSON
        $installments = [];
        for ($i = 0; $i < count($installment_amounts); $i++) {
            if (!empty($installment_amounts[$i]) && !empty($installment_dates[$i])) {
                $installments[] = [
                    'amount' => floatval($installment_amounts[$i]),
                    'due_date' => $installment_dates[$i]
                ];
            }
        }
        $installments_json = json_encode($installments);

        $success_count = 0;
        foreach ($selected_students as $student_id) {
            // Get student details
            $student_stmt = $conn->prepare("SELECT s.name, s.class_code, s.roll_no FROM students s WHERE s.id = ? AND s.user_id = ?");
            $student_stmt->bind_param("ii", $student_id, $user_id);
            $student_stmt->execute();
            $student = $student_stmt->get_result()->fetch_assoc();
            $student_stmt->close();

            if ($student) {
                // Check if fees already exists
                $check_stmt = $conn->prepare("SELECT id FROM fees_structure WHERE student_id = ? AND user_id = ?");
                $check_stmt->bind_param("ii", $student_id, $user_id);
                $check_stmt->execute();
                $exists = $check_stmt->get_result();

                if ($exists->num_rows > 0) {
                    // Update existing
                    $update_stmt = $conn->prepare("UPDATE fees_structure SET decided_fees = ?, installments = ?, notes = ?, updated_at = NOW() WHERE student_id = ? AND user_id = ?");
                    $update_stmt->bind_param("dssii", $decided_fees, $installments_json, $notes, $student_id, $user_id);
                    if ($update_stmt->execute()) {
                        $success_count++;
                    }
                    $update_stmt->close();
                } else {
                    // Insert new
                    $insert_stmt = $conn->prepare("INSERT INTO fees_structure (student_id, student_name, student_roll_no, class_code, decided_fees, installments, notes, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $insert_stmt->bind_param("isssdssi", $student_id, $student['name'], $student['roll_no'], $student['class_code'], $decided_fees, $installments_json, $notes, $user_id);
                    if ($insert_stmt->execute()) {
                        $success_count++;
                    }
                    $insert_stmt->close();
                }
                $check_stmt->close();
            }
        }
        if ($success_count > 0) {
            $success_msg = "Fees structure saved successfully for $success_count student(s)!";
        } else {
            $success_msg = "Error: Could not save fees structure. Please try again.";
        }
    } else if (!empty($selected_students) && $decided_fees > 0) {
        $success_msg = "Error: Installment amounts total (₹" . number_format($installment_total, 2) . ") must match decided fees (₹" . number_format($decided_fees, 2) . ").";
    } else {
        $success_msg = "Error: Please select students and enter valid fees amount.";
    }
}

// Get all classes from both students and classes tables
$classes = [];
$classes_stmt = $conn->prepare("
    SELECT DISTINCT class_code 
    FROM (
        SELECT DISTINCT class_code FROM students WHERE user_id = ? AND class_code IS NOT NULL AND class_code != ''
        UNION
        SELECT DISTINCT code as class_code FROM classes WHERE user_id = ?
    ) AS all_classes 
    ORDER BY class_code
");
$classes_stmt->bind_param("ii", $user_id, $user_id);
$classes_stmt->execute();
$classes_result = $classes_stmt->get_result();
while ($row = $classes_result->fetch_assoc()) {
    $classes[] = $row;
}
$classes_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Structure | Acadexa</title>
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
        
        .logo img {
            height: 40px;
            width: auto;
            object-fit: contain;
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
        
        .container {
            padding: 30px 40px;
            max-width: 1800px;
            margin: 0 auto;
        }
        
        h1 {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        p.subtext {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 30px;
        }
        
        .fees-container {
            display: grid;
            gap: 24px;
        }
        
        .card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            position: relative;
            padding-left: 15px;
            margin-bottom: 20px;
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
        
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        select, input, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--white);
            font-family: 'Inter', sans-serif;
        }
        
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .students-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .student-card {
            background: var(--light);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid var(--light-gray);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .student-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
        }
        
        .student-card.selected {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        
        .student-card.has-fees {
            background: rgba(76, 201, 240, 0.1);
            border-color: var(--success);
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .student-card.has-fees:hover {
            transform: none;
            box-shadow: none;
            border-color: var(--success);
        }
        
        .student-card.has-fees .student-info h4::after {
            content: ' ✓';
            color: var(--success);
            font-weight: bold;
        }
        
        .student-card.has-fees .student-info p::after {
            content: ' • Fees Already Set';
            color: var(--success);
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .student-checkbox {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .student-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
        }
        
        .student-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .student-info p {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .installments-container {
            border: 1px dashed var(--light-gray);
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
            background: var(--light);
        }
        
        .installment-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
            margin-bottom: 15px;
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
            background: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-secondary {
            background: var(--primary-light);
            color: var(--primary);
        }
        
        .btn-secondary:hover {
            background: var(--primary);
            color: var(--white);
        }
        
        .btn-danger {
            background: var(--danger);
            color: var(--white);
            padding: 8px 12px;
        }
        
        .btn-danger:hover {
            background: #e53e3e;
        }
        
        .alert-success {
            background: rgba(76, 201, 240, 0.1);
            color: var(--success);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid var(--success);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            header {
                padding: 15px 20px;
            }
            
            .students-grid {
                grid-template-columns: 1fr;
            }
            
            .installment-row {
                grid-template-columns: 1fr;
                gap: 10px;
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
        <h1>Fees Structure</h1>
        <p class="subtext">Manage student fees and installment plans</p>

        <?php if ($success_msg): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= $success_msg ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="fees-container">
                <!-- Class Selection -->
                <div class="card">
                    <h3 class="card-title"><i class="fas fa-graduation-cap"></i> Select Class</h3>
                    <div class="form-group">
                        <label for="class_select">Choose Class/Batch</label>
                        <select id="class_select" onchange="loadStudents(this.value)" required>
                            <option value="">Select a class...</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= htmlspecialchars($class['class_code']) ?>"><?= htmlspecialchars($class['class_code']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="students_container" style="display: none;">
                        <h4 style="margin: 20px 0 15px 0; color: var(--dark);">Select Students:</h4>
                        <div id="students_list" class="students-grid"></div>
                    </div>
                </div>

                <!-- Fees Structure -->
                <div class="card">
                    <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Fees Structure</h3>
                    
                    <div class="form-group">
                        <label for="decided_fees">Total Decided Fees (₹)</label>
                        <input type="number" id="decided_fees" name="decided_fees" step="0.01" required onchange="validateTotal()">
                    </div>

                    <div class="form-group">
                        <label>Installments</label>
                        <div class="installments-container">
                            <div id="installments_list">
                                <div class="installment-row">
                                    <div>
                                        <label>Amount (₹)</label>
                                        <input type="number" name="installment_amount[]" step="0.01" required oninput="validateTotal()">
                                    </div>
                                    <div>
                                        <label>Due Date</label>
                                        <input type="date" name="installment_date[]" required>
                                    </div>
                                    <button type="button" class="btn btn-danger" onclick="removeInstallment(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="addInstallment()" id="addInstallmentBtn">
                                <i class="fas fa-plus"></i> Add Installment
                            </button>
                            <p style="font-size: 0.85rem; color: var(--gray); margin-top: 10px;">Maximum 12 installments allowed</p>
                            <div id="totalValidation" style="margin-top: 15px; padding: 10px; border-radius: 8px; display: none;">
                                <p id="totalMessage" style="margin: 0; font-weight: 600;"></p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" placeholder="Additional notes or instructions..."></textarea>
                    </div>

                    <button type="submit" name="save_fees" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Fees Structure
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
    function loadStudents(classCode) {
        if (!classCode) {
            document.getElementById('students_container').style.display = 'none';
            return;
        }

        fetch('get_students_by_class.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'class_code=' + encodeURIComponent(classCode)
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('students_list');
            container.innerHTML = '';
            
            if (data.success && data.students.length > 0) {
                data.students.forEach(student => {
                    const studentCard = document.createElement('div');
                    studentCard.className = 'student-card';
                    
                    if (student.has_fees == 1) {
                        studentCard.classList.add('has-fees');
                        studentCard.innerHTML = `
                            <div class="student-checkbox">
                                <div class="student-info">
                                    <h4>${student.name}</h4>
                                    <p>Class: ${student.class_code} | ID: ${student.id}</p>
                                </div>
                            </div>
                        `;
                    } else {
                        studentCard.onclick = (e) => {
                            if (e.target.type !== 'checkbox') {
                                toggleStudent(studentCard, student.id);
                            }
                        };
                        
                        studentCard.innerHTML = `
                            <div class="student-checkbox">
                                <input type="checkbox" name="selected_students[]" value="${student.id}" onchange="toggleStudentCard(this)">
                                <div class="student-info">
                                    <h4>${student.name}</h4>
                                    <p>Class: ${student.class_code} | ID: ${student.id}</p>
                                </div>
                            </div>
                        `;
                    }
                    
                    container.appendChild(studentCard);
                });
                document.getElementById('students_container').style.display = 'block';
            } else {
                container.innerHTML = '<p style="text-align: center; color: var(--gray);">No students found in this class.</p>';
                document.getElementById('students_container').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading students');
        });
    }

    function toggleStudent(card, studentId) {
        const checkbox = card.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        toggleStudentCard(checkbox);
    }

    function toggleStudentCard(checkbox) {
        const card = checkbox.closest('.student-card');
        if (checkbox.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    }

    function addInstallment() {
        const container = document.getElementById('installments_list');
        const currentCount = container.children.length;
        
        if (currentCount >= 12) {
            alert('Maximum 12 installments allowed');
            return;
        }
        
        const newRow = document.createElement('div');
        newRow.className = 'installment-row';
        newRow.innerHTML = `
            <div>
                <label>Amount (₹)</label>
                <input type="number" name="installment_amount[]" step="0.01" required oninput="validateTotal()">
            </div>
            <div>
                <label>Due Date</label>
                <input type="date" name="installment_date[]" required>
            </div>
            <button type="button" class="btn btn-danger" onclick="removeInstallment(this)">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newRow);
        setDateRestrictions(newRow.querySelector('input[type="date"]'));
        updateAddButton();
        validateTotal();
    }

    function removeInstallment(button) {
        const row = button.closest('.installment-row');
        const container = document.getElementById('installments_list');
        if (container.children.length > 1) {
            row.remove();
            updateAddButton();
            validateTotal();
        } else {
            alert('At least one installment is required');
        }
    }
    
    function updateAddButton() {
        const container = document.getElementById('installments_list');
        const addBtn = document.getElementById('addInstallmentBtn');
        const currentCount = container.children.length;
        
        if (currentCount >= 12) {
            addBtn.style.display = 'none';
        } else {
            addBtn.style.display = 'inline-flex';
        }
    }
    
    function setDateRestrictions(dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const minDate = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', minDate);
    }
    
    function validateTotal() {
        const decidedFees = parseFloat(document.getElementById('decided_fees').value) || 0;
        const installmentInputs = document.querySelectorAll('input[name="installment_amount[]"]');
        let totalInstallments = 0;
        
        installmentInputs.forEach(input => {
            totalInstallments += parseFloat(input.value) || 0;
        });
        
        const validationDiv = document.getElementById('totalValidation');
        const messageP = document.getElementById('totalMessage');
        const submitBtn = document.querySelector('button[name="save_fees"]');
        
        if (decidedFees > 0 && totalInstallments > 0) {
            const difference = Math.abs(decidedFees - totalInstallments);
            
            if (difference < 0.01) {
                validationDiv.style.display = 'block';
                validationDiv.style.background = 'rgba(76, 201, 240, 0.1)';
                validationDiv.style.border = '1px solid var(--success)';
                messageP.style.color = 'var(--success)';
                messageP.innerHTML = '<i class="fas fa-check-circle"></i> Total matches! Decided Fees: ₹' + decidedFees.toFixed(2) + ' = Installments: ₹' + totalInstallments.toFixed(2);
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            } else {
                validationDiv.style.display = 'block';
                validationDiv.style.background = 'rgba(239, 35, 60, 0.1)';
                validationDiv.style.border = '1px solid var(--danger)';
                messageP.style.color = 'var(--danger)';
                messageP.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Total mismatch! Decided Fees: ₹' + decidedFees.toFixed(2) + ' ≠ Installments: ₹' + totalInstallments.toFixed(2) + ' (Difference: ₹' + difference.toFixed(2) + ')';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
            }
        } else {
            validationDiv.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }
    }
    
    // Set date restrictions on page load
    document.addEventListener('DOMContentLoaded', function() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(setDateRestrictions);
        updateAddButton();
        validateTotal();
    });
    </script>
</body>
</html>