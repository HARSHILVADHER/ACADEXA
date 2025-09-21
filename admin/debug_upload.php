<?php
require_once 'config.php';

echo "<h3>Database Connection Test</h3>";
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
} else {
    echo "Database connected successfully<br>";
}

echo "<h3>Tables Check</h3>";
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    echo "Table: " . $row[0] . "<br>";
}

echo "<h3>Classes Data</h3>";
$result = $conn->query("SELECT * FROM classes");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Code: " . $row['code'] . " - Name: " . $row['name'] . "<br>";
    }
} else {
    echo "No classes found<br>";
}

echo "<h3>Study Materials Data</h3>";
$result = $conn->query("SELECT id, title, code, subject, type FROM study_materials");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Title: " . $row['title'] . " - Code: " . $row['code'] . "<br>";
    }
} else {
    echo "No materials found<br>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received</h3>";
    echo "Title: " . ($_POST['title'] ?? 'Not set') . "<br>";
    echo "Class Code: " . ($_POST['class_code'] ?? 'Not set') . "<br>";
    echo "Subject: " . ($_POST['subject'] ?? 'Not set') . "<br>";
    echo "Type: " . ($_POST['type'] ?? 'Not set') . "<br>";
    echo "Description: " . ($_POST['description'] ?? 'Not set') . "<br>";
    
    if (isset($_FILES['material_file'])) {
        echo "File Name: " . $_FILES['material_file']['name'] . "<br>";
        echo "File Size: " . $_FILES['material_file']['size'] . "<br>";
        echo "File Error: " . $_FILES['material_file']['error'] . "<br>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <h3>Test Upload Form</h3>
    <input type="text" name="title" placeholder="Title" required><br><br>
    <select name="class_code" required>
        <option value="">Select Class</option>
        <?php
        $result = $conn->query("SELECT code, name FROM classes");
        while($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['code'] . "'>" . $row['name'] . "</option>";
        }
        ?>
    </select><br><br>
    <input type="text" name="subject" placeholder="Subject" required><br><br>
    <select name="type" required>
        <option value="">Select Type</option>
        <option value="notes">Notes</option>
        <option value="assignment">Assignment</option>
    </select><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <input type="file" name="material_file" required><br><br>
    <button type="submit">Test Upload</button>
</form>