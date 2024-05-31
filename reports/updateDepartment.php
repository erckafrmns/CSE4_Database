<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departmentID = $_POST['DepartmentID'];
    $departmentName = $_POST['DepartmentName'];
    $location = $_POST['Location'];

    $sql = "UPDATE department SET DepartmentName='$departmentName', Location='$location' WHERE DepartmentID='$departmentID'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
}
?>
