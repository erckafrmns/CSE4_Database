<?php
require 'connection.php';

if (isset($_POST["submit"])){
    $CourseID = $_POST["CourseID"];
    $CourseName = $_POST["CourseName"];
    $Credits = $_POST["Credits"];

    $query = "INSERT INTO department VALUES ('$CourseID', '$CourseName', '$Credits')";
    mysqli_query($conn, $query);
    echo "<script> alert(''$CourseName' was Successfully Added!'); </script>";
}  

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Display and Form</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/b6ecc94894.js" crossorigin="anonymous"></script>
</head>
<body>

    <nav>
        <h2><i class="fa-brands fa-wpforms fa-sm" style="color: #ffffff; font-style: italic;"></i>                  FORMS</h2>
        <div class="forms-items">
            <a href="index.php"><i class="fa-solid fa-user fa-sm"></i>               STUDENT</a>
            <a href="major.php"><i class="fa-solid fa-book fa-sm"></i>               MAJOR</a>
            <a href="department.php"><i class="fa-solid fa-building-columns fa-sm"></i>               DEPARTMENT</a>
            <a href="course.php" class="active"><i class="fa-solid fa-book-open-reader fa-sm"></i>               COURSE</a>
        </div>
        <button onclick="location.href='reports.php'" class="tabs"><i class="fa-regular fa-file-lines"></i>            Reports</button>
    </nav>

    <div class="contentPanel">
        <h1><i class="fa-solid fa-book-open-reader"></i>                         Course Display and Form</h1>

        <div class="form-header">
            <h3><i class="fa-solid fa-file-circle-plus" style="color: #F0F0EA;"></i>                ADD NEW COURSE</h3>
        </div>
        <div class="form-container">
            <form action="" method="post" autocomplete="off">
                <label for="CourseID">Course ID :</label>
                <input type="text" id="CourseID" placeholder="Enter course ID ..." name="CourseID" >
                <label for="CourseName">Course Name :</label>
                <input type="text" id="CourseName" placeholder="Enter course name ..." name="CourseName" required>
                <label for="Credits">Credits :</label>
                <input type="text" name="Credits" placeholder="Enter credits ..." required value="">
                <button type="submit" class="submitBTN" name="submit">SUBMIT      <i class="fa-solid fa-arrow-up-right-from-square fa-sm"></i></button>
            </form>
        </div>
    </div>
    
</body>
</html>
