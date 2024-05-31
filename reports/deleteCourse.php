<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseID = $_POST['CourseID'];

    $sql = "DELETE FROM course WHERE CourseID='$courseID'";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();
}
?>
