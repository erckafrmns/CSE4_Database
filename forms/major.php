<?php
require '../connection.php';

if (isset($_POST["submit"])){
    $MajorID = $_POST["MajorID"];
    $MajorName = $_POST["MajorName"];
    $DepartmentID = $_POST["DepartmentID"];

    // Check if MajorID already exists
    $check_query = "SELECT * FROM major WHERE MajorID = '$MajorID'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script> alert('Major ID: $MajorID already exists!'); </script>";
    } else {
        // Insert new major if MajorID does not exist
        $query = "INSERT INTO major VALUES ('$MajorID', '$MajorName', '$DepartmentID')";
        mysqli_query($conn, $query);

        // Assign default courses to the newly created major and insert it in junction table
        $default_courses = ['GEM14', 'NSTP1', 'NSTP2'];
        foreach ($default_courses as $courseID) {
            $courseMajorQuery = "INSERT INTO major_course_jnct (MajorID, CourseID) VALUES ('$MajorID', '$courseID')";
            mysqli_query($conn, $courseMajorQuery);
        }

        echo "<script> alert('$MajorName Major was Successfully Added!'); </script>";
    }
}  

// Fetch all available department from the database
$departmentQuery = "SELECT * FROM department";
$departmentResult = mysqli_query($conn, $departmentQuery);
$deptOptions = '';
while ($row = mysqli_fetch_assoc($departmentResult)) {
    $deptOptions .= "<option value='{$row['DepartmentID']}'>{$row['DepartmentID']}</option>";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Major Form</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i>                  FORMS</h2>
        <div class="forms-items">
            <a href="student.php"><i class="fa-solid fa-user fa-sm"></i>               STUDENT</a>
            <a href="major.php" class="active"><i class="fa-solid fa-book fa-sm"></i>               MAJOR</a>
            <a href="department.php"><i class="fa-solid fa-building-columns fa-sm"></i>               DEPARTMENT</a>
            <a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i>               COURSE</a>
        </div>
        <button onclick="location.href='../reports/studentReport.php'" class="tabs"><i class="fa-regular fa-file-lines"></i>            Reports</button>
    </nav>

    <div class="contentPanel">
        <h1 class="defaultH1"><i class="fa-solid fa-book"></i>                         Major Form</h1>

        <div class="form-header">
            <h3><i class="fa-solid fa-file-circle-plus" style="color: #F0F0EA;"></i>                ADD NEW MAJOR</h3>
        </div>
        <div class="form-container">
            <form action="" method="post" autocomplete="off">
                <div class="form-group">
                    <label for="MajorID">Major ID :</label>
                    <input type="text" id="MajorID" placeholder="Enter major ID ..." name="MajorID" >
                </div>
                <div class="form-group">
                    <label for="MajorName">Major Name :</label>
                    <input type="text" id="MajorName" placeholder="Enter major name ..." name="MajorName" required>
                </div>
                <div class="form-group">
                    <label for="DepartmentID">Department ID :</label>
                    <select id="DepartmentID" name="DepartmentID" class="select-dept" required>
                        <option value="" disabled selected>Select Department ID ...</option>
                        <?php echo $deptOptions; ?>
                    </select>
                </div>
                <button type="submit" class="submitBTN" name="submit">SUBMIT      <i class="fa-solid fa-arrow-up-right-from-square fa-sm"></i></button>
            </form>
        </div>
    </div>
    
</body>
</html>
