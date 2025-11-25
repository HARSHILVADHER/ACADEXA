<?php
session_start();
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
    
    .logo img {
      height: 40px;
      width: auto;
      object-fit: contain;
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

    .search-filter-bar {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 20px 40px;
      background: white;
      border-bottom: 1px solid #e5e7eb;
    }
    .search-box {
      flex: 1;
      position: relative;
    }
    .search-box input {
      width: 100%;
      padding: 10px 40px 10px 15px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 0.95rem;
      transition: border-color 0.3s;
    }
    .search-box input:focus {
      outline: none;
      border-color: #2563eb;
    }
    .search-box i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6b7280;
    }
    .filter-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .filter-group label {
      font-weight: 600;
      color: #374151;
      font-size: 0.95rem;
    }
    .filter-group select {
      padding: 10px 15px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 0.95rem;
      background: white;
      cursor: pointer;
      min-width: 150px;
      transition: border-color 0.3s;
    }
    .filter-group select:focus {
      outline: none;
      border-color: #2563eb;
    }

    .exam-row {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 24px;
      padding: 30px 40px;
    }

    @media (max-width: 1600px) {
      .exam-row { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 1200px) {
      .exam-row { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 900px) {
      .exam-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
      .exam-row { grid-template-columns: 1fr; }
    }

    .exam-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      padding: 24px;
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
      position: relative;
      border: 2px solid #2563eb;
    }
    .exam-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 16px rgba(37, 99, 235, 0.15);
      border-color: #1d4ed8;
    }
    .exam-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: #2563eb;
      margin-bottom: 12px;
    }
    .exam-class {
      font-size: 0.95rem;
      color: #2563eb;
      margin-bottom: 16px;
      padding: 6px 12px;
      background: #e0e7ff;
      border-radius: 6px;
      display: inline-block;
    }
    .exam-info {
      font-size: 0.9rem;
      color: #374151;
      margin-bottom: 8px;
    }
    .exam-info strong {
      color: #1f2937;
    }
    .exam-notes {
      font-size: 0.88rem;
      color: #6b7280;
      margin-top: 12px;
      padding: 12px;
      background: #f3f4f6;
      border-radius: 6px;
      border-left: 3px solid #2563eb;
    }
    .no-exams {
      grid-column: 1 / -1;
      text-align: center;
      color: #6b7280;
      padding: 60px 20px;
      font-size: 1.1rem;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .modal-content {
      background-color: #fff;
      margin: 3% auto;
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      animation: slideDown 0.3s;
    }
    @keyframes slideDown {
      from { transform: translateY(-50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
      font-size: 1.5rem;
      font-weight: 700;
      color: #2563eb;
      margin-bottom: 20px;
      padding-right: 30px;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      line-height: 20px;
    }
    .close:hover { color: #000; }
    .form-group {
      margin-bottom: 16px;
      clear: both;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #374151;
    }
    .form-group input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 0.95rem;
    }
    .form-group input:focus {
      outline: none;
      border-color: #2563eb;
    }
    .btn-primary {
      background: #2563eb;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      margin-right: 10px;
      transition: background 0.3s;
    }
    .btn-primary:hover {
      background: #1d4ed8;
    }
    .btn-secondary {
      background: #10b981;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.3s;
    }
    .btn-secondary:hover {
      background: #059669;
    }
    .btn-secondary:disabled {
      background: #9ca3af;
      cursor: not-allowed;
      opacity: 0.6;
    }
    .modal-buttons {
      margin-top: 20px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    </style>
</head>
<body>
  <header>
    <?php include 'php/header_logo.php'; ?>
    <nav>
      <a href="php/dashboard.php">Home</a>
      <a href="createclass.html">Classes</a>
      <a href="attendance.html">Attendance</a>
      <a href="php/gradecard.php">Reports</a>
      <a href="php/inquiry.php">Inquiries</a>
      <a href="php/profile.php">Profile</a>
    </nav>
  </header>
  
  <div class="search-filter-bar">
    <div class="search-box">
      <input type="text" id="searchInput" placeholder="Search exams by name...">
      <i class="fas fa-search"></i>
    </div>
    <div class="filter-group">
      <label>Class:</label>
      <select id="classFilter">
        <option value="">All Classes</option>
      </select>
    </div>
    <div class="filter-group">
      <label>Date:</label>
      <select id="dateFilter">
        <option value="">All Dates</option>
      </select>
    </div>
  </div>
  
  <div class="exam-row" id="examContainer">
    <div class="no-exams">Loading exams...</div>
  </div>
  
  <!-- Edit Exam Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeEditModal()">&times;</span>
      <div class="modal-header">Edit Exam Details</div>
      <form id="editExamForm">
        <input type="hidden" id="edit_exam_id">
        <div class="form-group">
          <label>Exam Name</label>
          <input type="text" id="edit_exam_name" readonly style="background:#f3f4f6;">
        </div>
        <div class="form-group">
          <label>Class</label>
          <input type="text" id="edit_class_name" readonly style="background:#f3f4f6;">
        </div>
        <div class="form-group">
          <label>Date</label>
          <input type="date" id="edit_exam_date" required>
        </div>
        <div class="form-group">
          <label>Start Time</label>
          <input type="time" id="edit_start_time" required>
        </div>
        <div class="form-group">
          <label>End Time</label>
          <input type="time" id="edit_end_time" required>
        </div>
        <div class="form-group">
          <label>Total Marks</label>
          <input type="number" id="edit_total_marks" required min="1">
        </div>
        <div class="form-group">
          <label>Passing Marks</label>
          <input type="number" id="edit_passing_marks" required min="1">
        </div>
        <div class="modal-buttons">
          <button type="button" id="addMarksBtn" class="btn-secondary" onclick="navigateToAddMarks()">Add Marks</button>
          <button type="submit" class="btn-primary">Save Update</button>
        </div>
      </form>
    </div>
  </div>
  <script>
let allExams = [];

function loadExams() {
  fetch('php/get_all_exams.php')
    .then(response => response.text())
    .then(text => {
      try {
        const exams = JSON.parse(text);
        
        if (exams.error) {
          document.getElementById('examContainer').innerHTML = '<div class="no-exams">Error loading exams</div>';
          return;
        }
        
        allExams = exams;
        populateFilters(exams);
        displayExams(exams);
      } catch (e) {
        console.error('Error parsing exams:', e);
        document.getElementById('examContainer').innerHTML = '<div class="no-exams">Error loading exams</div>';
      }
    })
    .catch(error => {
      console.error('Error loading exams:', error);
      document.getElementById('examContainer').innerHTML = '<div class="no-exams">Error loading exams</div>';
    });
}

function populateFilters(exams) {
  const classFilter = document.getElementById('classFilter');
  const dateFilter = document.getElementById('dateFilter');
  
  const classes = [...new Set(exams.map(e => e.class_name))].sort();
  const dates = [...new Set(exams.map(e => e.exam_date))].sort();
  
  classFilter.innerHTML = '<option value="">All Classes</option>';
  classes.forEach(cls => {
    classFilter.innerHTML += `<option value="${cls}">${cls}</option>`;
  });
  
  dateFilter.innerHTML = '<option value="">All Dates</option>';
  dates.forEach(date => {
    dateFilter.innerHTML += `<option value="${date}">${date}</option>`;
  });
}

function displayExams(exams) {
  const container = document.getElementById('examContainer');
  
  if (exams.length === 0) {
    container.innerHTML = '<div class="no-exams">No exams found.</div>';
    return;
  }
  
  container.innerHTML = '';
  exams.forEach(exam => {
    const examCard = document.createElement('div');
    examCard.className = 'exam-card';
    examCard.style.position = 'relative';
    
    examCard.innerHTML = `
      <div class="exam-title">${exam.exam_name}</div>
      <div class="exam-class">Class: ${exam.class_name}</div>
      <div class="exam-info"><strong>Date:</strong> ${exam.exam_date}</div>
      <div class="exam-info"><strong>Time:</strong> ${exam.start_time} - ${exam.end_time}</div>
      <div class="exam-info"><strong>Total Marks:</strong> ${exam.total_marks}</div>
      <div class="exam-info"><strong>Passing Marks:</strong> ${exam.passing_marks}</div>
      ${exam.notes ? `<div class="exam-notes"><strong>Notes:</strong> ${exam.notes}</div>` : ''}
      <button class="edit-btn" onclick="openEditModal(${exam.id}, '${exam.exam_name}', '${exam.class_name}', '${exam.class_code}', '${exam.exam_date}', '${exam.start_time}', '${exam.end_time}', ${exam.total_marks}, ${exam.passing_marks})" title="Edit Exam" style="position:absolute;bottom:12px;right:52px;background:none;border:none;cursor:pointer;">
        <i class="fa-regular fa-pen-to-square" style="color:#2563eb;font-size:20px;"></i>
      </button>
      <button class="delete-btn" onclick="deleteExam(${exam.id}, this)" title="Delete Exam" style="position:absolute;bottom:12px;right:12px;background:none;border:none;cursor:pointer;">
        <i class="fa-solid fa-trash" style="color:#e11d48;font-size:20px;"></i>
      </button>
    `;
    
    container.appendChild(examCard);
  });
}

function filterExams() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const classFilter = document.getElementById('classFilter').value;
  const dateFilter = document.getElementById('dateFilter').value;
  
  let filtered = allExams.filter(exam => {
    const matchesSearch = exam.exam_name.toLowerCase().includes(searchTerm);
    const matchesClass = !classFilter || exam.class_name === classFilter;
    const matchesDate = !dateFilter || exam.exam_date === dateFilter;
    
    return matchesSearch && matchesClass && matchesDate;
  });
  
  displayExams(filtered);
}

document.getElementById('searchInput').addEventListener('input', filterExams);
document.getElementById('classFilter').addEventListener('change', filterExams);
document.getElementById('dateFilter').addEventListener('change', filterExams);

function deleteExam(examId, btn) {
  if (!confirm('Are you sure you want to delete this exam?')) return;
  btn.disabled = true;
  fetch('php/delete_exam.php', {
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

// Load exams on page load
document.addEventListener('DOMContentLoaded', loadExams);

let currentExamData = {};

function openEditModal(id, examName, className, classCode, examDate, startTime, endTime, totalMarks, passingMarks) {
  currentExamData = {
    id: id,
    exam_name: examName,
    class_name: className,
    class_code: classCode,
    exam_date: examDate
  };
  
  document.getElementById('edit_exam_id').value = id;
  document.getElementById('edit_exam_name').value = examName;
  document.getElementById('edit_class_name').value = className;
  document.getElementById('edit_exam_date').value = examDate;
  document.getElementById('edit_start_time').value = startTime;
  document.getElementById('edit_end_time').value = endTime;
  document.getElementById('edit_total_marks').value = totalMarks;
  document.getElementById('edit_passing_marks').value = passingMarks;
  
  const today = new Date().toISOString().split('T')[0];
  const addMarksBtn = document.getElementById('addMarksBtn');
  
  if (examDate < today) {
    addMarksBtn.disabled = false;
    addMarksBtn.title = 'Add marks for this exam';
  } else if (examDate === today) {
    addMarksBtn.disabled = false;
    addMarksBtn.title = 'Add marks for this exam';
  } else {
    addMarksBtn.disabled = true;
    addMarksBtn.title = 'Marks can only be added on or after the exam date';
  }
  
  document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

function navigateToAddMarks() {
  const examDate = currentExamData.exam_date;
  const today = new Date().toISOString().split('T')[0];
  
  if (examDate > today) {
    alert('Marks can only be added on or after the exam date.');
    return;
  }
  
  const examId = document.getElementById('edit_exam_id').value;
  window.location.href = `php/add_marks.php?exam_id=${examId}`;
}

document.getElementById('editExamForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData();
  formData.append('exam_id', document.getElementById('edit_exam_id').value);
  formData.append('exam_date', document.getElementById('edit_exam_date').value);
  formData.append('start_time', document.getElementById('edit_start_time').value);
  formData.append('end_time', document.getElementById('edit_end_time').value);
  formData.append('total_marks', document.getElementById('edit_total_marks').value);
  formData.append('passing_marks', document.getElementById('edit_passing_marks').value);
  
  fetch('php/update_exam.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    if (data.trim() === 'success') {
      alert('Exam updated successfully!');
      closeEditModal();
      loadExams();
    } else {
      alert('Failed to update exam: ' + data);
    }
  })
  .catch(err => {
    alert('Error updating exam.');
    console.error(err);
  });
});

// Close modal when clicking outside
window.onclick = function(event) {
  const modal = document.getElementById('editModal');
  if (event.target == modal) {
    closeEditModal();
  }
}
</script>
</body>
</html>