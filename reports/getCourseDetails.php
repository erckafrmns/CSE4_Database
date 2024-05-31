<?php
include('../connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['CourseID'])) {
    $courseID = $_GET['CourseID'];
    
    $sql = "SELECT * FROM course WHERE CourseID = '$courseID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(array());
    }
} else {
    echo json_encode(array());
}
?>
