<?php
session_start();
require_once 'config.php';

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

// Check if user has income access (password verified)
if (!isset($_SESSION['income_access']) || !$_SESSION['income_access']) {
    header('Location: dashboard.php');
    exit();
}

// Check if access is still valid (expires after 1 hour)
if (isset($_SESSION['income_access_time']) && (time() - $_SESSION['income_access_time']) > 3600) {
    unset($_SESSION['income_access']);
    unset($_SESSION['income_access_time']);
    header('Location: dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Create income table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS income (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    source VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    category VARCHAR(100) DEFAULT 'Other',
    payment_method VARCHAR(50) DEFAULT 'Cash',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_date (date),
    INDEX idx_category (category)
)");

// Get income statistics for this user
$totalIncome = 0;
$monthlyIncome = 0;
$todayIncome = 0;
$incomeCount = 0;

// Total income
$stmt = $conn->prepare("SELECT SUM(amount) as total, COUNT(*) as count FROM income WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$totalIncome = $result['total'] ?? 0;
$incomeCount = $result['count'] ?? 0;
$stmt->close();

// Monthly income
$stmt = $conn->prepare("SELECT SUM(amount) as monthly FROM income WHERE user_id = ? AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$monthlyIncome = $result['monthly'] ?? 0;
$stmt->close();

// Today's income
$stmt = $conn->prepare("SELECT SUM(amount) as today FROM income WHERE user_id = ? AND date = CURDATE()");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$todayIncome = $result['today'] ?? 0;
$stmt->close();

// Get total expense
$totalExpense = 0;
$stmt = $conn->prepare("SELECT SUM(amount) as total FROM expense WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$totalExpense = $result['total'] ?? 0;
$stmt->close();

// Get total decided fees from fees_structure
$totalDecidedFees = 0;
$stmt = $conn->prepare("SELECT SUM(decided_fees) as total FROM fees_structure WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$totalDecidedFees = $result['total'] ?? 0;
$stmt->close();

// Get recent income entries (15 entries)
$recentIncome = [];
$stmt = $conn->prepare("SELECT * FROM income WHERE user_id = ? ORDER BY date DESC, created_at DESC LIMIT 15");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recentIncome[] = $row;
}
$stmt->close();

// Get expense by category for pie chart
$expenseCategoryData = [];
$stmt = $conn->prepare("SELECT category, SUM(amount) as total FROM expense WHERE user_id = ? GROUP BY category ORDER BY total DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $expenseCategoryData[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Management | Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --primary-dark: #3a0ca3;
            --secondary: #3f37c9;
            --accent: #f72585;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #ef233c;
            --dark: #1a1a1a;
            --light: #f8f9fa;
            --gray: #6c757d;
            --light-gray: #e9ecef;
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
            height: 76px;
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
        
        nav a:hover {
            color: var(--primary);
            background: var(--primary-light);
        }
        
        nav a.active {
            color: var(--primary);
            background: var(--primary-light);
        }
        
        .container {
            padding: 30px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .add-income-btn {
            background: var(--primary);
            color: var(--white);
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-income-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .stat-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--white);
            font-size: 1.4rem;
        }
        
        .stat-card.total .icon {
            background: linear-gradient(135deg, var(--success), #3a86f7);
        }
        
        .stat-card.monthly .icon {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        .stat-card.today .icon {
            background: linear-gradient(135deg, var(--warning), #ff6b35);
        }
        
        .stat-card.count .icon {
            background: linear-gradient(135deg, var(--accent), #c77dff);
        }
        
        .stat-card.expense .icon {
            background: linear-gradient(135deg, #ef233c, #d90429);
        }
        
        .stat-card.fees .icon {
            background: linear-gradient(135deg, #06ffa5, #00d9ff);
        }
        
        .stat-card .value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .income-card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(67, 97, 238, 0.1);
        }
        
        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
            position: relative;
            padding-left: 15px;
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
        
        .income-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .income-table thead th {
            padding: 12px 15px;
            text-align: left;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--gray);
            background: var(--light);
            border-bottom: 2px solid var(--light-gray);
        }
        
        .income-table tbody tr {
            transition: var(--transition);
        }
        
        .income-table tbody tr:hover {
            background: rgba(67, 97, 238, 0.03);
        }
        
        .income-table td {
            padding: 15px;
            border-bottom: 1px solid var(--light-gray);
            font-size: 0.9rem;
            color: var(--dark);
        }
        
        .amount {
            font-weight: 600;
            color: var(--success);
        }
        
        .category-tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            background: var(--primary-light);
            color: var(--primary);
        }
        
        .category-list {
            list-style: none;
        }
        
        .category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .category-item:last-child {
            border-bottom: none;
        }
        
        .category-name {
            font-weight: 600;
            color: var(--dark);
        }
        
        .category-amount {
            font-weight: 700;
            color: var(--success);
        }
        
        .recent-income-scroll {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .recent-income-scroll::-webkit-scrollbar {
            width: 6px;
        }
        
        .recent-income-scroll::-webkit-scrollbar-track {
            background: var(--light);
            border-radius: 10px;
        }
        
        .recent-income-scroll::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
        
        #expenseChart {
            max-width: 350px;
            height: 250px;
            margin: 0 auto;
        }
        
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--primary-light);
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .container {
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <header>
        <?php include 'header_logo.php'; ?>
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="income.php" class="active">Finance</a>
            <a href="#" onclick="showIncomeSheet(); return false;">Income</a>
            <a href="#" onclick="showExpenseSheet(); return false;">Expense</a>
            <a href="#" onclick="showEmptySheet(); return false;">Sheets</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Income Management</h1>
            <button class="add-income-btn" onclick="showAddIncomeModal()">
                <i class="fas fa-plus"></i>
                Add Income
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="value">₹<?php echo number_format($totalIncome, 2); ?></div>
                <div class="label">Total Income</div>
            </div>

            <div class="stat-card expense">
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="value">₹<?php echo number_format($totalExpense, 2); ?></div>
                <div class="label">Total Expense</div>
            </div>

            <div class="stat-card fees">
                <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="value">₹<?php echo number_format($totalDecidedFees, 2); ?></div>
                <div class="label">Expected Yearly Fees</div>
            </div>

            <div class="stat-card monthly">
                <div class="icon">
                    <i class="fas fa-calendar-month"></i>
                </div>
                <div class="value">₹<?php echo number_format($monthlyIncome, 2); ?></div>
                <div class="label">This Month Income</div>
            </div>
        </div>

        <!-- Recent Income Section -->
        <div class="income-card" style="margin-bottom: 30px;">
            <h3 class="card-title">Recent Income (Last 15 Entries)</h3>
            <?php if (empty($recentIncome)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No income entries found</p>
                    <p style="font-size: 0.9rem; margin-top: 10px;">Start by adding your first income entry</p>
                </div>
            <?php else: ?>
                <div class="recent-income-scroll">
                    <table class="income-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Source</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentIncome as $income): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($income['date'])); ?></td>
                                    <td><?php echo htmlspecialchars($income['source']); ?></td>
                                    <td><span class="category-tag"><?php echo htmlspecialchars($income['category']); ?></span></td>
                                    <td class="amount">₹<?php echo number_format($income['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($income['payment_method']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="income_list.php" style="background: var(--primary-light); color: var(--primary); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        View All Records
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Expense by Category Pie Chart -->
        <div class="income-card">
            <h3 class="card-title">Expense by Category</h3>
            <?php if (empty($expenseCategoryData)): ?>
                <div class="empty-state">
                    <i class="fas fa-chart-pie"></i>
                    <p>No expense data found</p>
                </div>
            <?php else: ?>
                <div class="chart-container">
                    <canvas id="expenseChart"></canvas>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Income Modal -->
    <div id="addIncomeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; width: 500px; max-width: 90%; max-height: 90%; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <h3 style="margin-bottom: 20px; color: var(--primary);">Add New Income</h3>
            <form id="addIncomeForm">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Source *</label>
                    <input type="text" id="incomeSource" required style="width: 100%; padding: 10px; border: 2px solid var(--light-gray); border-radius: 8px; font-size: 14px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Amount *</label>
                    <input type="number" id="incomeAmount" step="0.01" required style="width: 100%; padding: 10px; border: 2px solid var(--light-gray); border-radius: 8px; font-size: 14px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Category</label>
                    <select id="incomeCategory" style="width: 100%; padding: 10px; border: 2px solid var(--light-gray); border-radius: 8px; font-size: 14px;">
                        <option value="Fees">Fees</option>
                        <option value="Admission">Admission</option>
                        <option value="Books">Books</option>
                        <option value="Uniform">Uniform</option>
                        <option value="Transport">Transport</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Payment Method</label>
                    <select id="incomePaymentMethod" style="width: 100%; padding: 10px; border: 2px solid var(--light-gray); border-radius: 8px; font-size: 14px;">
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="UPI">UPI</option>
                        <option value="Card">Card</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Date *</label>
                    <input type="date" id="incomeDate" required style="width: 100%; padding: 10px; border: 2px solid var(--light-gray); border-radius: 8px; font-size: 14px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Description</label>
                    <textarea id="incomeDescription" rows="3" style="width: 100%; padding: 10px; border: 2px solid var(--light-gray); border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeAddIncomeModal()" style="padding: 10px 20px; background: var(--light-gray); color: var(--gray); border: none; border-radius: 8px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer;">Add Income</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Set today's date as default
        document.getElementById('incomeDate').value = new Date().toISOString().split('T')[0];
        
        // Expense Pie Chart
        <?php if (!empty($expenseCategoryData)): ?>
        const expenseData = <?php echo json_encode($expenseCategoryData); ?>;
        const ctx = document.getElementById('expenseChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: expenseData.map(item => item.category),
                datasets: [{
                    data: expenseData.map(item => item.total),
                    backgroundColor: [
                        '#4361ee',
                        '#f72585',
                        '#4cc9f0',
                        '#f8961e',
                        '#ef233c',
                        '#3a0ca3',
                        '#06ffa5',
                        '#c77dff',
                        '#ff6b35',
                        '#00d9ff'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11,
                                family: 'Inter'
                            },
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                return label + ': ₹' + value.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        function showIncomeSheet() {
            window.location.href = 'income_list.php';
        }

        function showExpenseSheet() {
            window.location.href = 'expense_list.php';
        }

        function showEmptySheet() {
            window.location.href = 'empty_sheet.php';
        }

        function showAddIncomeModal() {
            document.getElementById('addIncomeModal').style.display = 'flex';
        }

        function closeAddIncomeModal() {
            document.getElementById('addIncomeModal').style.display = 'none';
            document.getElementById('addIncomeForm').reset();
            document.getElementById('incomeDate').value = new Date().toISOString().split('T')[0];
        }

        document.getElementById('addIncomeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('source', document.getElementById('incomeSource').value);
            formData.append('amount', document.getElementById('incomeAmount').value);
            formData.append('category', document.getElementById('incomeCategory').value);
            formData.append('payment_method', document.getElementById('incomePaymentMethod').value);
            formData.append('date', document.getElementById('incomeDate').value);
            formData.append('description', document.getElementById('incomeDescription').value);

            fetch('add_income.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddIncomeModal();
                    location.reload(); // Refresh to show new data
                } else {
                    alert('Error: ' + (data.message || 'Failed to add income'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
        
        // Close modal on outside click
        document.getElementById('addIncomeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddIncomeModal();
            }
        });
    </script>
</body>
</html>