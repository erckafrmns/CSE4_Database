<?php
require '../connection.php';

// Generate unique student ID
function generateUniqueStudentID($conn) {
    $isUnique = false;
    $studentID = '';

    while (!$isUnique) {
        $currentYear = date('y'); // Get the last two digits of the current year
        $prefix = 'SU' . $currentYear;
        $suffix = sprintf('%06d', rand(0, 999999));
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
    $Email = $_POST["Email"];

    // Check if Major ID exists in the database
    $checkMajorQuery = "SELECT * FROM major WHERE MajorID = '$MajorID'";
    $checkMajorResult = mysqli_query($conn, $checkMajorQuery);

    // Generate the password
    $Password = strtolower($LastName) . '123';

    if (mysqli_num_rows($checkMajorResult) == 0) {
        echo "<script> alert('Invalid Input: Major ID does not exist'); </script>";
    } else {
        $query = "INSERT INTO student VALUES ('$StudentID', '$FirstName', '$LastName', '$MajorID', '$Email', '$Password')";
        mysqli_query($conn, $query);
        header("Location: student.php?success=update_success"); 
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
    <link rel="stylesheet" href="../css/forms.css">
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
        <div class="header">
            <h1>FORMS</h1>
        </div>

        <div class="formsNav">
            <ul>
                <li><a href="student.php" class="active"><i class="fa-solid fa-user-plus"></i>  Add Student</a></li>
                <li><a href="major.php"><i class="fa-solid fa-file-circle-plus"></i>  Add Major</a></li>
                <li><a href="department.php"><i class="fa-solid fa-file-circle-plus"></i>  Add Department</a></li>
                <li><a href="course.php"><i class="fa-solid fa-file-circle-plus"></i>  Add Course</a></li>
            </ul>
        </div>

        <div class="content">
            
            <h1><i class="fa-solid fa-user-plus"></i>  Student Form - Add New Student</h1>

            <div class="form-container">
                <form action="" method="post" autocomplete="off">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="StudentID">Student ID</label>
                            <input type="text" id="StudentID" name="StudentID" value="<?php echo $StudentID; ?>" disabled>
                            <input type="hidden" name="StudentID" value="<?php echo $StudentID; ?>">
                        </div>
                        <div class="form-group">
                            <label for="MajorID">Major ID</label>
                            <select id="MajorID" name="MajorID" required>
                                <option value="" disabled selected>Select Major ID</option>
                                <?php echo $majorOptions; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="FirstName">First Name</label>
                            <input type="text" placeholder="First Name" name="FirstName" required>
                        </div>
                        <div class="form-group">
                            <label for="LastName">Last Name</label>
                            <input type="text" placeholder="Last Name" name="LastName" required>
                        </div>
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="email" placeholder="Email" name="Email" required>
                        </div>

                        <?php if(isset($_GET['success']) && $_GET['success'] == 'update_success'): ?>
                            <p class="success-message">*Student Added Successfully*</p>
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