<?php
session_start();
include('../connection.php');

if(isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    $sql = "SELECT * FROM student WHERE StudentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student_data = $result->fetch_assoc();
    } else {
        header("Location: ../index.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        $update_sql = "UPDATE student SET FirstName = ?, LastName = ?, Email = ? WHERE StudentID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssss", $first_name, $last_name, $email, $student_id);

        if ($update_stmt->execute()) {
            header("Location: editInfoStudent.php?success=update_success"); 
            exit();
        } else {
            header("Location: editInfoStudent.php?error=update_error"); 
            exit();
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Information</title>
    <link rel="stylesheet" href="../css/studentNav.css">
    <link rel="stylesheet" href="../css/editInfo.css">
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
            <li class="menu-dropdown"><a href="editInfoStudent.php">Account</a>
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
            <h4><i class="fa-solid fa-user-pen"></i>        Edit Information</h4>
        </div>
        <form method="post" action="editInfoStudent.php">

            <?php if(isset($_GET['success']) && $_GET['success'] == 'update_success'): ?>
                <p class="success-message">*Information Updated Successfully*</p>
            <?php endif; ?>
            <?php if(isset($_GET['error']) && $_GET['error'] == 'update_error'): ?>
                <p class="error-message">*Error Updating Information*</p>
            <?php endif; ?>

            <label for="student_id">Admin ID</label>
            <input class="admId" type="text" id="student_id" name="student_id" value="<?php echo $student_data['StudentID']; ?>" readonly><br>
            
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $student_data['FirstName']; ?>" required><br>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $student_data['LastName']; ?>" required><br>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $student_data['Email']; ?>" required><br>

            <div class="btn">
                <button class="cancel" type="button" onclick="window.location.href='../studentAccount.php';">Cancel</button>
                <button class="save" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
    
</body>
</html>
