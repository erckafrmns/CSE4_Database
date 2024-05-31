<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseID = $_POST['CourseID'];
    $courseName = $_POST['CourseName'];
    $credits = $_POST['Credits'];

    $sql = "UPDATE course SET CourseName='$courseName', Credits='$credits' WHERE CourseID='$courseID'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
}
?>
