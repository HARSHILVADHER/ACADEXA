<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$logo_html = 'Acadexa';

if ($user_id) {
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'logo'");
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("SELECT logo FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (!empty($row['logo'])) {
                $logo_html = '<img src="' . htmlspecialchars($row['logo']) . '" alt="Logo">';
            }
        }
        $stmt->close();
    }
}
?>
<header>
    <div class="logo"><?php echo $logo_html; ?></div>
    <nav>
        <a href="php/dashboard.php">Home</a>
        <a href="createclass.html">Classes</a>
        <a href="attendance.html">Attendance</a>
        <a href="php/gradecard.php">Reports</a>
        <a href="php/inquiry.php">Inquiries</a>
        <a href="php/profile.php">Settings</a>
    </nav>
</header>
