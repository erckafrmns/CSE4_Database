<?php
session_start();
include('connection.php');

// Check if admin is logged in
if(isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];

    // Fetch admin details from the database
    $sql = "SELECT * FROM admin WHERE AdminID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin_data = $result->fetch_assoc();
    } else {
        header("Location: index.php");
        exit();
    }

    // Fetch total counts
    $total_students = $conn->query("SELECT COUNT(*) AS count FROM student")->fetch_assoc()['count'];
    $total_majors = $conn->query("SELECT COUNT(*) AS count FROM major")->fetch_assoc()['count'];
    $total_departments = $conn->query("SELECT COUNT(*) AS count FROM department")->fetch_assoc()['count'];
    $total_courses = $conn->query("SELECT COUNT(*) AS count FROM course")->fetch_assoc()['count'];

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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/adminNav.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="adminAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="forms/student.php">Forms</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="forms/student.php">Student Form</a></li>
                        <li><a href="forms/major.php">Major Form</a></li>
                        <li><a href="forms/department.php">Department Form</a></li>
                        <li><a href="forms/course.php">Course Form</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="reports/studentReport.php">Reports</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="reports/studentReport.php">Student</a></li>
                        <li><a href="reports/majorReport.php">Major</a></li>
                        <li><a href="reports/departmentReport.php">Department</a></li>
                        <li><a href="reports/courseReport.php">Course</a></li>
                        <li><a href="reports/majorCourseReport.php">Major-Course</a></li>
                        <li><a href="reports/studentCoursesReport.php">Student-Course</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="account/editInfoAdmin.php">Account</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="account/editInfoAdmin.php">Edit Information</a></li>
                        <li><a href="account/changePassAdmin.php">Change Password</a></li>
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
                if (isset($admin_data)) {
                    echo '<h4>' . $admin_data['FirstName'] . ' ' . $admin_data['LastName'] . '</h4>';
                    echo '<h4 class="id"> <span>Admin ID:</span> ' . $admin_data['AdminID'] . '</h4>';
                } else {
                    echo '<h4><i class="fa-solid fa-circle-user"></i> Admin</h4>';
                }
            ?>
            <h4 class="role"><span>Role:</span> Administrator</h4>
        </div>

        <div class="summary">
            <h3><i class="fa-solid fa-rectangle-list"></i> Summary of Reports</h3>
            <div class="summary-container">
                <div class="student-summary">
                    <i class="fa-solid fa-user"></i>
                    <h1>Total Student</h1>
                    <p><?php echo $total_students; ?></p>
                </div>
                <div class="major-summary">
                    <i class="fa-solid fa-graduation-cap"></i>    
                    <h1>Total Major</h1>
                    <p><?php echo $total_majors; ?></p>
                </div>
                <div class="department-summary">
                    <i class="fa-solid fa-building-columns"></i>
                    <h1>Total Department</h1>
                    <p><?php echo $total_departments; ?></p>
                </div>
                <div class="course-summary">
                    <i class="fa-solid fa-book-open-reader"></i>
                    <h1>Total Course</h1>
                    <p><?php echo $total_courses; ?></p>
                </div>
            </div>
        </div>
        <div class="misc">
            <h3><i class="fa-solid fa-location-pin"></i> What would you like to do?</h3>
            <div class="misc-container">
                <ul>
                    <li><a href="forms/student.php">Add Student</a></li>
                    <li><a href="reports/studentReport.php">View Student Report</a></li>
                    <li><a href="forms/major.php">Add Major</a></li>
                    <li><a href="reports/majorReport.php">View Major Report</a></li>
                    <li><a href="forms/department.php">Add Department</a></li>
                    <li><a href="reports/departmentReport.php">View Department Report</a></li>
                    <li><a href="forms/course.php">Add Course</a></li>
                    <li><a href="reports/courseReport.php">View Course Report</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>