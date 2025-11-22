<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Total Inquiry Details</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
    
    /* Main Content */
    .main-content {
      padding: 30px;
      max-width: 1600px;
      margin: 0 auto;
    }
    
    .page-title {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .page-title i {
      font-size: 1.8rem;
    }
    
    /* Inquiry Cards */
    .inquiry-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }
    
    .inquiry-card {
      background: var(--white);
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 25px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      border-left: 4px solid var(--primary);
    }
    
    .inquiry-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(67, 97, 238, 0.15);
    }
    
    .inquiry-card-content {
      margin-bottom: 15px;
    }
    
    .inquiry-card h2 {
      margin: 0 0 10px 0;
      font-size: 1.3rem;
      color: var(--dark);
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .inquiry-card h2 i {
      color: var(--primary);
      font-size: 1.1rem;
    }
    
    .inquiry-card p {
      margin: 8px 0;
      font-size: 0.95rem;
      color: var(--gray);
      display: flex;
      align-items: flex-start;
      gap: 8px;
    }
    
    .inquiry-card p strong {
      min-width: 110px;
      color: var(--dark);
    }
    
    .inquiry-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
      padding-top: 15px;
      border-top: 1px solid #eee;
    }
    
    .view-btn {
      font-size: 0.9rem;
      color: var(--primary);
      cursor: pointer;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: var(--transition);
    }
    
    .view-btn:hover {
      color: var(--secondary);
    }
    
    .action-buttons {
      display: flex;
      gap: 10px;
    }
    
    .edit-btn, .delete-btn {
      background: none;
      border: none;
      cursor: pointer;
      padding: 6px 10px;
      border-radius: 6px;
      transition: var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .edit-btn {
      color: var(--primary);
      background-color: rgba(67, 97, 238, 0.1);
    }
    
    .edit-btn:hover {
      background-color: rgba(67, 97, 238, 0.2);
    }
    
    .delete-btn {
      color: var(--danger);
      background-color: rgba(247, 37, 133, 0.1);
    }
    
    .delete-btn:hover {
      background-color: rgba(247, 37, 133, 0.2);
    }
    
    .no-inquiries {
      text-align: center;
      padding: 40px;
      color: var(--gray);
      grid-column: 1 / -1;
    }
    
    .no-inquiries i {
      font-size: 3rem;
      color: #d1d5db;
      margin-bottom: 15px;
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1050;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      overflow: auto;
      backdrop-filter: blur(5px);
    }
    
    .modal.show {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .modal-content {
      background: var(--white);
      padding: 30px;
      border-radius: 15px;
      width: 90%;
      max-width: 700px;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .close-btn {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 28px;
      font-weight: bold;
      color: var(--gray);
      cursor: pointer;
      transition: var(--transition);
    }
    
    .close-btn:hover {
      color: var(--danger);
      transform: rotate(90deg);
    }
    
    .modal-content h2 {
      margin-top: 0;
      font-size: 1.5rem;
      color: var(--primary);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .modal-content p {
      margin: 10px 0;
      font-size: 0.95rem;
      color: var(--dark);
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    
    .modal-content p strong {
      min-width: 150px;
      color: var(--gray);
    }
    
    /* Edit Modal Styles */
    .edit-modal .modal-content {
      max-width: 500px;
      background: var(--white);
    }
    
    .edit-inquiry-form {
      display: grid;
      gap: 20px;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 0.95rem;
      font-weight: 500;
      color: var(--dark);
    }
    
    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group input[type="time"],
    .form-group select {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      font-size: 0.95rem;
      transition: var(--transition);
      background-color: var(--light);
    }
    
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
      background-color: var(--white);
    }
    
    .followup-container {
      display: grid;
      gap: 15px;
      margin-bottom: 10px;
    }
    
    .add-more-btn {
      background-color: var(--primary-light);
      color: var(--primary);
      border: none;
      padding: 10px 15px;
      font-size: 0.9rem;
      font-weight: 500;
      border-radius: 8px;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 8px;
      justify-content: center;
      margin-bottom: 15px;
    }
    
    .add-more-btn:hover {
      background-color: rgba(67, 97, 238, 0.2);
    }
    
    .save-inquiry-btn {
      background-color: var(--primary);
      color: var(--white);
      border: none;
      padding: 14px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
      transition: var(--transition);
      width: 100%;
    }
    
    .save-inquiry-btn:hover {
      background-color: var(--secondary);
    }
    
    /* Responsive Styles */
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
    
    @media (max-width: 480px) {
      header {
        flex-direction: column;
        padding: 15px;
      }
      
      .logo {
        margin-bottom: 10px;
      }
      
      nav {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .page-title {
        font-size: 1.5rem;
      }
      
      .inquiry-actions {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
      }
      
      .action-buttons {
        width: 100%;
        justify-content: flex-end;
      }
    }
    @media (max-width: 900px) {
      nav {
        gap: 15px;
      }
      .logo {
        margin-right: 20px;
      }
    }
    @media (max-width: 600px) {
      header {
        flex-direction: column;
        padding: 10px 0;
      }
      .logo {
        margin: 0 0 10px 0;
      }
      nav {
        gap: 8px;
      }
      nav a {
        padding: 6px 10px;
        font-size: 0.9rem;
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
      <a href="inquiry.php" class="active">Inquiries</a>
      <a href="profile.php">Profile</a>
    </nav>
  </header>

  <div class="main-content">
    <h1 class="page-title animate__animated animate__fadeIn">
      <i class="fas fa-list-alt"></i>
      Total Inquiry Details
    </h1>

    <div class="inquiry-container">
      <?php
      require_once 'config.php';

      // Multi-user: Only show inquiries for the logged-in user
      $user_id = $_SESSION['user_id'] ?? null;
      if (!$user_id) {
        echo "<p style='text-align:center;color:red;'>User not authenticated!</p>";
        exit();
      }

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      $sql = "SELECT * FROM inquiry WHERE user_id = ? ORDER BY id DESC";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result && $result->num_rows > 0) {
        while ($inq = $result->fetch_assoc()) {
          $id = htmlspecialchars($inq['id']);
          $studentName = htmlspecialchars($inq['student_name']);
          $school = htmlspecialchars($inq['school_name']);
          $phone = htmlspecialchars($inq['student_mobile']);
          $interest = htmlspecialchars($inq['interest_level']);
          $followUpDate = htmlspecialchars($inq['followup_date']);
          $followUpTime = htmlspecialchars($inq['followup_time']);

          echo '<div class="inquiry-card animate__animated animate__fadeInUp">';
          echo '<div class="inquiry-card-content">';
          echo "<h2><i class='fas fa-user-graduate'></i> {$studentName}</h2>";
          echo "<p><strong><i class='fas fa-school'></i> School:</strong> {$school}</p>";
          echo "<p><strong><i class='fas fa-phone'></i> Contact:</strong> {$phone}</p>";
          echo "<p><strong><i class='fas fa-star'></i> Interest Level:</strong> {$interest}</p>";
          echo "<p><strong><i class='fas fa-calendar-day'></i> Follow Up:</strong> {$followUpDate} at {$followUpTime}</p>";
          echo '</div>';
          echo "<div class='inquiry-actions'>";
          echo "<span class='view-btn' onclick='openModal({$id})'><i class='fas fa-eye'></i> View Details</span>";
          echo "<div class='action-buttons'>";
          echo "<button title='Edit Inquiry' class='edit-btn' onclick='openEditModal({$id})'>
                  <i class='fas fa-edit'></i>
                </button>";
          echo "<button title='Delete Inquiry' class='delete-btn' onclick='deleteInquiry($id, this)'>
                  <i class='fas fa-trash'></i>
                </button>";
          echo "</div>";
          echo "</div>";
          echo '</div>';

          // Modal for each inquiry
          echo '<div class="modal" id="modal-' . $id . '">';
          echo '<div class="modal-content animate__animated animate__fadeIn">';
          echo '<span class="close-btn" onclick="closeModal(' . $id . ')">&times;</span>';
          echo '<h2><i class="fas fa-info-circle"></i> Inquiry Details</h2>';
          foreach ($inq as $key => $value) {
            $label = ucfirst(str_replace("_", " ", $key));
            $icon = '';
            
            // Assign icons based on field
            switch($key) {
              case 'student_name': $icon = 'fas fa-user-graduate'; break;
              case 'student_mobile': $icon = 'fas fa-phone'; break;
              case 'father_mobile': $icon = 'fas fa-user-friends'; break;
              case 'school_name': $icon = 'fas fa-school'; break;
              case 'percentage': $icon = 'fas fa-percentage'; break;
              case 'std': $icon = 'fas fa-graduation-cap'; break;
              case 'medium': $icon = 'fas fa-language'; break;
              case 'group_name': $icon = 'fas fa-layer-group'; break;
              case 'reference_by': $icon = 'fas fa-handshake'; break;
              case 'interest_level': $icon = 'fas fa-star'; break;
              case 'followup_date': $icon = 'fas fa-calendar-day'; break;
              case 'followup_time': $icon = 'fas fa-clock'; break;
              case 'notes': $icon = 'fas fa-sticky-note'; break;
              default: $icon = 'fas fa-info-circle';
            }
            
            echo "<p><strong><i class='{$icon}'></i> {$label}:</strong> " . htmlspecialchars($value) . "</p>";
          }
          echo '</div></div>';

          // Edit modal
          echo '<div class="modal edit-modal" id="edit-modal-' . $id . '">';
          echo '<div class="modal-content animate__animated animate__fadeIn">';
          echo '<span class="close-btn" onclick="closeEditModal(' . $id . ')">&times;</span>';
          echo '<h2><i class="fas fa-edit"></i> Edit Inquiry</h2>';
          echo '<form class="edit-inquiry-form" onsubmit="return saveInquiry(event, ' . $id . ')">';
          echo '<div class="form-group">';
          echo '<label>Student Name</label>';
          echo '<input type="text" name="student_name" value="' . $studentName . '" required>';
          echo '</div>';
          echo '<div class="form-group">';
          echo '<label>School Name</label>';
          echo '<input type="text" name="school_name" value="' . $school . '" required>';
          echo '</div>';
          echo '<div class="form-group">';
          echo '<label>Contact</label>';
          echo '<input type="text" name="student_mobile" value="' . $phone . '" required>';
          echo '</div>';
          echo '<div class="form-group">';
          echo '<label>Interest Level</label>';
          echo '<input type="text" name="interest_level" value="' . $interest . '" required>';
          echo '</div>';
          echo '<div id="followup-dates-'.$id.'" class="followup-container">';
          echo '<div class="form-group">';
          echo '<label>Follow Up Date</label>';
          echo '<input type="date" name="followup_date[]" value="' . $followUpDate . '" required>';
          echo '</div>';
          echo '<div class="form-group">';
          echo '<label>Follow Up Time</label>';
          echo '<input type="time" name="followup_time[]" value="' . $followUpTime . '" required>';
          echo '</div>';
          echo '</div>';
          echo '<button type="button" class="add-more-btn" onclick="addMoreFollowup('.$id.')">';
          echo '<i class="fas fa-plus"></i> Add More Followup';
          echo '</button>';
          echo '<button type="submit" class="save-inquiry-btn">';
          echo '<i class="fas fa-save"></i> Save Inquiry';
          echo '</button>';
          echo '</form>';
          echo '</div></div>';
        }
      } else {
        echo '<div class="no-inquiries animate__animated animate__fadeIn">';
        echo '<i class="fas fa-inbox"></i>';
        echo '<p>No inquiries found</p>';
        echo '</div>';
      }

      $stmt->close();
      $conn->close();
      ?>
    </div>
  </div>

  <script>
    // Modal functions
    function openModal(id) {
      const modal = document.getElementById('modal-' + id);
      if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeModal(id) {
      const modal = document.getElementById('modal-' + id);
      if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
      }
    }

    function openEditModal(id) {
      document.getElementById('edit-modal-' + id).classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeEditModal(id) {
      document.getElementById('edit-modal-' + id).classList.remove('show');
      document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal')) {
        e.target.classList.remove('show');
        document.body.style.overflow = 'auto';
      }
    });

    function deleteInquiry(id, btn) {
      if (!confirm('Are you sure you want to delete this inquiry?')) return;
      
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      btn.disabled = true;
      
      fetch('delete_inquiry.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'inquiry_id=' + encodeURIComponent(id)
      })
      .then(res => res.text())
      .then(data => {
        if (data.trim() === 'success') {
          btn.closest('.inquiry-card').classList.add('animate__animated', 'animate__fadeOut');
          setTimeout(() => {
            btn.closest('.inquiry-card').remove();
          }, 300);
        } else {
          alert('Failed to delete inquiry.');
          btn.innerHTML = '<i class="fas fa-trash"></i>';
          btn.disabled = false;
        }
      })
      .catch(() => {
        alert('Error deleting inquiry.');
        btn.innerHTML = '<i class="fas fa-trash"></i>';
        btn.disabled = false;
      });
    }

    // Save Inquiry AJAX
    function saveInquiry(event, id) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData();

      // Get all form fields except followup_date/time
      ['student_name', 'school_name', 'student_mobile', 'interest_level'].forEach(name => {
        formData.append(name, form.elements[name].value);
      });

      // Get all followup_date and followup_time fields
      const dates = Array.from(form.querySelectorAll('input[name="followup_date[]"]')).map(i => i.value);
      const times = Array.from(form.querySelectorAll('input[name="followup_time[]"]')).map(i => i.value);

      // Use the last entered date/time
      formData.append('followup_date', dates[dates.length - 1]);
      formData.append('followup_time', times[times.length - 1]);
      formData.append('inquiry_id', id);

      const submitBtn = form.querySelector('.save-inquiry-btn');
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      submitBtn.disabled = true;

      fetch('update_inquiry.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        if (data.trim() === 'success') {
          submitBtn.innerHTML = '<i class="fas fa-check"></i> Saved!';
          setTimeout(() => {
            closeEditModal(id);
            location.reload();
          }, 1000);
        } else {
          alert('Failed to update inquiry.');
          submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Inquiry';
          submitBtn.disabled = false;
        }
      })
      .catch(() => {
        alert('Error updating inquiry.');
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Inquiry';
        submitBtn.disabled = false;
      });
      return false;
    }

    function addMoreFollowup(id) {
      const container = document.getElementById('followup-dates-' + id);
      const html = `
        <div class="form-group">
          <label>Follow Up Date</label>
          <input type="date" name="followup_date[]" required>
        </div>
        <div class="form-group">
          <label>Follow Up Time</label>
          <input type="time" name="followup_time[]" required>
        </div>
      `;
      container.insertAdjacentHTML('beforeend', html);
    }
  </script>
</body>
</html>