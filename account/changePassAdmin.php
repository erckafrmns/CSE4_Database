<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

$stmt = $conn->prepare("SELECT * FROM admin WHERE AdminID = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin_data = $result->fetch_assoc();
} else {
    header("Location: ../index.php");
    exit();
}

$error_messages = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Verify current password
    if (!password_verify($current_password, $admin_data['Password'])) {
        $error_messages[] = 'Current Password is Incorrect';
    }

    if ($new_password != $confirm_new_password) {
        $error_messages[] = 'New Passwords Do Not Match';
    }

    if (empty($error_messages)) {        
        // Hash the new password before updating
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_stmt = $conn->prepare("UPDATE admin SET Password = ? WHERE AdminID = ?");
        $update_stmt->bind_param("ss", $hashed_password, $admin_id);

        if ($update_stmt->execute()) {
            header("Location: changePassAdmin.php?success=update_success"); 
            exit();
        } else {
            $error_messages[] = '*Error Changing Password*';
        }
    }

    if (!empty($error_messages)) {
        $_SESSION['error_messages'] = $error_messages;
        header("Location: changePassAdmin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/adminNav.css">
    <link rel="stylesheet" href="../css/changePass.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
    <script src="../sweetalert/sweetalert2.min.js"></script>
    <script src="../sweetalert/sweetalert2.min.js/sweetalert2.all.min.js"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="../adminAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="../forms/student.php">Forms</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../forms/student.php">Student Form</a></li>
                        <li><a href="../forms/major.php">Major Form</a></li>
                        <li><a href="../forms/department.php">Department Form</a></li>
                        <li><a href="../forms/course.php">Course Form</a></li>
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
            <li class="menu-dropdown"><a href="editInfoAdmin.php">Account</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="editInfoAdmin.php">Edit Information</a></li>
                        <li><a href="changePassAdmin.php">Change Password</a></li>
                    </ul>
                </div>
            </li>
            <li><button class="SignOutBTN" onclick="window.location.href='../logout.php';">Sign Out</button></li>
        </ul>
    </nav>

    <div class="contentPanel">
        <div class="header">
            <h4><i class="fa-solid fa-lock"></i>        Change Password</h4>
        </div>
        <form method="post" action="changePassAdmin.php">

            <?php if(isset($_GET['success']) && $_GET['success'] == 'update_success'): ?>
                <script>
                    Swal.fire({
                        icon: "success",
                        title: "SUCCESS",
                        text: "Password Changed Successfully!",
                        confirmButtonColor: "#2C3E50"
                    });
                </script>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_messages']) && !empty($_SESSION['error_messages'])): ?>
                <script>
                    var errorMessages = "<?php echo implode('<br>', $_SESSION['error_messages']); ?>";
                    Swal.fire({
                        icon: "error",
                        title: "UNSUCCESSFUL",
                        html: errorMessages,
                        confirmButtonColor: "#2C3E50"
                        });
                </script>
                <?php unset($_SESSION['error_messages']); ?>
            <?php endif; ?>

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
