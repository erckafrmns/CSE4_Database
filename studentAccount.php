<?php
session_start();
include('connection.php');

// Check if admin is logged in
if(isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch admin details from the database
    $sql = "SELECT * FROM student WHERE StudentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student_data = $result->fetch_assoc();
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/studentAcc.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="studentAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="">Reports</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="reports/majorReport.php">Major</a></li>
                        <li><a href="reports/departmentReport.php">Department</a></li>
                        <li><a href="reports/courseReport.php">Course</a></li>
                        <li><a href="reports/majorCourseReport.php">Major-Course</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="">Account</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="account/editInfoStudent.php">Edit Information</a></li>
                        <li><a href="account/changePassStudent.php">Change Password</a></li>
                    </ul>
                </div>
            </li>
            <li><button class="SignOutBTN" onclick="window.location.href='logout.php';">Sign Out</button></li>
        </ul>
    </nav>
    
    <div class="contentPanel">

        <div class="userInfo">
            <i class="fa-solid fa-circle-user fa-2xl"></i>
            <?php
                if (isset($student_data)) {
                    echo '<h4>' . $student_data['FirstName'] . ' ' . $student_data['LastName'] . '</h4>';
                    echo '<h4 class="id"> <span>Student ID:</span> ' . $student_data['StudentID'] . '</h4>';
                    echo '<h4 class="role"> <span>Major ID:</span> ' . $student_data['MajorID'] . '</h4>';
                } else {
                    echo '<h4><i class="fa-solid fa-circle-user"></i> Admin</h4>';
                }
            ?>
        </div>

        <div class="course-table">
            
        </div>
    </div>
</body>
</html>