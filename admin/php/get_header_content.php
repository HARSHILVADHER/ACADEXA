<?php
session_start();
header('Content-Type: application/json');

$logo_html = 'Acadexa';

if (isset($_SESSION['user_id'])) {
    try {
        $conn = new mysqli('localhost', 'root', '', 'acadexa');
        if (!$conn->connect_error) {
            $stmt = $conn->prepare("SELECT logo_path FROM user_logos WHERE user_id = ? LIMIT 1");
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $logo_data = $result->fetch_assoc();
            $stmt->close();
            $conn->close();
            
            if ($logo_data && !empty($logo_data['logo_path'])) {
                $logo_html = '<img src="php/' . htmlspecialchars($logo_data['logo_path']) . '" alt="Logo" style="height:40px;width:auto;object-fit:contain;">';
            }
        }
    } catch (Exception $e) {
        // Fallback to default
    }
}

echo json_encode(['logo' => $logo_html]);
?>
