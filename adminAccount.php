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
    <title>Sarang University - ADMIN</title>
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="adminAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="">Forms</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="forms/student.php">Add Student</a></li>
                        <li><a href="forms/major.php">Add Major</a></li>
                        <li><a href="forms/department.php">Add Department</a></li>
                        <li><a href="forms/course.php">Add Course</a></li>
                    </ul>
                </div>
            </li>
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
                        <li><a href="account/editInfo.php?admin_id=<?php echo $admin_id; ?>">Edit Information</a></li>
                        <li><a href="account/changePass.php?admin_id=<?php echo $admin_id; ?>">Change Password</a></li>
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
            <h3>Summary of Reports</h3>
            <div class="summary-container">
                <div class="student-summary">
                    <h1>Total Student</h1>
                </div>
                <div class="major-summary">
                    <h1>Total Major</h1>
                </div>
                <div class="department-summary">
                    <h1>Total Department</h1>
                </div>
                <div class="course-summary">
                    <h1>Total Course</h1>
                </div>
            </div>
        </div>
        <div class="misc">
            <h3>What would you like to do?</h3>
            <div class="misc-container">
                <ul>
                    <li><a href="">Add Student</a></li>
                    <li><a href="">View Student Report</a></li>
                    <li><a href="">Add Major</a></li>
                    <li><a href="">View Major Report</a></li>
                    <li><a href="">Add Department</a></li>
                    <li><a href="">View Department Report</a></li>
                    <li><a href="">Add Course</a></li>
                    <li><a href="">View Course Report</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>