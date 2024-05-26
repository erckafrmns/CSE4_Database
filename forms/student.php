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
    <link rel="stylesheet" href="../css/student.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i>                  FORMS</h2>
        <div class="forms-items">
            <a href="student.php" class="active"><i class="fa-solid fa-user fa-sm"></i>               STUDENT</a>
            <a href="major.php"><i class="fa-solid fa-book fa-sm"></i>               MAJOR</a>
            <a href="department.php"><i class="fa-solid fa-building-columns fa-sm"></i>               DEPARTMENT</a>
            <a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i>               COURSE</a>
        </div>
        <button onclick="location.href='../reports/studentReport.php'" class="tabs"><i class="fa-regular fa-file-lines"></i>            Reports</button>
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