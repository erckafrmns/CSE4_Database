<?php
include('../connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentID = $_POST['StudentID'];
    $firstName = $_POST['FirstName'];
    $lastName = $_POST['LastName'];
    $majorID = $_POST['MajorID'];
    $email = $_POST['Email'];

    $sql = "UPDATE student SET FirstName='$firstName', LastName='$lastName', MajorID='$majorID', Email='$email' WHERE StudentID='$studentID'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
}
?>
