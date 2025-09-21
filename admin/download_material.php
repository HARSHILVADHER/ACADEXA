<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT file_name, file_type, file_data FROM study_materials WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        header('Content-Type: ' . $row['file_type']);
        header('Content-Disposition: attachment; filename="' . $row['file_name'] . '"');
        echo $row['file_data'];
    } else {
        echo "File not found.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}
?>