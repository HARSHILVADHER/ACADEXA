<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT date, description, category, payment_method, amount FROM expense WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$expense_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT SUM(amount) as total FROM expense WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_expense = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

// Get expense categories
$stmt = $conn->prepare("SELECT category_name FROM income_expense_category WHERE user_id = ? AND category_type = 'expense' ORDER BY category_name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$expense_categories = [];
while ($row = $result->fetch_assoc()) {
    $expense_categories[] = $row['category_name'];
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense List | Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@12.3.1/dist/handsontable.full.min.css">
    <script src="https://cdn.jsdelivr.net/npm/handsontable@12.3.1/dist/handsontable.full.min.js"></script>
    <style>
        :root {
            --primary: #dc3545;
            --primary-light: #f8d7da;
            --primary-dark: #c82333;
            --white: #ffffff;
            --gray: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        html, body {
            height: 100%;
            background-color: #f5f7ff;
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
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        nav a:hover, nav a.active {
            color: #4361ee;
            background: #e0e7ff;
        }
        
        .container {
            padding: 20px;
            height: calc(100vh - 76px);
            overflow: auto;
        }
        
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .toolbar h2 {
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .toolbar-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .total-display {
            padding: 10px 20px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }
        
        .row-selector {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .row-selector label {
            font-size: 14px;
            font-weight: 500;
        }
        
        .row-selector select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        #saveBtn {
            padding: 10px 25px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        
        #saveBtn:hover {
            background: #c82333;
        }
        
        #spreadsheet {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px;
        }
    </style>
</head>
<body>
    <header>
        <?php include 'header_logo.php'; ?>
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="income.php">Finance</a>
            <a href="income_list.php">Income</a>
            <a href="expense_list.php" class="active">Expense</a>
        </nav>
    </header>

    <div class="container">
        <div class="toolbar">
            <h2>Expense Records</h2>
            <div class="toolbar-right">
                <div class="total-display">
                    Total: ₹<?php echo number_format($total_expense, 2); ?>
                </div>
                <div class="row-selector">
                    <label>Rows:</label>
                    <select id="rowSelector" onchange="updateRows()">
                        <option value="50">50</option>
                        <option value="100" selected>100</option>
                        <option value="200">200</option>
                        <option value="500">500</option>
                    </select>
                </div>
                <button id="saveBtn" onclick="saveAllData()">💾 Save Data</button>
            </div>
        </div>
        <div id="spreadsheet"></div>
    </div>

    <script>
        const phpData = <?php echo json_encode(array_map(function($record) {
            return [
                $record['date'],
                $record['description'] ?: '',
                $record['category'],
                $record['payment_method'],
                $record['amount']
            ];
        }, $expense_records)); ?>;

        const expenseCategories = <?php echo json_encode($expense_categories); ?>;

        const container = document.getElementById('spreadsheet');
        let hot = new Handsontable(container, {
            data: phpData,
            colHeaders: ['Date', 'Description', 'Category', 'Mode', 'Amount (₹)'],
            columns: [
                { type: 'date', dateFormat: 'YYYY-MM-DD' },
                { type: 'text' },
                { type: 'dropdown', source: expenseCategories },
                { type: 'dropdown', source: ['Cash', 'Bank Transfer', 'UPI', 'Card', 'Cheque'] },
                { type: 'numeric', numericFormat: { pattern: '0,0.00' } }
            ],
            rowHeaders: true,
            width: '100%',
            height: 'auto',
            minSpareRows: 100,
            stretchH: 'all',
            licenseKey: 'non-commercial-and-evaluation',
            contextMenu: true,
            manualColumnResize: true,
            manualRowResize: true,
            filters: true,
            dropdownMenu: true
        });
        
        function updateRows() {
            const rowCount = parseInt(document.getElementById('rowSelector').value);
            hot.updateSettings({ minSpareRows: rowCount });
        }

        function saveAllData() {
            const data = hot.getData();
            const newRecords = [];
            
            for (let i = 0; i < data.length; i++) {
                const row = data[i];
                if (row[0] && row[2] && row[3] && row[4]) {
                    newRecords.push({
                        date: row[0],
                        description: row[1] || '',
                        category: row[2],
                        payment_method: row[3],
                        amount: parseFloat(row[4]) || 0
                    });
                }
            }
            
            if (newRecords.length === 0) {
                alert('No new data to save!');
                return;
            }
            
            fetch('save_expense.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ records: newRecords })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Saved ' + data.saved + ' new records!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save'));
                }
            })
            .catch(err => alert('Save failed: ' + err.message));
        }
    </script>
</body>
</html>pense.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ records: newRecords })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Saved ' + data.saved + ' new records!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save'));
                }
            })
            .catch(err => alert('Save failed: ' + err.message));
        }
    </script>
</body>
</html>
