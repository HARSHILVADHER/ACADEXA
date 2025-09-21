<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<p style='text-align:center;color:red;'>User not authenticated!</p>";
    exit();
}

// Fetch all exams for this user
$exams = [];
$sql = "SELECT e.*, c.name AS class_name FROM exam e 
        JOIN classes c ON e.code = c.code 
        WHERE e.user_id = ? ORDER BY e.exam_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $exams[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Exam</title>
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

    .exam-row {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      padding: 30px 40px;
      margin-top: 30px;
    }

    .exam-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 16px rgba(66,141,242,0.10);
      min-width: 270px;
      max-width: 300px;
      padding: 24px 20px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      transition: box-shadow 0.2s;
      position: relative;
    }
    .exam-card:hover {
      box-shadow: 0 8px 24px rgba(66,141,242,0.18);
    }
    .exam-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: #2563eb;
      margin-bottom: 8px;
    }
    .exam-class {
      font-size: 1rem;
      color: #374151;
      margin-bottom: 8px;
    }
    .exam-info {
      font-size: 0.97rem;
      color: #444;
      margin-bottom: 4px;
    }
    .exam-notes {
      font-size: 0.95rem;
      color: #6b7280;
      margin-top: 10px;
    }
    .no-exams {
      text-align: center;
      color: #6b7280;
      padding: 40px 0;
      font-size: 1.1rem;
    }
    </style>
</head>
<body>
    <header>
    <div class="logo">Acadexa</div>
    <nav>
      <a href="dashboard.php">Home</a>
      <a href="createclass.html" class="active">Classes</a>
      <a href="attendance.html">Attendance</a>
      <a href="gradecard.php">Reports</a>
      <a href="inquiry.php">Inquiries</a>
      <a href="profile.php">Settings</a>
    </nav>
  </header>
  <div class="exam-row">
    <?php if (empty($exams)): ?>
      <div class="no-exams">No exams found.</div>
    <?php else: ?>
      <?php foreach ($exams as $exam): ?>
        <div class="exam-card" style="position:relative;">
          <div class="exam-title"><?php echo htmlspecialchars($exam['exam_name']); ?></div>
          <div class="exam-class">Class: <?php echo htmlspecialchars($exam['class_name']); ?></div>
          <div class="exam-info"><strong>Date:</strong> <?php echo htmlspecialchars($exam['exam_date']); ?></div>
          <div class="exam-info"><strong>Time:</strong> <?php echo htmlspecialchars($exam['start_time']); ?> - <?php echo htmlspecialchars($exam['end_time']); ?></div>
          <div class="exam-info"><strong>Total Marks:</strong> <?php echo htmlspecialchars($exam['total_marks']); ?></div>
          <div class="exam-info"><strong>Passing Marks:</strong> <?php echo htmlspecialchars($exam['passing_marks']); ?></div>
          <?php if (!empty($exam['notes'])): ?>
            <div class="exam-notes"><strong>Notes:</strong> <?php echo htmlspecialchars($exam['notes']); ?></div>
          <?php endif; ?>
          <!-- Edit Button -->
          <button class="edit-btn" title="Edit Exam" style="position:absolute;bottom:12px;right:52px;background:none;border:none;cursor:pointer;">
            <i class="fa-regular fa-pen-to-square" style="color:#2563eb;font-size:20px;"></i>
          </button>
          <!-- Delete Button -->
          <button class="delete-btn" onclick="deleteExam(<?php echo $exam['id']; ?>, this)" title="Delete Exam" style="position:absolute;bottom:12px;right:12px;background:none;border:none;cursor:pointer;">
            <i class="fa-solid fa-trash" style="color:#e11d48;font-size:20px;"></i>
          </button>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <script>
function deleteExam(examId, btn) {
  if (!confirm('Are you sure you want to delete this exam?')) return;
  btn.disabled = true;
  fetch('delete_exam.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'exam_id=' + encodeURIComponent(examId)
  })
  .then(res => res.text())
  .then(data => {
    if (data.trim() === 'success') {
      btn.closest('.exam-card').remove();
    } else {
      alert('Failed to delete exam.');
      btn.disabled = false;
    }
  })
  .catch(() => {
    alert('Error deleting exam.');
    btn.disabled = false;
  });
}
</script>
</body>
</html>