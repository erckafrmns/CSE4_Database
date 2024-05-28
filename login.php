<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['student_login'])) {
        $student_id = $_POST['StudentID'];
        $password = $_POST['Password']; 

        $sql = "SELECT * FROM student WHERE StudentID = ? AND Password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $student_id, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['student_id'] = $student_id;
            header("Location: studentAccount.php"); // Redirect to student dashboard
            exit();
        } else {
            header("Location: index.php?error=student_login"); 
            exit();
        }
    } elseif (isset($_POST['admin_login'])) {
        $admin_id = $_POST['AdminID']; 
        $password = $_POST['Password']; 

        $sql = "SELECT * FROM admin WHERE AdminID = ? AND Password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $admin_id, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['admin_id'] = $admin_id;
            header("Location: adminAccount.php"); // Redirect to admin dashboard
            exit();
        } else {
            header("Location: index.php?error=admin_login"); 
            exit();
        }
    }
}

$conn->close();
?>
