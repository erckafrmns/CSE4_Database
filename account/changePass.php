<?php
session_start();
include('../connection.php');

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
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($new_password != $confirm_new_password) {
        echo "<script>alert('New passwords do not match');</script>";
    } else {
        // Verify current password
        if (password_verify($current_password, $admin_data['Password'])) {
            // Hash new password
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

            $update_sql = "UPDATE admin SET Password = ? WHERE AdminID = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_password_hashed, $admin_id);

            if ($update_stmt->execute()) {
                echo "<script>alert('Password changed successfully');</script>";
                header("Location: ../adminAccount.php");
                exit();
            } else {
                echo "<script>alert('Error changing password');</script>";
            }
        } else {
            echo "<script>alert('Current password is incorrect');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/changePass.css">
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
            <h4><i class="fa-solid fa-lock"></i>        Change Password</h4>
        </div>
        <form method="post" action="changePass.php">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" placeholder="Current Password" required><br>

            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" placeholder="New Password"required><br>

            <label for="confirm_new_password">Confirm New Password</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm New Password" required><br>

            <div class="btn">
                <button class="cancel" onclick="window.location.href='../adminAccount.php';">Cancel</button>
                <button class="change" type="submit">Change Password</button>
            </div>
        </form>
    </div>
</body>
</html>
