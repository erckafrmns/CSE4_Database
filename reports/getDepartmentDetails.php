<?php
include('../connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['DepartmentID'])) {
    $departmentID = $_GET['DepartmentID'];
    
    $sql = "SELECT * FROM department WHERE DepartmentID = '$departmentID'";
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
