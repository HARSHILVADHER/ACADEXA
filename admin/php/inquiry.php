<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "<p style='text-align:center;color:red;'>User not authenticated!</p>";
    exit();
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect inquiry cards in a variable
$inquiryCards = '';

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
        $interestLevel = htmlspecialchars($inq['interest_level']);
        $followUpDate = htmlspecialchars($inq['followup_date']);
        $followUpTime = htmlspecialchars($inq['followup_time']);

        // Card UI with animation classes
        $inquiryCards .= '<div class="inquiry-card animate__animated animate__fadeInUp">';
        $inquiryCards .= "<div class='card-header'>";
        $inquiryCards .= "<h2>{$studentName}</h2>";
        $inquiryCards .= "<span class='interest-badge {$interestLevel}'>" . strtoupper($interestLevel) . "</span>";
        $inquiryCards .= "</div>";
        $inquiryCards .= "<div class='card-body'>";
        $inquiryCards .= "<p><i class='fas fa-school'></i> {$school}</p>";
        $inquiryCards .= "<p><i class='fas fa-phone'></i> {$phone}</p>";
        $inquiryCards .= "</div>";
        $inquiryCards .= "<div class='card-footer'>";
        $inquiryCards .= "<p><i class='far fa-calendar-alt'></i> {$followUpDate} at {$followUpTime}</p>";
        $inquiryCards .= "<span class='view-btn' onclick='openModal({$id})'>View Details <i class='fas fa-chevron-right'></i></span>";
        $inquiryCards .= '</div></div>';

        // Modal for full details
        $inquiryCards .= '<div class="modal" id="modal-' . $id . '">';
        $inquiryCards .= '<div class="modal-content animate__animated animate__fadeIn">';
        $inquiryCards .= '<span class="close-btn" onclick="closeModal(' . $id . ')">&times;</span>';
        $inquiryCards .= '<h2><i class="fas fa-info-circle"></i> Inquiry Details</h2>';
        $inquiryCards .= '<div class="modal-grid">';

        foreach ($inq as $key => $value) {
            if ($key === 'user_id' || $key === 'id') continue;
            $label = ucfirst(str_replace("_", " ", $key));
            $icon = '';
            
            // Assign icons based on field
            switch($key) {
                case 'student_name': $icon = 'fas fa-user'; break;
                case 'student_mobile': $icon = 'fas fa-mobile-alt'; break;
                case 'father_mobile': $icon = 'fas fa-user-friends'; break;
                case 'school_name': $icon = 'fas fa-school'; break;
                case 'percentage': $icon = 'fas fa-percentage'; break;
                case 'std': $icon = 'fas fa-graduation-cap'; break;
                case 'medium': $icon = 'fas fa-language'; break;
                case 'group_name': $icon = 'fas fa-layer-group'; break;
                case 'reference_by': $icon = 'fas fa-handshake'; break;
                case 'interest_level': $icon = 'fas fa-star'; break;
                case 'followup_date': $icon = 'far fa-calendar-alt'; break;
                case 'followup_time': $icon = 'far fa-clock'; break;
                case 'notes': $icon = 'far fa-sticky-note'; break;
                default: $icon = 'fas fa-info-circle';
            }
            
            $inquiryCards .= "<div class='modal-item'>";
            $inquiryCards .= "<span class='modal-label'><i class='{$icon}'></i> {$label}:</span>";
            $inquiryCards .= "<span class='modal-value'>" . htmlspecialchars($value) . "</span>";
            $inquiryCards .= '</div>';
        }

        $inquiryCards .= '</div></div></div>';
    }
} else {
    $inquiryCards = "<div class='no-inquiries animate__animated animate__fadeIn'>";
    $inquiryCards .= "<i class='fas fa-inbox'></i>";
    $inquiryCards .= "<p>No inquiries found</p>";
    $inquiryCards .= "</div>";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Inquiries | Acadexa</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    
    /* Main Content Layout */
    .main-container {
      display: flex;
      min-height: calc(100vh - 80px);
      padding: 30px;
      gap: 30px;
      max-width: 1600px;
      margin: 0 auto;
    }
    
    .inquiries-section {
      flex: 1;
      min-width: 350px;
      max-width: 450px;
    }
    
    .form-section {
      flex: 2;
      min-width: 500px;
    }
    
    /* Inquiries List Styles */
    .section-title {
      font-size: 1.5rem;
      color: var(--primary);
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .section-title i {
      font-size: 1.3rem;
    }
    
    .inquiries-container {
      display: grid;
      gap: 20px;
    }
    
    .inquiry-card {
      background: var(--white);
      border-radius: 12px;
      box-shadow: var(--card-shadow);
      padding: 20px;
      transition: var(--transition);
      border-left: 4px solid var(--primary);
    }
    
    .inquiry-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--card-shadow-hover);
    }
    
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .inquiry-card h2 {
      margin: 0;
      font-size: 1.2rem;
      color: var(--dark);
      font-weight: 600;
    }
    
    .interest-badge {
      font-size: 0.7rem;
      padding: 4px 10px;
      border-radius: 20px;
      font-weight: 600;
      text-transform: uppercase;
    }
    
    .interest-badge.High {
      background-color: rgba(76, 201, 240, 0.1);
      color: var(--success);
    }
    
    .interest-badge.Medium {
      background-color: rgba(248, 150, 30, 0.1);
      color: var(--warning);
    }
    
    .interest-badge.Low {
      background-color: rgba(247, 37, 133, 0.1);
      color: var(--danger);
    }
    
    .card-body p, .card-footer p {
      margin: 8px 0;
      font-size: 0.9rem;
      color: var(--gray);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .card-footer {
      margin-top: 15px;
      padding-top: 15px;
      border-top: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .view-btn {
      font-size: 0.85rem;
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
    
    .view-btn i {
      font-size: 0.7rem;
      transition: var(--transition);
    }
    
    .view-btn:hover i {
      transform: translateX(3px);
    }
    
    .no-inquiries {
      background: var(--white);
      border-radius: 12px;
      box-shadow: var(--card-shadow);
      padding: 40px 20px;
      text-align: center;
      color: var(--gray);
    }
    
    .no-inquiries i {
      font-size: 3rem;
      color: #d1d5db;
      margin-bottom: 15px;
    }
    
    .no-inquiries p {
      font-size: 1.1rem;
    }
    
    /* Form Styles */
    .form-container {
      background: var(--white);
      border-radius: 12px;
      box-shadow: var(--card-shadow);
      padding: 30px;
      transition: var(--transition);
    }
    
    .form-container:hover {
      box-shadow: var(--card-shadow-hover);
    }
    
    .form-title {
      font-size: 1.5rem;
      color: var(--primary);
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .form-title i {
      font-size: 1.3rem;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group.full-width {
      grid-column: span 2;
    }
    
    label {
      display: block;
      margin-bottom: 8px;
      font-size: 0.9rem;
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
      min-height: 100px;
    }
    
    .submit-btn {
      background-color: var(--primary);
      color: var(--white);
      border: none;
      padding: 14px 28px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
      display: block;
      margin: 30px auto 0;
      transition: var(--transition);
      grid-column: span 2;
      width: fit-content;
    }
    
    .submit-btn:hover {
      background-color: var(--secondary);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }
    
    .submit-btn:active {
      transform: translateY(0);
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
    
    .modal-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
    }
    
    .modal-item {
      padding: 10px;
      border-radius: 8px;
      background-color: var(--light);
    }
    
    .modal-label {
      font-size: 0.85rem;
      font-weight: 500;
      color: var(--gray);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .modal-value {
      font-size: 0.95rem;
      color: var(--dark);
      font-weight: 500;
      display: block;
      margin-top: 5px;
      margin-left: 23px;
    }
    
    /* Animations */
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }
    
    .floating {
      animation: float 3s ease-in-out infinite;
    }
    
    /* Responsive Styles */
    @media (max-width: 1200px) {
      .main-container {
        flex-direction: column;
      }
      
      .inquiries-section, .form-section {
        min-width: 100%;
        max-width: 100%;
      }
    }
    
    @media (max-width: 768px) {
      header {
        padding: 15px 20px;
      }
      
      nav {
        gap: 8px;
      }
      
      nav a {
        padding: 6px 10px;
        font-size: 0.85rem;
      }
      
      .main-container {
        padding: 20px;
      }
      
      .form-grid, .modal-grid {
        grid-template-columns: 1fr;
      }
      
      .form-group.full-width {
        grid-column: span 1;
      }
      
      .modal-item {
        grid-column: span 1;
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
      
      .main-container {
        padding: 15px;
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

  <div class="main-container">
    <!-- Inquiries Section -->
    <section class="inquiries-section">
      <h2 class="section-title">
        <i class="fas fa-list-alt"></i>
        Inquiry Records
      </h2>
      <div class="inquiries-container">
        <?php echo $inquiryCards; ?>
      </div>
    </section>

    <!-- Form Section -->
    <section class="form-section">
      <div class="form-container animate__animated animate__fadeInRight">
        <h2 class="form-title">
          <i class="fas fa-user-plus floating" style="animation-delay: 0.2s"></i>
          New Student Inquiry
        </h2>
        <form action="submit_inquiry.php" method="post" id="inquiryForm">
          <div class="form-grid">
            <!-- Student Info -->
            <div class="form-group full-width">
              <label for="student_name" class="required">Student's Name</label>
              <input type="text" id="student_name" name="student_name" required placeholder="Enter full name">
            </div>
            
            <div class="form-group">
              <label for="student_mobile" class="required">Student's Mobile No</label>
              <input type="tel" id="student_mobile" name="student_mobile" required placeholder="Enter mobile number">
            </div>
            
            <div class="form-group">
              <label for="father_mobile" class="required">Father's Mobile No</label>
              <input type="tel" id="father_mobile" name="father_mobile" required placeholder="Enter father's number">
            </div>
            
            <!-- School Info -->
            <div class="form-group full-width">
              <label for="school_name" class="required">School Name</label>
              <input type="text" id="school_name" name="school_name" required placeholder="Enter school name">
            </div>
            
            <div class="form-group">
              <label for="percentage" class="required">Percentage</label>
              <input type="number" id="percentage" name="percentage" required placeholder="Enter percentage" min="0" max="100" step="0.01">
            </div>
            
            <div class="form-group">
              <label for="std" class="required">STD</label>
              <input type="text" id="std" name="std" required placeholder="Enter standard/grade">
            </div>
            
            <!-- Medium & Group -->
            <div class="form-group">
              <label for="medium" class="required">Medium</label>
              <select id="medium" name="medium" required>
                <option value="">Select Medium</option>
                <option value="English Medium">English Medium</option>
                <option value="Gujarati Medium">Gujarati Medium</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="group_name" class="required">Group</label>
              <select id="group_name" name="group_name" required>
                <option value="">Select Group</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="A+B">A+B</option>
              </select>
            </div>
            
            <!-- Reference -->
            <div class="form-group full-width">
              <label for="reference_by" class="required">Reference By</label>
              <input type="text" id="reference_by" name="reference_by" required placeholder="How did they hear about us?">
            </div>
            
            <!-- Interest Level -->
            <div class="form-group">
              <label for="interest_level" class="required">Interest Level</label>
              <select id="interest_level" name="interest_level" required>
                <option value="">Select Level</option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
              </select>
            </div>
            
            <!-- Follow Up -->
            <div class="form-group">
              <label for="followup_date">Follow-Up Date</label>
              <input type="date" id="followup_date" name="followup_date">
            </div>
            
            <div class="form-group">
              <label for="followup_time">Follow-Up Time</label>
              <input type="time" id="followup_time" name="followup_time">
            </div>
            
            <!-- Notes -->
            <div class="form-group full-width">
              <label for="notes">Notes</label>
              <textarea id="notes" name="notes" placeholder="Any additional notes or comments..."></textarea>
            </div>
            
            <input type="hidden" id="wa_message" value="Welcome to The StudyRoom! Thank you for your inquiry. We will contact you soon.">
            
            <button type="button" class="submit-btn animate__animated animate__pulse" onclick="submitInquiry()" style="animation-delay: 0.5s">
              <i class="fas fa-paper-plane"></i> Submit Inquiry
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal')) {
        e.target.classList.remove('show');
        document.body.style.overflow = 'auto';
      }
    });

    // Form submission with animation
    function submitInquiry() {
      const form = document.getElementById('inquiryForm');
      const submitBtn = document.querySelector('.submit-btn');
      
      // Validate form
      let isValid = true;
      const requiredFields = form.querySelectorAll('[required]');
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          isValid = false;
          field.style.borderColor = '#f72585';
          field.addEventListener('input', function() {
            if (this.value.trim()) {
              this.style.borderColor = '#e0e0e0';
            }
          });
        }
      });
      
      if (!isValid) {
        submitBtn.classList.remove('animate__pulse');
        submitBtn.classList.add('animate__shakeX');
        setTimeout(() => {
          submitBtn.classList.remove('animate__shakeX');
          submitBtn.classList.add('animate__pulse');
        }, 1000);
        return;
      }
      
      // Add loading state
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
      submitBtn.disabled = true;
      
      // Get form data
      const formData = new FormData(form);
      const number = formData.get('student_mobile').replace(/\D/g, '');
      const waMessage = document.getElementById('wa_message').value;
      
      // Submit via AJAX
      fetch('submit_inquiry.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(response => {
        // Success animation
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Success!';
        submitBtn.classList.remove('animate__pulse');
        submitBtn.classList.add('animate__bounce');
        
        // Open WhatsApp if number is valid
        if (number.length >= 10) {
          const waUrl = `https://wa.me/${number}?text=${encodeURIComponent(waMessage)}`;
          setTimeout(() => window.open(waUrl, '_blank'), 1000);
        }
        
        // Reset form and button after delay
        setTimeout(() => {
          form.reset();
          submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Inquiry';
          submitBtn.classList.remove('animate__bounce');
          submitBtn.classList.add('animate__pulse');
          submitBtn.disabled = false;
          
          // Reload page to show new inquiry
          setTimeout(() => location.reload(), 500);
        }, 2000);
      })
      .catch(() => {
        // Error state
        submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error! Try Again';
        submitBtn.style.backgroundColor = '#f72585';
        
        setTimeout(() => {
          submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Inquiry';
          submitBtn.style.backgroundColor = '';
          submitBtn.disabled = false;
        }, 2000);
      });
    }
    
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