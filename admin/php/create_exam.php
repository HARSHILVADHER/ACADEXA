<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<p style='text-align:center;color:red;'>User not authenticated!</p>";
    exit();
}
// Fetch classes for this user
$classes = [];
$sql = "SELECT id, name, code FROM classes WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$stmt->close();



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam</title>
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
        }
        /* Modern Form Styles */
        .main-content {
          display: flex;
          justify-content: center;
          align-items: flex-start;
          min-height: 80vh;
        }
        .container {
          background: #fff;
          border-radius: 16px;
          box-shadow: var(--card-shadow);
          max-width: 1000px;
          width: 100%;
          margin: 40px auto 0;
          padding: 32px 28px 28px 28px;
        }
        .container h1 {
          color: var(--primary);
          text-align: center;
          margin-bottom: 30px;
          font-size: 2rem;
          font-weight: 800;
        }
        .form-group {
          margin-bottom: 18px;
        }
        label {
          display: block;
          margin-bottom: 7px;
          font-weight: 600;
          color: var(--dark);
        }
        input, select, textarea {
          width: 100%;
          padding: 12px 14px;
          border: 1px solid #e0e0e0;
          border-radius: 8px;
          font-size: 1rem;
          background: var(--light);
          transition: var(--transition);
        }
        input:focus, select:focus, textarea:focus {
          outline: none;
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.12);
          background: var(--white);
        }
        .row {
          display: flex;
          gap: 16px;
        }
        .col-half {
          flex: 1;
        }
        button.submit-btn {
          background: var(--primary);
          color: #fff;
          border: none;
          padding: 14px 0;
          font-size: 1.1rem;
          font-weight: 700;
          border-radius: 8px;
          cursor: pointer;
          width: 100%;
          margin-top: 10px;
          transition: background 0.3s;
        }
        button.submit-btn:hover {
          background: var(--primary-dark);
        }
        @media (max-width: 600px) {
          .container {
            padding: 18px 6px 18px 6px;
          }
          .row {
            flex-direction: column;
            gap: 0;
          }
        }
    </style>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const classSelect = document.getElementById('class_code');
        const subjectSelect = document.getElementById('subject');
        
        classSelect.addEventListener('change', function() {
          const classCode = this.value;
          const className = this.options[this.selectedIndex].dataset.className;
          
          if (!classCode || !className) {
            subjectSelect.innerHTML = '<option value="">Select Class First</option>';
            subjectSelect.disabled = true;
            return;
          }
          
          fetch('get_subjects_by_class.php?class_name=' + encodeURIComponent(className))
            .then(response => response.json())
            .then(subjects => {
              subjectSelect.innerHTML = '<option value="">Select Subject</option>';
              if (subjects.length === 0) {
                subjectSelect.innerHTML = '<option value="">No subjects found</option>';
              } else {
                subjects.forEach(subject => {
                  const option = document.createElement('option');
                  option.value = subject.subject_code;
                  option.textContent = subject.subject_name;
                  subjectSelect.appendChild(option);
                });
              }
              subjectSelect.disabled = false;
            })
            .catch(error => {
              console.error('Error:', error);
              subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
              subjectSelect.disabled = false;
            });
        });
      });
    </script>
</head>
<body>
  <header>
    <?php include 'header_logo.php'; ?>
    <nav>
      <a href="dashboard.php">Home</a>
      <a href="../createclass.html" class="active">Classes</a>
      <a href="../attendance.html">Attendance</a>
      <a href="gradecard.php">Reports</a>
      <a href="inquiry.php">Inquiries</a>
      <a href="profile.php">Settings</a>
    </nav>
  </header>
  <main>
    <div class="main-content">
      <div class="container">
        <h1>Create New Exam</h1>
        <form action="submit_exam.php" method="post">
          <div class="form-group">
            <label for="class_code">Select Class</label>
            <select id="class_code" name="class_code" required>
              <option value="">Select Class</option>
              <?php foreach ($classes as $class): ?>
                <option value="<?php echo $class['code']; ?>" data-class-name="<?php echo htmlspecialchars($class['name']); ?>"><?php echo htmlspecialchars($class['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="exam_name">Exam Name</label>
            <input type="text" id="exam_name" name="exam_name" required>
          </div>
          <div class="form-group">
            <label for="subject">Select Subject</label>
            <select id="subject" name="subject" required disabled>
              <option value="">Select Class First</option>
            </select>
          </div>
          <div class="row">
            <div class="form-group col-half">
              <label for="exam_date">Exam Date</label>
              <input type="date" id="exam_date" name="exam_date" required>
            </div>
            <div class="form-group col-half">
              <label for="total_marks">Total Marks</label>
              <input type="number" id="total_marks" name="total_marks" min="1" required>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-half">
              <label for="start_time">Start Time</label>
              <input type="time" id="start_time" name="start_time" required>
            </div>
            <div class="form-group col-half">
              <label for="end_time">End Time</label>
              <input type="time" id="end_time" name="end_time" required>
            </div>
          </div>
          <div class="form-group">
            <label for="passing_marks">Passing Marks</label>
            <input type="number" id="passing_marks" name="passing_marks" min="0" required>
          </div>
          <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" placeholder="Any additional notes..."></textarea>
          </div>
          <button type="submit" class="submit-btn">Create Exam</button>
        </form>
      </div>
    </div>
  </main>
</body>
</html>