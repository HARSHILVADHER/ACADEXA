<?php
if (!isset($_SESSION['user_id'])) {
    echo '<div class="logo">ACADEXA</div>';
    return;
}

$user_id = $_SESSION['user_id'];
$logo_html = '<div class="logo">ACADEXA</div>';

try {
    $logo_conn = new mysqli('localhost', 'root', '', 'acadexa');
    if (!$logo_conn->connect_error) {
        $stmt = $logo_conn->prepare("SELECT logo_path FROM user_logos WHERE user_id = ? LIMIT 1");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $logo_data = $result->fetch_assoc();
        $stmt->close();
        $logo_conn->close();
        
        if ($logo_data && !empty($logo_data['logo_path'])) {
            $logo_path = htmlspecialchars($logo_data['logo_path']);
            $base_path = (basename(dirname($_SERVER['PHP_SELF'])) === 'php') ? '../../' : '../';
            $logo_html = '<div class="logo"><img src="' . $base_path . $logo_path . '" alt="Logo"></div>';
        }
    }
} catch (Exception $e) {
    // Fallback to default text
}

echo $logo_html;
?>
