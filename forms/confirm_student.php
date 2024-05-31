<?php
session_start();
require '../connection.php';

if (!isset($_SESSION['duplicate_student'])) {
    header("Location: student.php");
    exit();
}

$studentData = $_SESSION['duplicate_student'];
$StudentID = $studentData['StudentID'];
$FirstName = $studentData['FirstName'];
$LastName = $studentData['LastName'];
$MajorID = $studentData['MajorID'];
$Email = $studentData['Email'];
$Password = $studentData['Password'];

$query = "INSERT INTO student VALUES ('$StudentID', '$FirstName', '$LastName', '$MajorID', '$Email', '$Password')";
if (mysqli_query($conn, $query)) {
    unset($_SESSION['duplicate_student']);
    header("Location: student.php?success=add_success");
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
}
?>
