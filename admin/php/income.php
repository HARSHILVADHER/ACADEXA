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

// Get income by category for pie chart
$incomeCategoryData = [];
$stmt = $conn->prepare("SELECT category, SUM(amount) as total FROM income WHERE user_id = ? GROUP BY category ORDER BY total DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $incomeCategoryData[] = $row;
}
$stmt->close();

// Calculate balance data
$balanceData = [
    ['type' => 'Income', 'amount' => $totalIncome],
    ['type' => 'Expense', 'amount' => $totalExpense]
];

// Create category table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS income_expense_category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    category_type ENUM('income', 'expense') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_category_type (category_type),
    UNIQUE KEY unique_user_category (user_id, category_name, category_type)
)");

// Get category-wise income and expense
$categoryWiseData = [];
$stmt = $conn->prepare("
    SELECT 
        c.category_name,
        c.category_type,
        COALESCE(SUM(CASE WHEN c.category_type = 'income' THEN i.amount END), 0) as income_amount,
        COALESCE(SUM(CASE WHEN c.category_type = 'expense' THEN e.amount END), 0) as expense_amount
    FROM income_expense_category c
    LEFT JOIN income i ON c.category_name = i.category AND c.user_id = i.user_id AND c.category_type = 'income'
    LEFT JOIN expense e ON c.category_name = e.category AND c.user_id = e.user_id AND c.category_type = 'expense'
    WHERE c.user_id = ?
    GROUP BY c.category_name, c.category_type
    ORDER BY c.category_type, c.category_name
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categoryWiseData[] = $row;
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

        @media (max-width: 992px) {
            div[style*="grid-template-columns: repeat(3, 1fr)"] {
                grid-template-columns: 1fr !important;
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
            <a href="income_list.php">Income</a>
            <a href="expense_list.php">Expense</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Income Management</h1>
            <button class="add-income-btn" onclick="showAddCategoryModal()">
                <i class="fas fa-plus"></i>
                Add Category
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

        <!-- Three Pie Charts in One Line -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 30px;">
            <!-- Expense Pie Chart -->
            <div class="income-card">
                <h3 class="card-title" style="font-size: 1.1rem;">Expense</h3>
                <?php if (empty($expenseCategoryData)): ?>
                    <div class="empty-state" style="padding: 20px;">
                        <i class="fas fa-chart-pie" style="font-size: 2rem;"></i>
                        <p style="font-size: 0.85rem;">No data</p>
                    </div>
                <?php else: ?>
                    <div class="chart-container" style="padding: 10px 0;">
                        <canvas id="expenseChart" style="max-height: 200px;"></canvas>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Income Pie Chart -->
            <div class="income-card">
                <h3 class="card-title" style="font-size: 1.1rem;">Income</h3>
                <?php if (empty($incomeCategoryData)): ?>
                    <div class="empty-state" style="padding: 20px;">
                        <i class="fas fa-chart-pie" style="font-size: 2rem;"></i>
                        <p style="font-size: 0.85rem;">No data</p>
                    </div>
                <?php else: ?>
                    <div class="chart-container" style="padding: 10px 0;">
                        <canvas id="incomeChart" style="max-height: 200px;"></canvas>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Balance Pie Chart -->
            <div class="income-card">
                <h3 class="card-title" style="font-size: 1.1rem;">Balance</h3>
                <div class="chart-container" style="padding: 10px 0;">
                    <canvas id="balanceChart" style="max-height: 200px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Category-wise Income and Expense Table -->
        <div class="income-card" style="margin-bottom: 30px;">
            <h3 class="card-title">Category-wise Income & Expense</h3>
            <?php if (empty($categoryWiseData)): ?>
                <div class="empty-state">
                    <i class="fas fa-table"></i>
                    <p>No categories found</p>
                    <p style="font-size: 0.9rem; margin-top: 10px;">Add categories to track income and expenses</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="income-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Type</th>
                                <th style="text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categoryWiseData as $cat): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($cat['category_name']); ?></td>
                                    <td>
                                        <span class="category-tag" style="background: <?php echo $cat['category_type'] == 'income' ? '#d1f4e0' : '#ffd4d4'; ?>; color: <?php echo $cat['category_type'] == 'income' ? '#00a651' : '#d90429'; ?>;">
                                            <?php echo ucfirst($cat['category_type']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: <?php echo $cat['category_type'] == 'income' ? 'var(--success)' : 'var(--danger)'; ?>;">
                                        ₹<?php echo number_format($cat['category_type'] == 'income' ? $cat['income_amount'] : $cat['expense_amount'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; width: 450px; max-width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <h3 style="margin-bottom: 20px; color: var(--primary);">Add New Category</h3>
            <form id="addCategoryForm">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Category Name *</label>
                    <input type="text" id="categoryName" required style="width: 100%; padding: 10px; border: 2px solid var(--light-gray); border-radius: 8px; font-size: 14px;" placeholder="e.g., Salary, Rent, Food">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Category Type *</label>
                    <select id="categoryType" required style="width: 100%; padding: 10px; border: 2px solid var(--light-gray); border-radius: 8px; font-size: 14px;">
                        <option value="">Select Type</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeAddCategoryModal()" style="padding: 10px 20px; background: var(--light-gray); color: var(--gray); border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Cancel</button>
                    <button type="submit" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartColors = [
            '#4361ee', '#f72585', '#4cc9f0', '#f8961e', '#ef233c',
            '#3a0ca3', '#06ffa5', '#c77dff', '#ff6b35', '#00d9ff'
        ];

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 8,
                        font: { size: 10, family: 'Inter' },
                        boxWidth: 10
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ₹' + value.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        };
        
        // Expense Pie Chart
        <?php if (!empty($expenseCategoryData)): ?>
        const expenseData = <?php echo json_encode($expenseCategoryData); ?>;
        new Chart(document.getElementById('expenseChart'), {
            type: 'pie',
            data: {
                labels: expenseData.map(item => item.category),
                datasets: [{
                    data: expenseData.map(item => item.total),
                    backgroundColor: chartColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: chartOptions
        });
        <?php endif; ?>

        // Income Pie Chart
        <?php if (!empty($incomeCategoryData)): ?>
        const incomeData = <?php echo json_encode($incomeCategoryData); ?>;
        new Chart(document.getElementById('incomeChart'), {
            type: 'pie',
            data: {
                labels: incomeData.map(item => item.category),
                datasets: [{
                    data: incomeData.map(item => item.total),
                    backgroundColor: chartColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: chartOptions
        });
        <?php endif; ?>

        // Balance Pie Chart
        const balanceData = <?php echo json_encode($balanceData); ?>;
        new Chart(document.getElementById('balanceChart'), {
            type: 'pie',
            data: {
                labels: balanceData.map(item => item.type),
                datasets: [{
                    data: balanceData.map(item => item.amount),
                    backgroundColor: ['#4cc9f0', '#ef233c'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: chartOptions
        });



        function showAddCategoryModal() {
            document.getElementById('addCategoryModal').style.display = 'flex';
        }

        function closeAddCategoryModal() {
            document.getElementById('addCategoryModal').style.display = 'none';
            document.getElementById('addCategoryForm').reset();
        }

        document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('category_name', document.getElementById('categoryName').value);
            formData.append('category_type', document.getElementById('categoryType').value);

            fetch('add_category.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Category added successfully!');
                    closeAddCategoryModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to add category'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
        
        // Close modal on outside click
        document.getElementById('addCategoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddCategoryModal();
            }
        });
    </script>
</body>
</html>