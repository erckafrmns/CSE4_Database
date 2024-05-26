<?php
require '../connection.php';

if (isset($_POST["submit"])){
    $CourseID = $_POST["CourseID"];
    $CourseName = $_POST["CourseName"];
    $Credits = $_POST["Credits"];
    $selectedMajors = isset($_POST["courseSelect-major"]) ? $_POST["courseSelect-major"] : []; 

    $check_query = "SELECT * FROM course WHERE CourseID = '$CourseID'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script> alert('Course ID: $CourseID already exists!'); </script>";
    } else {
        $query = "INSERT INTO course VALUES ('$CourseID', '$CourseName', '$Credits')";
        mysqli_query($conn, $query);

        // Insert selected majors into the junction table
        foreach ($selectedMajors as $majorID) {
            $courseMajorQuery = "INSERT INTO major_course_jnct (MajorID, CourseID) VALUES ('$majorID', '$CourseID')";
            mysqli_query($conn, $courseMajorQuery);
        }
        
        echo "<script> alert('$CourseName was Successfully Added!'); </script>";
    }
}  

// Fetch all available majors from the database
$majorQuery = "SELECT * FROM major";
$majorResult = mysqli_query($conn, $majorQuery);
$majorOptions = '';
while ($row = mysqli_fetch_assoc($majorResult)) {
    $majorOptions .= "<option value='{$row['MajorID']}'>{$row['MajorName']}</option>";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Form</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i>                  FORMS</h2>
        <div class="forms-items">
            <a href="student.php"><i class="fa-solid fa-user fa-sm"></i>               STUDENT</a>
            <a href="major.php"><i class="fa-solid fa-book fa-sm"></i>               MAJOR</a>
            <a href="department.php"><i class="fa-solid fa-building-columns fa-sm"></i>               DEPARTMENT</a>
            <a href="course.php" class="active"><i class="fa-solid fa-book-open-reader fa-sm"></i>               COURSE</a>
        </div>
        <button onclick="location.href='../reports/studentReport.php'" class="tabs"><i class="fa-regular fa-file-lines"></i>            Reports</button>
    </nav>

    <div class="contentPanel">
        <h1 class="courseH1"><i class="fa-solid fa-book-open-reader"></i>                         Course Form</h1>

        <div class="form-header">
            <h3><i class="fa-solid fa-file-circle-plus" style="color: #F0F0EA;"></i>                ADD NEW COURSE</h3>
        </div>
        <div class="courseForm-container">
            <form action="" method="post" autocomplete="off">
                <div class="form-group">
                    <label for="CourseID">Course ID :</label>
                    <input type="text" id="CourseID" placeholder="Enter course ID ..." name="CourseID" >
                </div>
                <div class="form-group">
                    <label for="CourseName">Course Name :</label>
                    <input type="text" id="CourseName" placeholder="Enter course name ..." name="CourseName" required>
                </div>
                <div class="form-group">
                    <label for="Credits">Credits :</label>
                    <input type="number" name="Credits" placeholder="Enter credits ..." required value="">
                </div>  
                <div class="multiform-group">
                    <label for="courseSelect-major">Select Majors :</label>
                    <select id="courseSelect-major" name="courseSelect-major[]" class="courseSelect-major" multiple required>
                        <?php echo $majorOptions; ?>
                    </select>
                </div>
                <button type="submit" class="courseSubmit" name="submit">SUBMIT      <i class="fa-solid fa-arrow-up-right-from-square fa-sm"></i></button>
            </form>
        </div>
    </div>
</body>
</html>
