<?php
session_start();
require '../connection.php';

// Check if an admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST["submit"])){
    $MajorID = $_POST["MajorID"];
    $MajorName = $_POST["MajorName"];
    $DepartmentID = $_POST["DepartmentID"];

    // Check if MajorID already exists
    $check_query = "SELECT * FROM major WHERE MajorID = '$MajorID'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        header("Location: major.php?error=add_error"); 
        exit();
    } else {
        // Insert new major if MajorID does not exist
        $query = "INSERT INTO major VALUES ('$MajorID', '$MajorName', '$DepartmentID')";
        mysqli_query($conn, $query);

        // Assign default courses to the newly created major and insert it in junction table
        $default_courses = ['GEM14', 'NSTP1', 'NSTP2'];
        foreach ($default_courses as $courseID) {
            $courseMajorQuery = "INSERT INTO major_course_jnct (MajorID, CourseID) VALUES ('$MajorID', '$courseID')";
            mysqli_query($conn, $courseMajorQuery);
        }

        header("Location: major.php?success=add_success"); 
        exit();
    }
}  

// Fetch all available department from the database
$departmentQuery = "SELECT * FROM department";
$departmentResult = mysqli_query($conn, $departmentQuery);
$deptOptions = '';
while ($row = mysqli_fetch_assoc($departmentResult)) {
    $deptOptions .= "<option value='{$row['DepartmentID']}'>{$row['DepartmentID']} - {$row['DepartmentName']}</option>";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Major Form</title>
    <link rel="stylesheet" href="../css/adminNav.css">
    <link rel="stylesheet" href="../css/forms1.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
    <script src="../sweetalert/sweetalert2.min.js"></script>
    <script src="../sweetalert/sweetalert2.min.js/sweetalert2.all.min.js"></script>
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
                        <li><a href="../accoun/changePassAdmin.php">Change Password</a></li>
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
                <li><a href="major.php" class="active"><i class="fa-solid fa-graduation-cap fa-sm"></i> Major Form</a></li>
                <li><a href="department.php" ><i class="fa-solid fa-building-columns fa-sm"></i> Department Form</a></li>
                <li><a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i> Course Form</a></li>
            </ul>
        </div>

        <div class="content">
            
            <h1><i class="fa-solid fa-file-circle-plus"></i>  Add New Major</h1>

            <div class="form-container">
                <form action="" method="post" autocomplete="off">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="MajorID">Major ID</label>
                            <input type="text" id="MajorID" placeholder="Major ID" name="MajorID" >
                        </div>
                        <div class="form-group">
                            <label for="MajorName">Major Name</label>
                            <input type="text" id="MajorName" placeholder="Major Name" name="MajorName" required>
                        </div>
                        <div class="form-group">
                            <label for="DepartmentID">Department ID</label>
                            <select id="DepartmentID" name="DepartmentID" class="select-dept" required>
                                <option value="" disabled selected>Department ID</option>
                                <?php echo $deptOptions; ?>
                            </select>
                        </div>

                        <?php if(isset($_GET['success']) && $_GET['success'] == 'add_success'): ?>
                            <script>
                                Swal.fire({
                                    icon: "success",
                                    title: "SUCCESS",
                                    text: "Major Added Successfully!",
                                    confirmButtonColor: "#2C3E50"
                                });
                            </script>
                        <?php endif; ?>

                        <?php if(isset($_GET['error']) && $_GET['error'] == 'add_error'): ?>
                            <script>
                                Swal.fire({
                                    icon: "error",
                                    title: "UNSUCCESSFUL",
                                    text: "Major ID Already Exists!",
                                    confirmButtonColor: "#2C3E50"
                                    });
                            </script>
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