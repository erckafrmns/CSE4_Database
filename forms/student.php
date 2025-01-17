<?php
session_start();
require '../connection.php';

// Check if an admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

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

    // Check if a student with the same first and last name exists
    $checkStudentQuery = "SELECT * FROM student WHERE FirstName = '$FirstName' AND LastName = '$LastName'";
    $checkStudentResult = mysqli_query($conn, $checkStudentQuery);


    // Generate the password
    $Password = strtolower($LastName) . '123';

    if (mysqli_num_rows($checkMajorResult) == 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Input',
                text: 'Major ID does not exist',
                confirmButtonColor: '#2C3E50'
            });
    </script>";
    } elseif (mysqli_num_rows($checkStudentResult) > 0) {
        $studentData = [
            'StudentID' => $StudentID,
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'MajorID' => $MajorID,
            'Email' => $Email,
            'Password' => $Password
        ];
        $_SESSION['duplicate_student'] = $studentData;

        header("Location: student.php?error=duplicate_entry");
        exit();
    } else {
        $query = "INSERT INTO student VALUES ('$StudentID', '$FirstName', '$LastName', '$MajorID', '$Email', '$Password')";
        mysqli_query($conn, $query);
        header("Location: student.php?success=add_success"); 
        $StudentID = generateUniqueStudentID($conn);
    }
}


// Fetch all available majors from the database
$majorQuery = "SELECT * FROM major";
$majorResult = mysqli_query($conn, $majorQuery);
$majorOptions = '';
while ($row = mysqli_fetch_assoc($majorResult)) {
    $majorOptions .= "<option value='{$row['MajorID']}'>{$row['MajorID']} - {$row['MajorName']}</option>";
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
    <script src="../sweetalert/sweetalert2.min.js"></script>
    <script src="../sweetalert/sweetalert2.min.js/sweetalert2.all.min.js"></script>
</head>
<body>

    <nav>
        <h1><span class="sarang">SARANG </span><span class="univ">UNIVERSITY</span></h1>
        <ul>
            <li class="dashboard"><a href="../adminAccount.php">Dashboard</a></li>
            <li class="menu-dropdown"><a href="student.php">Forms</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="student.php">Student Form</a></li>
                        <li><a href="major.php">Major Form</a></li>
                        <li><a href="department.php">Department Form</a></li>
                        <li><a href="course.php">Course Form</a></li>
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
            <li class="menu-dropdown"><a href="../account/editInfoAdmin.php">Account</a>
                <div class="reports-dropdown">
                    <ul>
                        <li><a href="../account/editInfoAdmin.php">Edit Information</a></li>
                        <li><a href="../account/changePassAdmin.php">Change Password</a></li>
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
                <li><a href="student.php" class="active"><i class="fa-solid fa-user fa-sm"></i> Student Form</a></li>
                <li><a href="major.php" ><i class="fa-solid fa-graduation-cap fa-sm"></i> Major Form</a></li>
                <li><a href="department.php" ><i class="fa-solid fa-building-columns fa-sm"></i> Department Form</a></li>
                <li><a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i> Course Form</a></li>
            </ul>
        </div>

        <div class="content">
            
            <h1><i class="fa-solid fa-user-plus"></i>  Add New Student</h1>

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

                        <?php if(isset($_GET['success']) && $_GET['success'] == 'add_success'): ?>
                            <script>
                                Swal.fire({
                                    icon: "success",
                                    title: "SUCCESS",
                                    text: "Student Added Successfully!",
                                    confirmButtonColor: "#2C3E50"
                                });
                            </script>
                        <?php endif; ?>

                        <?php if(isset($_GET['error']) && $_GET['error'] == 'duplicate_entry'): ?>
                            <script>
                                Swal.fire({
                                    icon: "warning",
                                    title: "DUPLICATE ENTRY",
                                    confirmButtonText: "Add Student",
                                    showLoaderOnConfirm: true,
                                    showDenyButton: true,
                                    denyButtonText: `Cancel`,
                                    confirmButtonColor: "#2C3E50",
                                    text: "A student with this name '<?php echo $_SESSION['duplicate_student']['FirstName']; ?> <?php echo $_SESSION['duplicate_student']['LastName']; ?>' already exists! Do you still want to continue?"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'confirm_student.php';
                                    } else if (result.isDenied) {
                                        Swal.fire({
                                            icon: "error",
                                            title: "CANCELLED",
                                            confirmButtonColor: "#2C3E50"
                                        });
                                    }
                                });
                            </script>
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