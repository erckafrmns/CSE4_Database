<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $majorID = $_POST['MajorID'];
    $majorName = $_POST['MajorName'];
    $departmentID = $_POST['DepartmentID'];

    $sql = "UPDATE major SET MajorName='$majorName', DepartmentID='$departmentID' WHERE MajorID='$majorID'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
}
?>
