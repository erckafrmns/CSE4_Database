<?php
session_start();
include('connection.php');

if(isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch student, major, and department details from the database
    $sql = "
        SELECT 
            student.*, 
            major.MajorName, 
            department.DepartmentName 
        FROM 
            student 
        JOIN 
            major ON student.MajorID = major.MajorID 
        JOIN 
            department ON major.DepartmentID = department.DepartmentID 
        WHERE 
            student.StudentID = ?";
            
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

    // Fetch courses for the student's major
    $course_sql = "
        SELECT 
            course.CourseID, 
            course.CourseName, 
            course.Credits 
        FROM 
            course 
        JOIN 
            major_course_jnct ON course.CourseID = major_course_jnct.CourseID 
        WHERE 
            major_course_jnct.MajorID = ?";
    
    $course_stmt = $conn->prepare($course_sql);
    $course_stmt->bind_param("s", $student_data['MajorID']);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    $courses = $course_result->fetch_all(MYSQLI_ASSOC);


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
            <li class="menu-dropdown"><a href="studentReport/majorReport.php">Reports</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="studentReport/majorReport.php">Major</a></li>
                        <li><a href="studentReport/departmentReport.php">Department</a></li>
                        <li><a href="studentReport/courseReport.php">Course</a></li>
                        <li><a href="studentReport/majorCourseReport.php">Major-Course</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="account/editInfoStudent.php">Account</a>
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

            <div class="name">
                <!-- <i class="fa-duotone fa-circle-user" style="--fa-primary-color: #ffc470; --fa-secondary-color: #2c3e50; --fa-secondary-opacity: 1;"></i> -->
                <i class="fa-solid fa-circle-user fa-2xl"></i>
                <?php echo '<h1>' . $student_data['FirstName'] . ' ' . $student_data['LastName'] . '</h1>'; ?>
            </div>

            <div class="info">
                <div class="row2">
                    <?php 
                        echo '<h4 class="stuID"> <span>Student ID:</span> ' . $student_data['StudentID'] . '</h4>'; 
                        echo '<h4 class="majorID"> <span>Major ID:</span> ' . $student_data['MajorID'] . '</h4>';
                    ?>
                </div>

                <div class="row3">
                    <?php 
                        echo '<h4 class="deptName"> <span>Department Name:</span> ' . $student_data['DepartmentName'] . '</h4>'; 
                        echo '<h4 class="majorName"> <span>Major Name:</span> ' . $student_data['MajorName'] . '</h4>';
                    ?>
                </div>
            </div>

        </div>

        <div class="course-table">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Credits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $index => $course): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $course['CourseID']; ?></td>
                            <td><?php echo $course['CourseName']; ?></td>
                            <td><?php echo $course['Credits']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>