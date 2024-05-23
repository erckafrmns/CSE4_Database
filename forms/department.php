<?php
require '../connection.php';

if (isset($_POST["submit"])){
    $DepartmentID = $_POST["DepartmentID"];
    $DepartmentName = $_POST["DepartmentName"];
    $Location = $_POST["Location"];

    $check_query = "SELECT * FROM department WHERE DepartmentID = '$DepartmentID'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script> alert('Department ID: $DepartmentID already exists!'); </script>";
    } else {
        $query = "INSERT INTO department VALUES ('$DepartmentID', '$DepartmentName', '$Location')";
        mysqli_query($conn, $query);
        echo "<script> alert('$DepartmentName was Successfully Added!'); </script>";
    }
}  

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Form</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i>                  FORMS</h2>
        <div class="forms-items">
            <a href="../index.php"><i class="fa-solid fa-user fa-sm"></i>               STUDENT</a>
            <a href="major.php"><i class="fa-solid fa-book fa-sm"></i>               MAJOR</a>
            <a href="department.php" class="active"><i class="fa-solid fa-building-columns fa-sm"></i>               DEPARTMENT</a>
            <a href="course.php"><i class="fa-solid fa-book-open-reader fa-sm"></i>               COURSE</a>
        </div>
        <button onclick="location.href='../reports/studentReport.php'" class="tabs"><i class="fa-regular fa-file-lines"></i>            Reports</button>
    </nav>

    <div class="contentPanel">
        <h1 class="defaultH1"><i class="fa-solid fa-building-columns"></i>                         Department Form</h1>

        <div class="form-header">
            <h3><i class="fa-solid fa-file-circle-plus" style="color: #F0F0EA;"></i>                ADD NEW DEPARTMENT</h3>
        </div>
        <div class="form-container">
            <form action="" method="post" autocomplete="off">
                <div class="form-group">
                    <label for="DepartmentID">Department ID :</label>
                    <input type="text" id="DepartmentID" placeholder="Enter department ID ..." name="DepartmentID" >
                </div>
                <div class="form-group">
                    <label for="DepartmentName">Department Name :</label>
                    <input type="text" id="DepartmentName" placeholder="Enter department name ..." name="DepartmentName" required>
                </div>
                <div class="form-group">
                    <label for="Location">Location :</label>
                    <input type="text" name="Location" placeholder="Enter location ..." required value="">
                </div>
                <button type="submit" class="submitBTN" name="submit">SUBMIT      <i class="fa-solid fa-arrow-up-right-from-square fa-sm"></i></button>
            </form>
        </div>
    </div>
    
</body>
</html>