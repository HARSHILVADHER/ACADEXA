<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch classes for the logged-in user
$classes = [];
$stmt = $conn->prepare("SELECT code, name FROM classes WHERE user_id = ? ORDER BY name");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Generator | Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
        
        .filter-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }
        
        .filter-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
        }
        
        .filter-row {
            display: flex;
            gap: 20px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .input-group {
            flex: 1;
            min-width: 250px;
        }
        
        .input-group label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
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
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(67, 97, 238, 0.4);
        }
        
        .info-card {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
            display: none;
        }
        
        .info-card.show {
            display: block;
        }
        
        .info-text {
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .info-text strong {
            color: var(--primary);
        }
        
        .id-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .id-card {
            width: 320px;
            height: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            position: relative;
        }
        
        .id-card-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 15px;
            text-align: center;
            color: white;
        }
        
        .id-card-header h2 {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 3px;
        }
        
        .id-card-header p {
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .id-card-photo {
            width: 180px;
            height: 180px;
            margin: 20px auto;
            border: 4px solid #d32f2f;
            border-radius: 20px;
            overflow: hidden;
            background: #f0f0f0;
        }
        
        .id-card-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .id-card-body {
            padding: 0 20px;
        }
        
        .id-card-name {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: #d32f2f;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .id-card-info {
            border-top: 3px solid #d32f2f;
            padding-top: 10px;
            margin-bottom: 10px;
        }
        
        .id-card-row {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 2px solid #d32f2f;
        }
        
        .id-card-footer {
            background: #d32f2f;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .input-group {
                width: 100%;
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
            <h1 class="page-title">ID Card Generator</h1>
            <p class="page-subtitle">Generate student ID cards for your classes</p>
        </div>

        <div class="filter-card">
            <h3>Select Class</h3>
            <div class="filter-row">
                <div class="input-group">
                    <label>Class</label>
                    <select id="class_select" class="input-field" required>
                        <option value="">Choose a class</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo htmlspecialchars($class['code']); ?>">
                                <?php echo htmlspecialchars($class['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="info-card" id="studentInfo">
            <div class="info-text">
                Total Students: <strong id="studentCount">0</strong>
            </div>
            <button type="button" class="btn-primary" onclick="generateIDCards()">
                <i class="fas fa-id-card"></i> Generate ID Cards
            </button>
        </div>
        
        <div id="idCardsContainer" class="id-cards-container"></div>
    </div>

    <script>
        document.getElementById('class_select').addEventListener('change', function() {
            const classCode = this.value;
            const infoCard = document.getElementById('studentInfo');
            
            if (classCode) {
                fetch(`get_students_by_class.php?class_code=${encodeURIComponent(classCode)}`)
                    .then(response => response.json())
                    .then(students => {
                        document.getElementById('studentCount').textContent = students.length;
                        infoCard.classList.add('show');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to fetch student data');
                    });
            } else {
                infoCard.classList.remove('show');
            }
        });

        function generateIDCards() {
            const classCode = document.getElementById('class_select').value;
            
            if (!classCode) {
                alert('Please select a class');
                return;
            }
            
            fetch(`get_students_by_class.php?class_code=${encodeURIComponent(classCode)}`)
                .then(response => response.json())
                .then(students => {
                    const container = document.getElementById('idCardsContainer');
                    container.innerHTML = '';
                    
                    students.forEach(student => {
                        const card = createIDCard(student);
                        container.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to generate ID cards');
                });
        }
        
        function createIDCard(student) {
            const card = document.createElement('div');
            card.className = 'id-card';
            
            const photoUrl = student.photo ? `/ACADEXA/${student.photo}` : '';
            
            card.innerHTML = `
                <div class="id-card-header">
                    <h2>ACADEXA</h2>
                    <p>Direction : Solution : Education</p>
                </div>
                <div class="id-card-photo">
                    ${photoUrl ? `<img src="${photoUrl}" alt="${student.name}">` : ''}
                </div>
                <div class="id-card-body">
                    <div class="id-card-name">${student.name}</div>
                    <div class="id-card-info">
                        <div class="id-card-row">CLASS : ${student.class_name || 'N/A'}</div>
                        <div class="id-card-row">D.O.B. : ${student.dob || 'N/A'}</div>
                        <div class="id-card-row">MO. : ${student.phone || 'N/A'}</div>
                        <div class="id-card-row" style="border:none; font-size:0.8rem;">${student.address || 'Address not provided'}</div>
                    </div>
                </div>
                <div class="id-card-footer">
                    CONTACT : ${student.contact || '+91 00000 00000'}
                </div>
            `;
            
            return card;
        }
    </script>
</body>
</html>
