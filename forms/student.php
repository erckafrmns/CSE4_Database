<?php
require '../connection.php';

// Generate unique student ID
function generateUniqueStudentID($conn) {
    $isUnique = false;
    $studentID = '';

    while (!$isUnique) {
        $prefix = sprintf('%02d', rand(1, 99));
        $suffix = sprintf('%07d', rand(0, 9999999));
        $studentID = $prefix . '-' . $suffix;

        $query = "SELECT * FROM student WHERE StudentID = '$studentID'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 0) {
            $isUnique = true;
        }
    }

    return $studentID;
}

$StudentID = generateUniqueStudentID($conn);

if (isset($_POST["submit"])){
    $StudentID = $_POST["StudentID"];
    $FirstName = $_POST["FirstName"];
    $LastName = $_POST["LastName"];
    $MajorID = $_POST["MajorID"];

    // Check if Major ID exists in the database
    $checkMajorQuery = "SELECT * FROM major WHERE MajorID = '$MajorID'";
    $checkMajorResult = mysqli_query($conn, $checkMajorQuery);

    if (mysqli_num_rows($checkMajorResult) == 0) {
        echo "<script> alert('Invalid Input: Major ID does not exist'); </script>";
    } else {
        $query = "INSERT INTO student VALUES ('$StudentID', '$FirstName', '$LastName', '$MajorID')";
        mysqli_query($conn, $query);
        echo "<script> alert('$FirstName $LastName was Successfully Added!'); </script>";
        $StudentID = generateUniqueStudentID($conn);
    }
}


// Fetch all available majors from the database
$majorQuery = "SELECT * FROM major";
$majorResult = mysqli_query($conn, $majorQuery);
$majorOptions = '';
while ($row = mysqli_fetch_assoc($majorResult)) {
    $majorOptions .= "<option value='{$row['MajorID']}'>{$row['MajorID']}</option>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Form</title>
    <link rel="stylesheet" href="../css/adminNav.css">
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
                        <li><a href="student.php">Add Student</a></li>
                        <li><a href="major.php">Add Major</a></li>
                        <li><a href="department.php">Add Department</a></li>
                        <li><a href="course.php">Add Course</a></li>
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
                        <li><a href="editInfoAdmin.php">Edit Information</a></li>
                        <li><a href="changePassAdmin.php">Change Password</a></li>
                    </ul>
                </div>
            </li>
            <li><button class="SignOutBTN" onclick="window.location.href='../logout.php';">Sign Out</button></li>
        </ul>
    </nav>

    <div class="contentPanel">
        <h1><i class="fa-solid fa-user-pen" style="color: #14202b;"></i>  Student Form</h1>

        <div class="form-header">
            <h3><i class="fa-solid fa-user-plus" style="color: #F0F0EA;"></i>                ADD NEW STUDENT</h3>
        </div>
        <div class="form-container">
            <form action="" class="" method="post" autocomplete="off">
                <div class="form-group">
                    <label for="StudentID">Student No. :</label>
                    <input type="text" id="StudentID" name="StudentID" value="<?php echo $StudentID; ?>" disabled>
                    <input type="hidden" name="StudentID" value="<?php echo $StudentID; ?>">
                </div>
                <div class="form-group">
                    <label for="FirstName">First Name :</label>
                    <input type="text" placeholder="Enter your first name ..." name="FirstName" required>
                </div>
                <div class="form-group">
                    <label for="LastName">Last Name :</label>
                    <input type="text" placeholder="Enter your last name ..." name="LastName" required>
                </div>
                <div class="form-group">
                    <label for="MajorID">Major ID :</label>
                    <select id="MajorID" name="MajorID" required>
                        <option value="" disabled selected>Select Major ID ...</option>
                        <?php echo $majorOptions; ?>
                    </select>
                </div>
                <button type="submit" class="submitBTN" name="submit">SUBMIT      <i class="fa-solid fa-arrow-up-right-from-square fa-sm"></i></button>
            </form>
        </div>
    </div>
    
</body>
</html>