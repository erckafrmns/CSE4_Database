<?php
include('../connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $studentID = $_GET['StudentID'];
    $sql = "SELECT * FROM student WHERE StudentID='$studentID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode([]);
    }

    $conn->close();
}
?>
