<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

if (!isset($_SESSION['income_access']) || !$_SESSION['income_access']) {
    header('Location: dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT date, source, description, category, payment_method, amount FROM income WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$income_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income List | Acadexa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@12.3.1/dist/handsontable.full.min.css">
    <script src="https://cdn.jsdelivr.net/npm/handsontable@12.3.1/dist/handsontable.full.min.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --primary-dark: #3a0ca3;
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
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
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
            color: var(--primary);
            background: var(--primary-light);
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
            background: #4361ee;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        
        #saveBtn:hover {
            background: #3a0ca3;
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
            <a href="income_list.php" class="active">Income</a>
            <a href="expense_list.php">Expense</a>
            <a href="empty_sheet.php">Sheets</a>
        </nav>
    </header>

    <div class="container">
        <div class="toolbar">
            <h2>Income Records</h2>
            <div class="toolbar-right">
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
                $record['source'],
                $record['description'] ?: '',
                $record['category'],
                $record['payment_method'],
                $record['amount']
            ];
        }, $income_records)); ?>;

        const container = document.getElementById('spreadsheet');
        let hot = new Handsontable(container, {
            data: phpData,
            colHeaders: ['Date', 'Source', 'Description', 'Category', 'Payment Method', 'Amount (₹)'],
            columns: [
                { type: 'date', dateFormat: 'YYYY-MM-DD' },
                { type: 'text' },
                { type: 'text' },
                { type: 'text' },
                { type: 'text' },
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
            const existingCount = phpData.length;
            const newRecords = [];
            const newRowIndices = [];
            
            for (let i = 0; i < data.length; i++) {
                const row = data[i];
                if (row[0] && row[1] && row[3] && row[4] && row[5]) {
                    const isDuplicate = phpData.some(existing => 
                        existing[0] === row[0] && 
                        existing[1] === row[1] && 
                        existing[3] === row[3] && 
                        existing[4] === row[4] && 
                        parseFloat(existing[5]) === parseFloat(row[5])
                    );
                    
                    if (!isDuplicate) {
                        newRecords.push({
                            date: row[0],
                            source: row[1],
                            description: row[2] || '',
                            category: row[3],
                            payment_method: row[4],
                            amount: parseFloat(row[5]) || 0
                        });
                        newRowIndices.push(i);
                    }
                }
            }
            
            if (newRecords.length === 0) {
                alert('No new data to save!');
                return;
            }
            
            fetch('save_income.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ records: newRecords })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Saved ' + data.saved + ' new records!');
                    newRowIndices.forEach(idx => {
                        hot.setDataAtRow(idx, [null, null, null, null, null, null]);
                    });
                } else {
                    alert('Error: ' + (data.message || 'Failed to save'));
                }
            })
            .catch(err => alert('Save failed: ' + err.message));
        }
    </script>
</body>
</html>
