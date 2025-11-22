<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] === 0) {
        $title = $_POST['title'];
        $code = $_POST['class_code'];
        $subject = $_POST['subject'];
        $type = $_POST['type'];
        $description = $_POST['description'];

        $fileName = $_FILES['material_file']['name'];
        $fileType = $_FILES['material_file']['type'];
        $fileTmpPath = $_FILES['material_file']['tmp_name'];
        $fileData = file_get_contents($fileTmpPath);

        $sql = "INSERT INTO study_materials (title, code, subject, type, description, file_name, file_type, file_data)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssssss", $title, $code, $subject, $type, $description, $fileName, $fileType, $fileData);

            if ($stmt->execute()) {
                echo "<script>alert('File uploaded successfully!'); window.location.href='../studymaterial.php';</script>";
            } else {
                echo "<script>alert('Database error'); window.location.href='../studymaterial.php';</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Database connection error'); window.location.href='../studymaterial.php';</script>";
        }
    } else {
        echo "<script>alert('File upload error'); window.location.href='../studymaterial.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request'); window.location.href='../studymaterial.php';</script>";
}
?>
