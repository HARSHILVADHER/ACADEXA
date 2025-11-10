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
    <script src="https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/plugins/js/plugin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/plugins/plugins.js"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/plugins/css/pluginsCss.css' />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/plugins/plugins.css' />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/css/luckysheet.css' />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/assets/iconfont/iconfont.css' />
    <script src="https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/luckysheet.umd.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --primary-dark: #3a0ca3;
            --white: #ffffff;
            --gray: #6c757d;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
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
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
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
            padding: 0;
            height: calc(100vh - 70px);
        }
        
        #spreadsheet {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Acadexa</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="income.php">Income</a>
            <a href="income_list.php" class="active">Income List</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div id="spreadsheet"></div>
    </div>

    <script>
        window.onload = function() {
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



            luckysheet.create({
                container: 'spreadsheet',
                loading: false,
                showinfobar: false,
                data: [{
                    name: 'Income Records',
                    color: '#4361ee',
                    status: 1,
                    order: 0,
                    celldata: [
                        { r: 0, c: 0, v: { v: 'Date', bg: '#4361ee', fc: '#ffffff', bl: 1 } },
                        { r: 0, c: 1, v: { v: 'Source', bg: '#4361ee', fc: '#ffffff', bl: 1 } },
                        { r: 0, c: 2, v: { v: 'Description', bg: '#4361ee', fc: '#ffffff', bl: 1 } },
                        { r: 0, c: 3, v: { v: 'Category', bg: '#4361ee', fc: '#ffffff', bl: 1 } },
                        { r: 0, c: 4, v: { v: 'Payment Method', bg: '#4361ee', fc: '#ffffff', bl: 1 } },
                        { r: 0, c: 5, v: { v: 'Amount (₹)', bg: '#4361ee', fc: '#ffffff', bl: 1 } },
                        ...phpData.flatMap((row, i) => [
                            { r: i + 1, c: 0, v: { v: row[0] } },
                            { r: i + 1, c: 1, v: { v: row[1] } },
                            { r: i + 1, c: 2, v: { v: row[2] } },
                            { r: i + 1, c: 3, v: { v: row[3] } },
                            { r: i + 1, c: 4, v: { v: row[4] } },
                            { r: i + 1, c: 5, v: { v: row[5] } }
                        ])
                    ],
                    row: phpData.length + 50,
                    column: 6,
                    config: {
                        columnlen: { 0: 150, 1: 200, 2: 300, 3: 150, 4: 180, 5: 150 }
                    },
                    frozen: { type: 'row', range: { row_focus: 0, column_focus: 0 } }
                }],
                title: 'Income Records',
                userInfo: false,
                showsheetbar: true,
                showstatisticBar: true,
                sheetFormulaBar: true,
                enableAddRow: true,
                enableAddBackTop: false,
                lang: 'en'
            });
        };
    </script>
</body>
</html>
