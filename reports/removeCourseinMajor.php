<?php
include('../connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['CourseID']) && isset($_POST['MajorID'])) {
        $courseID = $conn->real_escape_string($_POST['CourseID']);
        $majorID = $conn->real_escape_string($_POST['MajorID']);

        // SQL query to delete the course from the major_course_jnct table
        $sql = "DELETE FROM major_course_jnct WHERE CourseID = '$courseID' AND MajorID = '$majorID'";
        
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}
?>
