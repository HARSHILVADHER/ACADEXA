<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: ../login.html');
    exit();
}

$exam_id = $_GET['exam_id'] ?? null;
if (!$exam_id) {
    header('Location: all_exam.html');
    exit();
}

// Fetch exam details
$sql = "SELECT e.*, c.name AS class_name, c.code AS class_code FROM exam e 
        JOIN classes c ON e.code = c.code 
        WHERE e.id = ? AND e.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $exam_id, $user_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$exam) {
    header('Location: all_exam.html');
    exit();
}

// Fetch students from the class
$sql = "SELECT roll_no, name FROM students WHERE class_code = ? ORDER BY roll_no";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $exam['class_code']);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch existing marks if any
$existing_marks = [];
$sql = "SELECT student_roll_no, actual_marks FROM marks WHERE exam_name = ? AND class_code = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $exam['exam_name'], $exam['class_code'], $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $existing_marks[$row['student_roll_no']] = $row['actual_marks'];
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Marks - <?php echo htmlspecialchars($exam['exam_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f8fafc;
            color: #1a202c;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 40px;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2563eb;
        }
        nav {
            display: flex;
            gap: 20px;
        }
        nav a {
            text-decoration: none;
            color: #1a1a1a;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        nav a.active {
            background: #e0e7ff;
            color: #2563eb;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .exam-details {
            background: white;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .exam-details h2 {
            color: #2563eb;
            margin-bottom: 12px;
            font-size: 1.5rem;
        }
        .exam-details p {
            color: #374151;
            margin-bottom: 6px;
            font-size: 1rem;
        }
        .marks-table-container {
            background: white;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:hover {
            background: #f9fafb;
        }
        .marks-input {
            width: 80px;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            text-align: center;
        }
        .marks-input:focus {
            outline: none;
            border-color: #2563eb;
        }
        .marks-display {
            color: #6b7280;
            font-weight: 600;
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
        }
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        .error {
            border-color: #ef4444 !important;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Acadexa</div>
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="../createclass.html" class="active">Classes</a>
            <a href="../attendance.html">Attendance</a>
            <a href="gradecard.php">Reports</a>
            <a href="inquiry.php">Inquiries</a>
            <a href="profile.php">Settings</a>
        </nav>
    </header>
    
    <div class="container">
        <a href="../all_exam.html" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Exams</a>
        
        <div class="exam-details">
            <h2><?php echo htmlspecialchars($exam['exam_name']); ?></h2>
            <p><strong>Class:</strong> <?php echo htmlspecialchars($exam['class_name']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($exam['exam_date']); ?></p>
            <p><strong>Total Marks:</strong> <?php echo htmlspecialchars($exam['total_marks']); ?></p>
            <p><strong>Passing Marks:</strong> <?php echo htmlspecialchars($exam['passing_marks']); ?></p>
        </div>
        
        <div class="marks-table-container">
            <h3 style="margin-bottom: 20px; color: #1a202c;">Enter Student Marks</h3>
            
            <?php if (empty($students)): ?>
                <p style="color: #6b7280; text-align: center; padding: 40px 0;">No students found in this class.</p>
            <?php else: ?>
                <form id="marksForm">
                    <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                    <input type="hidden" name="exam_name" value="<?php echo htmlspecialchars($exam['exam_name']); ?>">
                    <input type="hidden" name="class_code" value="<?php echo htmlspecialchars($exam['class_code']); ?>">
                    <input type="hidden" name="exam_date" value="<?php echo htmlspecialchars($exam['exam_date']); ?>">
                    <input type="hidden" name="total_marks" value="<?php echo $exam['total_marks']; ?>">
                    <input type="hidden" name="passing_marks" value="<?php echo $exam['passing_marks']; ?>">
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Marks Obtained</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['roll_no']); ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td>
                                        <input type="number" 
                                               class="marks-input" 
                                               name="marks[<?php echo htmlspecialchars($student['roll_no']); ?>]"
                                               data-student-name="<?php echo htmlspecialchars($student['name']); ?>"
                                               min="0" 
                                               max="<?php echo $exam['total_marks']; ?>"
                                               value="<?php echo isset($existing_marks[$student['roll_no']]) ? $existing_marks[$student['roll_no']] : ''; ?>"
                                               placeholder="0">
                                        <span class="marks-display"> / <?php echo $exam['total_marks']; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Save Marks
                        </button>
                        <button type="button" class="btn btn-success" onclick="downloadSample()">
                            <i class="fa-solid fa-download"></i> Download Sample CSV
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('csvFile').click()">
                            <i class="fa-solid fa-upload"></i> Import Marks
                        </button>
                        <input type="file" id="csvFile" accept=".csv" style="display: none;" onchange="importCSV(event)">
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        const totalMarks = <?php echo $exam['total_marks']; ?>;
        
        // Validate marks input
        document.querySelectorAll('.marks-input').forEach(input => {
            input.addEventListener('input', function() {
                const value = parseInt(this.value);
                if (value > totalMarks) {
                    this.classList.add('error');
                    alert(`Marks cannot exceed ${totalMarks}`);
                    this.value = totalMarks;
                } else {
                    this.classList.remove('error');
                }
            });
        });
        
        // Save marks
        document.getElementById('marksForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('save_marks.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === 'success') {
                    alert('Marks saved successfully!');
                } else {
                    alert('Error saving marks: ' + data);
                }
            })
            .catch(err => {
                alert('Error saving marks.');
                console.error(err);
            });
        });
        
        // Download sample CSV
        function downloadSample() {
            const students = <?php echo json_encode($students); ?>;
            const className = '<?php echo addslashes($exam['class_name']); ?>';
            const examName = '<?php echo addslashes($exam['exam_name']); ?>';
            
            let csv = 'student_roll_no,student_name,actual_marks\n';
            
            students.forEach(student => {
                csv += `${student.roll_no},${student.name},\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${className}_${examName}_marks.csv`.replace(/[^a-zA-Z0-9_]/g, '_');
            a.click();
            window.URL.revokeObjectURL(url);
        }
        
        // Import CSV
        function importCSV(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const text = e.target.result;
                const lines = text.split('\n');
                
                // Skip header
                const dataLines = lines.slice(1).filter(line => line.trim());
                
                // Get current students
                const inputs = document.querySelectorAll('.marks-input');
                const studentMap = new Map();
                inputs.forEach(input => {
                    const rollNo = input.name.match(/\[(.*?)\]/)[1];
                    const studentName = input.getAttribute('data-student-name');
                    studentMap.set(rollNo, { input, studentName });
                });
                
                // Validate and import
                let errors = [];
                let imported = 0;
                
                dataLines.forEach((line, index) => {
                    const parts = line.split(',').map(p => p.trim());
                    if (parts.length < 2) return;
                    
                    const rollNo = parts[0];
                    const name = parts[1];
                    const marks = parts[2] || '';
                    
                    if (!studentMap.has(rollNo)) {
                        errors.push(`Row ${index + 2}: Student with Roll No ${rollNo} not found`);
                        return;
                    }
                    
                    const student = studentMap.get(rollNo);
                    
                    if (marks === '') {
                        return;
                    }
                    
                    const marksValue = parseInt(marks);
                    if (isNaN(marksValue) || marksValue < 0 || marksValue > totalMarks) {
                        errors.push(`Row ${index + 2}: Invalid marks ${marks} for ${name}`);
                        return;
                    }
                    
                    student.input.value = marksValue;
                    imported++;
                });
                
                if (errors.length > 0) {
                    alert('Import completed with errors:\n' + errors.join('\n'));
                } else {
                    alert(`Successfully imported marks for ${imported} students!`);
                }
                
                event.target.value = '';
            };
            
            reader.readAsText(file);
        }
    </script>
</body>
</html>
