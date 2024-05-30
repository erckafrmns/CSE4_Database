<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT * FROM student WHERE StudentID = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student_data = $result->fetch_assoc();
} else {
    header("Location: ../index.php");
    exit();
}

$error_messages = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($current_password != $student_data['Password']) {
        $error_messages[] = '*Current Password is Incorrect*';
    }

    if ($new_password != $confirm_new_password) {
        $error_messages[] = '*New Passwords Do Not Match*';
    }

    if (empty($error_messages)) {
        $update_stmt = $conn->prepare("UPDATE student SET Password = ? WHERE StudentID = ?");
        $update_stmt->bind_param("ss", $new_password, $student_id);

        if ($update_stmt->execute()) {
            header("Location: changePassStudent.php?success=update_success"); 
            exit();
        } else {
            $error_messages[] = '*Error Changing Password*';
        }
    }

    if (!empty($error_messages)) {
        $_SESSION['error_messages'] = $error_messages;
        header("Location: changePassStudent.php");
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
    <link rel="stylesheet" href="../css/studentNav.css">
    <link rel="stylesheet" href="../css/changePass.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="../studentAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="../studentReport/majorReport.php">Reports</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../studentReport/majorReport.php">Major</a></li>
                        <li><a href="../studentReport/departmentReport.php">Department</a></li>
                        <li><a href="../studentReport/courseReport.php">Course</a></li>
                        <li><a href="../studentReport/majorCourseReport.php">Major-Course</a></li>
                    </ul>
                </div>
            </li>
            <li class="menu-dropdown"><a href="">Account</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="editInfoStudent.php">Edit Information</a></li>
                        <li><a href="changePassStudent.php">Change Password</a></li>
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
        <form method="post" action="changePassStudent.php">

            <?php if(isset($_GET['success']) && $_GET['success'] == 'update_success'): ?>
                <p class="success-message">*Password Changed Successfully*</p>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_messages']) && !empty($_SESSION['error_messages'])): ?>
                <ul class="error-messages">
                    <?php foreach($_SESSION['error_messages'] as $message): ?>
                        <li><?php echo $message; ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php unset($_SESSION['error_messages']); ?>
            <?php endif; ?>

            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" placeholder="Current Password" required><br>

            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" placeholder="New Password"required><br>

            <label for="confirm_new_password">Confirm New Password</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm New Password" required><br>
    
            <div class="btn">
                <button class="cancel" onclick="window.location.href='../studentAccount.php';">Cancel</button>
                <button class="change" type="submit">Change Password</button>
            </div>
        </form>
    </div>
</body>
</html>
