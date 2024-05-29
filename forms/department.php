<?php
session_start();
require '../connection.php';

// Check if an admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST["submit"])){
    $DepartmentID = $_POST["DepartmentID"];
    $DepartmentName = $_POST["DepartmentName"];
    $Location = $_POST["Location"];

    $check_query = "SELECT * FROM department WHERE DepartmentID = '$DepartmentID'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        header("Location: department.php?error=add_error"); 
        exit();
    } else {
        $query = "INSERT INTO department VALUES ('$DepartmentID', '$DepartmentName', '$Location')";
        mysqli_query($conn, $query);
        header("Location: department.php?success=add_success"); 
        exit();
    }
}  

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Form</title>
    <link rel="stylesheet" href="../css/adminNav.css">
    <link rel="stylesheet" href="../css/forms1.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="../adminAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="student.php">Forms</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="student.php">Student Form</a></li>
                        <li><a href="major.php">Major Form</a></li>
                        <li><a href="department.php">Department Form</a></li>
                        <li><a href="course.php">Course Form</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="../reports/studentReport.php">Reports</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../reports/studentReport.php">Student</a></li>
                        <li><a href="../reports/majorReport.php">Major</a></li>
                        <li><a href="../reports/departmentReport.php">Department</a></li>
                        <li><a href="../reports/courseReport.php">Course</a></li>
                        <li><a href="../reports/majorCourseReport.php">Major-Course</a></li>
                        <li><a href="../reports/studentCoursesReport.php">Student-Course</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="../account/editInfoAdmin.php">Account</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../account/editInfoAdmin.php">Edit Information</a></li>
                        <li><a href="../account/changePassAdmin.php">Change Password</a></li>
                    </ul>
                </div>
            </li>
            <li><button class="SignOutBTN" onclick="window.location.href='../logout.php';">Sign Out</button></li>
        </ul>
    </nav>

    <div class="contentPanel">
        <div class="header">
            <h1>FORMS</h1>
        </div>

        <div class="formsNav">
            <ul>
                <li><a href="student.php"><i class="fa-solid fa-user fa-sm"></i> Student Form</a></li>
                <li><a href="major.php"><i class="fa-solid fa-graduation-cap fa-sm"></i> Major Form</a></li>
                <li><a href="department.php" class="active"><i class="fa-solid fa-building-columns fa-sm"></i> Department Form</a></li>
                <li><a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i> Course Form</a></li>
            </ul>
        </div>

        <div class="content">
            
            <h1><i class="fa-solid fa-file-circle-plus"></i>  Add New Department</h1>

            <div class="form-container">
                <form action="" method="post" autocomplete="off">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="DepartmentID">Department ID</label>
                            <input type="text" id="DepartmentID" placeholder="Department ID" name="DepartmentID" >
                        </div>
                        <div class="form-group">
                            <label for="DepartmentName">Department Name</label>
                            <input type="text" id="DepartmentName" placeholder="Department Name" name="DepartmentName" required>
                        </div>
                        <div class="form-group">
                            <label for="Location">Location</label>
                            <input type="text" name="Location" placeholder="Location" required value="">
                        </div>

                        <?php if(isset($_GET['success']) && $_GET['success'] == 'add_success'): ?>
                            <p class="success-message">*Department Added Successfully*</p>
                        <?php endif; ?>

                        <?php if(isset($_GET['error']) && $_GET['error'] == 'add_error'): ?>
                            <p class="error-message">*Department ID Already Exists*</p>
                        <?php endif; ?>

                        <div class="form-group button-group">
                            <button type="submit" class="submitBTN" name="submit">SUBMIT <i class="fa-solid fa-arrow-up-right-from-square fa-sm"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>