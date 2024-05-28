<?php
session_start();
include('../connection.php');

if(isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];

    $sql = "SELECT * FROM admin WHERE AdminID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin_data = $result->fetch_assoc();
    } else {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $update_sql = "UPDATE admin SET FirstName = ?, LastName = ?, Email = ? WHERE AdminID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssss", $first_name, $last_name, $email, $admin_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Information updated successfully');</script>";
        header("Location: ../adminAccount.php");
        exit();
    } else {
        echo "<script>alert('Error updating information');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Information</title>
    <link rel="stylesheet" href="../css/editInfo.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="../adminAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="">Forms</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../forms/student.php">Add Student</a></li>
                        <li><a href="../forms/major.php">Add Major</a></li>
                        <li><a href="../forms/department.php">Add Department</a></li>
                        <li><a href="../forms/course.php">Add Course</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="">Reports</a>
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
            <li class="menu-dropdown"><a href="">Account</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../account/editInfo.php?admin_id=<?php echo $admin_id; ?>">Edit Information</a></li>
                        <li><a href="../account/changePass.php?admin_id=<?php echo $admin_id; ?>">Change Password</a></li>
                    </ul>
                </div>
            </li>
            <li><button class="SignOutBTN" onclick="window.location.href='logout.php';">Sign Out</button></li>
        </ul>
    </nav>

    <div class="contentPanel">
        <div class="header">
            <h4><i class="fa-solid fa-user-pen"></i>        Edit Information</h4>
        </div>
        <form method="post" action="editInfo.php">
            <label for="admin_id">Admin ID</label>
            <input class="admId" type="text" id="admin_id" name="admin_id" value="<?php echo $admin_data['AdminID']; ?>" readonly><br>
            
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $admin_data['FirstName']; ?>" required><br>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $admin_data['LastName']; ?>" required><br>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $admin_data['Email']; ?>" required><br>

            <div class="btn">
                <button class="cancel" onclick="window.location.href='../adminAccount.php';">Cancel</button>
                <button class="save" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
    
</body>
</html>
