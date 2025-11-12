<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// Optional: Role-based access
// if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher') {
//     header('Location: unauthorized.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>All Students - Acadexa</title>
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
    .container {
      max-width: 1200px;
      margin: 2rem auto;
      background-color: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .search-filter {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1.5rem;
      gap: 1rem;
    }
    .search-filter input,
    .search-filter select {
      padding: 0.6rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      flex: 1;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 0.75rem 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th {
      background-color: #f1f3f4;
      font-weight: 600;
    }
    tr:hover {
      background-color: #f9fafb;
    }
    .download-btn {
      display: inline-block;
      padding: 0.8rem 1.5rem;
      background-color: #1a73e8;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 500;
      transition: background-color 0.3s ease;
    }
    .download-btn:hover {
      background-color: #155ab6;
    }
  </style>
</head>
<body>
  <header>
    <?php require_once 'config.php'; include 'header_logo.php'; ?>
    <nav>
      <a href="dashboard.php">Home</a>
      <a href="createclass.html" class="active">Classes</a>
      <a href="attendance.html">Attendance</a>
      <a href="gradecard.php">Reports</a>
      <a href="inquiry.php">Inquiries</a>
      <a href="profile.php">Profile</a>
    </nav>
  </header>

  <div class="container">
    <div class="search-filter">
      <input type="text" id="searchInput" placeholder="Search by name..." onkeyup="filterTable()">
      <select id="filterSelect" onchange="filterTable()">
        <option value="">Filter by class</option>
        <?php
          require_once 'config.php';
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }
          $result = $conn->query("SELECT DISTINCT class_code FROM students");
          while ($row = $result->fetch_assoc()) {
              echo '<option value="' . htmlspecialchars($row['class_code']) . '">' . htmlspecialchars($row['class_code']) . '</option>';
          }
        ?>
      </select>
    </div>

    <table id="studentsTable">
      <thead>
        <tr>
          <th>Name</th>
          <th>Age</th>
          <th>Contact</th>
          <th>Email</th>
          <th>Class Code</th>
        </tr>
      </thead>
      <tbody>
        <?php
          require_once 'config.php';
          $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

          // Use a prepared statement to fetch only this user's students
          $stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['class_code']) . "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='5'>No students found</td></tr>";
          }
          $stmt->close();
          $conn->close();
        ?>
      </tbody>
    </table>
    <div style="text-align: center; margin-top: 1rem;">
        <a href="download_excel.php" class="download-btn">Download Excel</a>
    </div>
  </div>

  <script>
    function filterTable() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const filterClass = document.getElementById("filterSelect").value;
      const rows = document.querySelectorAll("#studentsTable tbody tr");

      rows.forEach(row => {
        const name = row.cells[0].textContent.toLowerCase();
        const classCode = row.cells[4].textContent;
        const matchesSearch = name.includes(input);
        const matchesClass = filterClass === "" || classCode === filterClass;
        row.style.display = matchesSearch && matchesClass ? "" : "none";
      });
    }
  </script>

</body>
</html>
