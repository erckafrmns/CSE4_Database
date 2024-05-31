<?php
session_start();
require '../connection.php';

// Check if an admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST["submit"])){
    $CourseID = $_POST["CourseID"];
    $CourseName = $_POST["CourseName"];
    $Credits = $_POST["Credits"];
    $selectedMajors = isset($_POST["courseSelect-major"]) ? $_POST["courseSelect-major"] : []; 

    $check_query = "SELECT * FROM course WHERE CourseID = '$CourseID'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        header("Location: course.php?error=add_error"); 
        exit();
    } else {
        $query = "INSERT INTO course VALUES ('$CourseID', '$CourseName', '$Credits')";
        mysqli_query($conn, $query);

        // Insert selected majors into the junction table
        foreach ($selectedMajors as $majorID) {
            $courseMajorQuery = "INSERT INTO major_course_jnct (MajorID, CourseID) VALUES ('$majorID', '$CourseID')";
            mysqli_query($conn, $courseMajorQuery);
        }
        
        header("Location: course.php?success=add_success"); 
        exit();
    }
}  

// Fetch all available majors from the database
$majorQuery = "SELECT * FROM major";
$majorResult = mysqli_query($conn, $majorQuery);
$majorOptions = '';
while ($row = mysqli_fetch_assoc($majorResult)) {
    $majorOptions .= "<option value='{$row['MajorID']}'>{$row['MajorName']}</option>";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Form</title>
    <link rel="stylesheet" href="../css/adminNav.css">
    <link rel="stylesheet" href="../css/forms.css">
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
                <li><a href="student.php" ><i class="fa-solid fa-user fa-sm"></i> Student Form</a></li>
                <li><a href="major.php" ><i class="fa-solid fa-graduation-cap fa-sm"></i> Major Form</a></li>
                <li><a href="department.php" ><i class="fa-solid fa-building-columns fa-sm"></i> Department Form</a></li>
                <li><a href="course.php" class="active"><i class="fa-solid fa-book-open-reader fa-sm"></i> Course Form</a></li>
            </ul>
        </div>

        <div class="content">
            
            <h1><i class="fa-solid fa-file-circle-plus"></i>  Add New Course</h1>

            <div class="form-container">
                <form action="" method="post" autocomplete="off">
                    <div class="form-grid">
                        <div class="form-column">
                            <div class="course-form-group">
                                <label for="CourseID">Course ID</label>
                                <input type="text" id="CourseID" placeholder="Course ID" name="CourseID" >
                            </div>
                            <div class="course-form-group">
                                <label for="CourseName">Course Name</label>
                                <input type="text" id="CourseName" placeholder="Course Name" name="CourseName" required>
                            </div>
                            <div class="course-form-group">
                                <label for="Credits">Credits</label>
                                <input type="number" name="Credits" placeholder="Credits" required value="">
                            </div>  
                        </div>
                        <div class="form-column">
                            <div class="multiform-group">
                                <label for="courseSelect-major">Select Major(s)</label>
                                <select id="courseSelect-major" name="courseSelect-major[]" class="courseSelect-major" multiple required>
                                    <?php echo $majorOptions; ?>
                                </select>
                            </div>
                        </div>

                        <?php if(isset($_GET['success']) && $_GET['success'] == 'add_success'): ?>
                            <script>
                                Swal.fire({
                                    icon: "success",
                                    title: "SUCCESS",
                                    text: "Course Added Successfully!",
                                    confirmButtonColor: "#2C3E50"
                                });
                            </script>
                        <?php endif; ?>

                        <?php if(isset($_GET['error']) && $_GET['error'] == 'add_error'): ?>
                            <script>
                                Swal.fire({
                                    icon: "error",
                                    title: "UNSUCCESSFUL",
                                    text: "Course ID Already Exists!",
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