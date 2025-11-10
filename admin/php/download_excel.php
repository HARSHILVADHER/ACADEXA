<?php
session_start(); // Add session to get user_id

// Connection
include 'config.php';

// Get user_id from session
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("Unauthorized access.");
}

// Headers for Excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=students_list.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Start of table
echo "<table border='1'>";
echo "<tr>
        <th>Name</th>
        <th>Age</th>
        <th>Student Contact</th>
        <th>Parent Contact</th>
        <th>Email</th>
        <th>Class Code</th>
      </tr>";

// Fetch and print only this user's students
$stmt = $conn->prepare("SELECT name, age, student_contact, parent_contact, email, class_code FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['age']) . "</td>
            <td>" . htmlspecialchars($row['student_contact']) . "</td>
            <td>" . htmlspecialchars($row['parent_contact']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['class_code']) . "</td>
          </tr>";
}
echo "</table>";

$stmt->close();
$conn->close();
?>